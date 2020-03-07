<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('leaderboard_model', '', TRUE);

        $this->resources = $this->game_model->get_all('resource');
        $this->terrains = $this->game_model->get_all('terrain');
        $this->unit_types = $this->game_model->get_all('unit_type');
        $this->supplies = $this->game_model->get_all('supply');
        $this->supplies_category_labels = [0,'Stats','Materials','Agriculture','Energy','Riches','Cash Crops','Metals','Knowledge','Light Industry','Heavy Industry'];
        $this->settlements = $this->game_model->get_all('settlement');
        $this->settlement_category_labels = [0, 'Township', 'Agriculture', 'Materials', 'Energy', 'Cash Crops'];
        $this->industries = $this->game_model->get_all('industry');
        $this->industry_category_labels = [0, 'Government', 'Merchandise', 'Energy', 'Light', 'Heavy', 'Tourism', 'Knowledge', 'Metro'];
        $this->unit_labels = [0, 'Infantry', 'Guerrilla', 'Commandos'];

        // Force ssl
        if (!is_dev()) {
            force_ssl();
        }
    }

    // Game view and update json
    public function index($world_slug = 1, $marketing_slug = false)
    {
        if (MAINTENANCE) {
            return $this->maintenance();
        }

        $this->user_model->record_marketing_hit($marketing_slug);

        $data['world'] = $this->game_model->get_world_by_slug($world_slug);
        if (!$data['world']) {
            return $this->load->view('errors/page_not_found', $data);
        }

        $data['account'] = $this->user_model->this_account($data['world']['id']);

        if ($this->input->get('json')) {
            $server_map_update_interval_s = (MAP_UPDATE_INTERVAL_MS / 1000) * 2;
            $data['tiles'] = $this->game_model->get_all_tiles_in_world_recently_updated($data['world']['id'], $server_map_update_interval_s);
        }
        else {
            $data['worlds'] = $this->game_model->get_all('world');
            $data['leaderboards'] = $this->leaderboards($data['world']['id']);
            $data['tiles'] = $this->game_model->get_all_tiles_in_world($data['world']['id']);
            $data['validation_errors'] = $this->session->flashdata('validation_errors');
            $data['failed_form'] = $this->session->flashdata('failed_form');
            $data['just_registered'] = $this->session->flashdata('just_registered');
        }

        if ($this->input->get('json')) {
            return api_response($data);
        }

        // Load view
        $this->load->view('header', $data);
        $this->load->view('menus', $data);
        $this->load->view('government', $data);
        $this->load->view('diplomacy', $data);
        $this->load->view('leaderboard', $data);
        $this->load->view('blocks', $data);
        $this->load->view('tile_block', $data);
        $this->load->view('trade_block', $data);
        $this->load->view('variables', $data);
        $this->load->view('shared', $data);
        $this->load->view('scripts/map_script', $data);
        $this->load->view('scripts/interface_script', $data);
        $this->load->view('scripts/render_script', $data);
        $this->load->view('scripts/tile_script', $data);
        $this->load->view('scripts/trade_script', $data);
        // $this->load->view('scripts/tutorial_script', $data);
        $this->load->view('scripts/chat_script', $data);
        $this->load->view('footer', $data);
    }

    // Get infomation on single land
    public function get_single_tile()
    {
        $world_key = $this->input->get('world_key');
        $lat = $this->input->get('lat');
        $lng = $this->input->get('lng');
        $tile = $this->game_model->get_single_tile($lat, $lng, $world_key);
        if (!$tile) {
            echo '{"error": "tile not found"}';
            return false;
        }
        $tile['account'] = $tile['account_key'] ? $this->user_model->get_account_by_id($tile['account_key']) : false;

        $tile['username'] = $tile['account'] ? $tile['account']['username'] : '';

        $account = $this->user_model->this_account($world_key);
        $tile['in_range'] = false;
        if ($account) {
            $world = $this->game_model->get_world_by_id($world_key);
            $account['tile_count'] = $this->game_model->get_count_of_account_tile($account['id']);
        }
        
        // Strip html entities from all untrusted columns, except content as it's stripped on insert
        $tile['tile_name'] = htmlspecialchars($tile['tile_name']);
        $tile['color'] = htmlspecialchars($tile['color']);
        $tile['username'] = htmlspecialchars($tile['username']);
        return api_response($tile);
    }

    public function get_this_full_account($world_key, $raw = false)
    {
        $account = $this->user_model->this_account($world_key);
        $account['supplies'] = array();
        $supplies = $this->game_model->get_account_supplies($account['id']);
        foreach ($supplies as $key => $supply) {
            $account['supplies'][$supply['slug']] = $supply;
        }
        if ($raw) {
            return $account;
        }
        return api_response($account);
    }

    public function laws_form()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('world_key', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('input_government', 'Form of Government', 'trim|required|integer|max_length[1]');
        $this->form_validation->set_rules('input_tax_rate', 'Tax Rate', 'trim|integer|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('input_ideology', 'Ideology', 'trim|integer|greater_than_equal_to[1]|less_than_equal_to[2]');

        $world_key = $this->input->post('world_key');
        $account = $this->user_model->this_account($world_key);

        // Fail
        if ($this->form_validation->run() == FALSE) {
            echo '{"error": "' + validation_errors() + '"}';
            return false;
        }
        $government = $this->input->post('input_government');
        $tax_rate = $this->input->post('input_tax_rate');
        $ideology = $this->input->post('input_ideology');

        // Set account
        $account_key = $account['id'];
        $this->game_model->update_account_laws($account_key, $government, $tax_rate, $ideology);

        // Success
        echo '{"status": "success", "result": true, "message": "Laws Updated"}';
    }

    public function leaderboards($world_id)
    {
        return;
    }

    public function maintenance()
    {
        // Send refresh signal to clients when true
        if ($this->input->get('json')) {
            $data['refresh'] = $this->maintenance_flag;
            echo json_encode($data);
        }
        else {
            echo '<h1>Landgrab is being updated. This will only take a minute or two. This page will refresh automatically.</h1>';
            echo '<script>window.setTimeout(function(){ window.location.href = "' . base_url() . '"; }, 5000);</script>';
        }
        return false;
    }

    public function do_first_claim()
    {
        $world_key = $this->input->post('world_key');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $tile = $this->game_model->get_single_tile($end_lat, $end_lng, $world_key);
        $account = $this->get_this_full_account($tile['world_key'], true);
        if (!$this->first_claim_validation($account, $tile)) {
            return false;
        }
        $this->game_model->first_claim($tile, $account);
        $this->game_model->increment_account_supply($account['id'], TILES_KEY);
        $this->game_model->increment_account_supply($account['id'], POPULATION_KEY);
    }

    public function first_claim_validation($account, $tile)
    {
        if (!$account) {
            return false;
        }
        if ($tile['terrain_key'] === OCEAN_KEY) {
            return false;
        }
        if ($account['supplies']['tiles']['amount'] > 0) {
            return false;
        }
        if ($this->game_model->tile_is_incorporated($tile['settlement_key'])) {
            return false;
        }
        return true;
    }

    public function can_claim($account, $tile)
    {
        if (!$account) {
            return false;
        }
        if ($tile['terrain_key'] === OCEAN_KEY) {
            return false;
        }
        if ($tile['account_key']) {
            return false;
        }
        return true;
    }

    function unit_move_to_land()
    {
        $world_key = $this->input->post('world_key');
        $start_lat = $this->input->post('start_lat');
        $start_lng = $this->input->post('start_lng');
        $end_lat = $this->input->post('end_lat');
        $end_lng = $this->input->post('end_lng');
        // Keep remove before add, makes dupe bugs less likely
        $tile = $this->game_model->get_single_tile($end_lat, $end_lng, $world_key);
        $previous_tile = $this->game_model->get_single_tile($start_lat, $start_lng, $world_key);
        $this->game_model->remove_unit_from_previous_tile($world_key, $previous_tile['lat'], $previous_tile['lng']);
        $account = $this->get_this_full_account($tile['world_key'], true);
        if ($this->can_claim($account, $tile)) {
            $this->game_model->claim($tile, $account, $previous_tile['unit_key']);
            $this->game_model->increment_account_supply($account['id'], TILES_KEY);
            $this->game_model->increment_account_supply($account['id'], POPULATION_KEY);
        }
    }

    public function tile_form()
    {
        $world_key = $this->input->post('world_key');
        $lat = $this->input->post('world_key');
        $lng = $this->input->post('world_key');
        $terrain_key = FERTILE_KEY;
        $terrain_key = BARREN_KEY;
        // $terrain_key = MOUNTAIN_KEY;
        // $terrain_key = TUNDRA_KEY;
        $terrain_key = COASTAL_KEY;
        $terrain_key = OCEAN_KEY;
        $this->game_model->update_tile_terrain($lng, $lat, $terrain_key);
    }

}