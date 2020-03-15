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
        $this->unit_labels = [0, 'Infantry', 'Tanks', 'Commandos'];

        // Force ssl
        if (!is_dev()) {
            force_ssl();
        }
    }

    // Game view
    public function index($world_slug = 1, $marketing_slug = false)
    {
        if (MAINTENANCE) {
            return $this->maintenance();
        }

        $this->user_model->record_slug_hit($marketing_slug);

        $data['world'] = $this->game_model->get_world_by_slug($world_slug);
        if (!$data['world']) {
            return $this->load->view('errors/page_not_found', $data);
        }

        $data['account'] = $this->user_model->this_account($data['world']['id']);

        $data['worlds'] = $this->game_model->get_all('world');
        $data['leaderboards'] = $this->leaderboards($data['world']['id']);
        $data['tiles'] = $this->game_model->get_all_tiles_in_world($data['world']['id']);
        $data['validation_errors'] = $this->session->flashdata('validation_errors');
        $data['failed_form'] = $this->session->flashdata('failed_form');
        $data['just_registered'] = $this->session->flashdata('just_registered');

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
        $this->load->view('scripts/shared_script', $data);
        $this->load->view('scripts/map_script', $data);
        $this->load->view('scripts/interface_script', $data);
        $this->load->view('scripts/render_tile_script', $data);
        $this->load->view('scripts/tile_script', $data);
        $this->load->view('scripts/trade_script', $data);
        $this->load->view('scripts/tutorial_script', $data);
        $this->load->view('scripts/chat_script', $data);
        $this->load->view('footer', $data);
    }

    public function update_world($world_key)
    {
        if (MAINTENANCE) {
            $data['refresh'] = $this->maintenance_flag;
            api_response($data);
        }

        $data['world'] = $this->game_model->get_world_by_id($world_key);
        if (!$data['world']) {
            api_error_response('world_not_found', 'World Not Found');
        }

        $data['account'] = $this->user_model->this_account($data['world']['id']);

        $server_map_update_interval_s = (MAP_UPDATE_INTERVAL_MS / 1000) * 2;
        $data['tiles'] = $this->game_model->get_all_tiles_in_world_recently_updated($data['world']['id'], $server_map_update_interval_s);
        api_response($data);
    }

    // Get infomation on single land
    public function get_tile()
    {
        $world_key = $this->input->post('world_key');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $tile = $this->game_model->get_tile($lat, $lng, $world_key);
        if (!$tile) {
            api_error_response('tile_not_found', 'Tile Not Found');
        }
        $tile['account'] = $tile['account_key'] ? $this->user_model->get_account_by_id($tile['account_key']) : false;

        $tile['username'] = $tile['account'] ? $tile['account']['username'] : '';

        $account = $this->user_model->this_account($world_key);
        if ($account) {
            $world = $this->game_model->get_world_by_id($world_key);
            $account['tile_count'] = $this->game_model->get_count_of_account_tile($account['id']);
        }
        
        // Strip html entities from all untrusted columns, except content as it's stripped on insert
        $tile['tile_name'] = htmlspecialchars($tile['tile_name']);
        $tile['color'] = htmlspecialchars($tile['color']);
        $tile['username'] = htmlspecialchars($tile['username']);
        api_response($tile);
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
        api_response($account);
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

        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }
        $government = $this->input->post('input_government');
        $tax_rate = $this->input->post('input_tax_rate');
        $ideology = $this->input->post('input_ideology');

        // Set account
        $account_key = $account['id'];
        $this->game_model->update_account_laws($account_key, $government, $tax_rate, $ideology);
        api_response();
    }
    public function leaderboards($world_id)
    {
        return;
    }

    public function maintenance()
    {
        echo '<h1>Landgrab is being updated. This will only take a minute or two. This page will refresh automatically.</h1>';
        echo '<script>window.setTimeout(function(){ window.location.href = "' . base_url() . '"; }, 5000);</script>';
    }

    public function do_first_claim()
    {
        $world_key = $this->input->post('world_key');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $tile = $this->game_model->get_tile($lat, $lng, $world_key);
        $account = $this->get_this_full_account($tile['world_key'], true);
        if (!$this->first_claim_validation($account, $tile)) {
            api_error_response('first_claim_validation', 'You can no longer claim this land. Please try a different tile.');
        }
        $this->game_model->first_claim($tile, $account);
        $this->game_model->increment_account_supply($account['id'], TILES_KEY);
        $this->game_model->increment_account_supply($account['id'], POPULATION_KEY, $this->settlements[TOWN_KEY]['base_population']);
        api_response();
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
        if ($this->game_model->tile_is_township($tile['settlement_key'])) {
            return false;
        }
        return true;
    }

    public function can_claim($account, $tile)
    {
        if (!$account) {
            return false;
        }
        if ((int)$tile['terrain_key'] === OCEAN_KEY) {
            return false;
        }
        if ($tile['account_key']) {
            return false;
        }
        return true;
    }

    public function can_move_to($account, $tile)
    {
        if ((int)$tile['terrain_key'] === OCEAN_KEY) {
            return true;
        }
        if ($tile['unit_key']) {
            api_error_response('tile_is_occupied', 'There is already a friendly unit on this tile');
        }
        return true;
    }

    public function unit_move_to_land()
    {
        $world_key = $this->input->post('world_key');
        $start_lat = $this->input->post('start_lat');
        $start_lng = $this->input->post('start_lng');
        $end_lat = $this->input->post('end_lat');
        $end_lng = $this->input->post('end_lng');
        $account = $this->user_model->this_account($world_key);
        $tile = $this->game_model->get_tile($end_lat, $end_lng, $world_key);
        $previous_tile = $this->game_model->get_tile($start_lat, $start_lng, $world_key);
        if (!$this->game_model->tiles_are_adjacent($tile['lat'], $tile['lng'], $previous_tile['lat'], $previous_tile['lng'])) {
            api_error_response('tiles_not_adjacent', 'Tiles Are Not Adjacent');
        }
        if ($previous_tile['unit_owner_key'] != $account['id']) {
            api_error_response('unit_does_not_belong_to_account', 'Unit Does Not Belong To Account');
        }
        // Keep remove before add, makes dupe bugs less likely
        $account = $this->get_this_full_account($tile['world_key'], true);
        if ($this->can_claim($account, $tile)) {
            $this->game_model->remove_unit_from_previous_tile($world_key, $previous_tile['lat'], $previous_tile['lng']);
            $this->game_model->claim($tile, $account, $previous_tile['unit_key']);
            $this->game_model->increment_account_supply($account['id'], TILES_KEY);
        }
        else if ($this->can_move_to($account, $tile)) {
            $this->game_model->remove_unit_from_previous_tile($world_key, $previous_tile['lat'], $previous_tile['lng']);
            $this->game_model->put_unit_on_tile($tile, $account, $previous_tile['unit_key']);
        }
        $data['tile'] = $tile;
        api_response($data);
    }

    public function update_tile_name()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('tile_id', 'tile_id', 'required');
        $this->form_validation->set_rules('tile_name', 'tile_name', 'trim|max_length[100]');
        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }
        $tile_id = $this->input->post('tile_id');
        $tile_name = $this->input->post('tile_name');
        $tile = $this->game_model->get_tile_by_id($tile_id);
        $account = $this->user_model->this_account($tile['world_key']);
        if ($account['id'] != $tile['account_key']) {
            api_error_response('auth', 'Tile is not yours');
        }
        $this->game_model->update_tile_name($tile_id, $tile_name);
        api_response();
    }

    public function update_tile_desc()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('tile_id', 'tile_id', 'required');
        $this->form_validation->set_rules('tile_desc', 'tile_desc', 'trim|max_length[1000]');
        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }
        $tile_id = $this->input->post('tile_id');
        $tile_desc = $this->input->post('tile_desc');
        $tile = $this->game_model->get_tile_by_id($tile_id);
        $account = $this->user_model->this_account($tile['world_key']);
        if ($account['id'] != $tile['account_key']) {
            return;
        }
        $this->game_model->update_tile_desc($tile_id, $tile_desc);
        api_response();
    }

    public function update_settlement()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('settlement_key', 'settlement_key', 'required');
        $this->form_validation->set_rules('tile_id', 'tile_id', 'required');
        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }
        $settlement_key = $this->input->post('settlement_key');
        $tile_id = $this->input->post('tile_id');
        $tile = $this->game_model->get_tile_by_id($tile_id);
        $account = $this->user_model->this_account($tile['world_key']);
        if ($account['id'] != $tile['account_key']) {
            api_error_response('auth', 'Tile is not yours');
        }
        $this->game_model->update_tile_settlement($tile_id, $settlement_key);
        api_response();
    }

    public function update_industry()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('industry_key', 'industry_key', 'required');
        $this->form_validation->set_rules('tile_id', 'tile_id', 'required');
        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }
        $industry_key = $this->input->post('industry_key');
        $tile_id = $this->input->post('tile_id');
        $tile = $this->game_model->get_tile_by_id($tile_id);
        $account = $this->user_model->this_account($tile['world_key']);
        if ($account['id'] != $tile['account_key']) {
            api_error_response('auth', 'Tile is not yours');
        }

        if ((int)$industry_key === CAPITOL_INDUSTRY_KEY) {
            $this->game_model->remove_capitol($account['id']);
        }
        $this->game_model->update_tile_industry($tile_id, $industry_key);
        api_response();
    }


    public function request_unit_spawn()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('unit_id', 'unit_id', 'required');
        $this->form_validation->set_rules('tile_id', 'tile_id', 'required');
        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }
        $unit_id = $this->input->post('unit_id');
        $tile_id = $this->input->post('tile_id');
        $tile = $this->game_model->get_tile_by_id($tile_id);
        if ($tile['unit_key']) {
            api_error_response('tile_is_occupied', 'There is already a friendly unit on this tile');
        }
        if (!(int)$tile['is_capitol'] && !(int)$tile['is_base']) {
            api_error_response('tile_is_not_allowed_to_spawn', 'This tile must be a capitol or base');
        }
        // @TODO
        // Validate money/support needed exists
        // Take money
        $account = $this->get_this_full_account($tile['world_key'], true);
        $this->game_model->put_unit_on_tile($tile, $account, $unit_id);
        api_response();
    }

    public function tile_form()
    {
        $world_key = $this->input->post('world_key');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $terrain_key = FERTILE_KEY;
        $terrain_key = BARREN_KEY;
        // $terrain_key = MOUNTAIN_KEY;
        // $terrain_key = TUNDRA_KEY;
        $terrain_key = COASTAL_KEY;
        // $terrain_key = OCEAN_KEY;
        $this->game_model->update_tile_terrain($world_key, $lng, $lat, $terrain_key);
        api_response();
    }

}