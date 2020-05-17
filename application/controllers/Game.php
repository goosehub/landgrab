<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('combat_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('leaderboard_model', '', TRUE);

        $this->resources = $this->game_model->get_all('resource');
        $this->terrains = $this->game_model->get_all('terrain');
        $this->unit_types = $this->game_model->get_all('unit_type');
        $this->supplies = $this->game_model->get_all('supply', 'category_id');
        $this->market_prices = $this->game_model->get_all('market_price');
        $this->supplies_category_labels = [0,'Stats','Food','Cash Crops','Energy','Metals','Materials','Services','Goods','GDP Bonuses','Riches'];
        $this->settlements = $this->game_model->get_all('settlement');
        $this->settlement_category_labels = [0, 'Township', 'Agriculture', 'Materials', 'Energy', 'Cash Crops'];
        $this->industries = $this->game_model->get_all('industry', 'category_id');
        $this->industry_category_labels = [0, 'Government', 'Energy', 'Tourism', 'Knowledge', 'Light', 'Heavy', 'Metro'];
        $this->unit_labels = [0, 'Infantry', 'Tanks', 'Airforce'];
        $this->treaties = [0, 'War', 'Peace', 'Passage'];

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
        if ($data['account']) {
            $data['account']['treaties'] = $this->game_model->treaties_by_account($data['account']['id']);
        }

        $data['worlds'] = $this->game_model->get_all('world');
        $data['leaderboards'] = $this->leaderboards($data['world']['id']);
        $data['active_accounts'] = $this->user_model->get_active_accounts_in_world($data['world']['id']);
        $data['tiles'] = $this->game_model->get_all_tiles_in_world($data['world']['id']);
        $data['validation_errors'] = $this->session->flashdata('validation_errors');
        $data['failed_form'] = $this->session->flashdata('failed_form');
        $data['just_registered'] = $this->session->flashdata('just_registered');

        $this->settlements = $this->game_model->merge_settlement_and_supply($this->settlements, $this->supplies);
        $this->industries = $this->game_model->merge_industry_and_supply($this->industries, $this->supplies);

        // Load view
        $this->load->view('header', $data);
        $this->load->view('menus', $data);
        $this->load->view('government', $data);
        $this->load->view('diplomacy', $data);
        $this->load->view('leaderboard', $data);
        $this->load->view('blocks', $data);
        $this->load->view('tile_block', $data);
        $this->load->view('new_trade_block', $data);
        $this->load->view('view_trade_block', $data);
        $this->load->view('scripts/shared_script', $data);
        $this->load->view('scripts/variables', $data);
        $this->load->view('scripts/interface_script', $data);
        $this->load->view('scripts/render_tile_script', $data);
        $this->load->view('scripts/tile_script', $data);
        $this->load->view('scripts/trade_script', $data);
        $this->load->view('scripts/unit_script', $data);
        $this->load->view('scripts/marker_script', $data);
        $this->load->view('scripts/tutorial_script', $data);
        $this->load->view('scripts/map_script', $data);
        $this->load->view('scripts/chat_script', $data);
        $this->load->view('scripts/account_update_script', $data);
        $this->load->view('scripts/market_script', $data);
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

    public function update_units($world_key)
    {
        $data['world'] = $this->game_model->get_world_by_id($world_key);
        if (!$data['world']) {
            api_error_response('world_not_found', 'World Not Found');
        }

        $data['account'] = $this->user_model->this_account($data['world']['id']);

        $data['tiles'] = $this->game_model->get_all_tiles_in_world_with_units($data['world']['id']);
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

        $unit_account = $tile['unit_owner_key'] ? $this->user_model->get_account_by_id($tile['unit_owner_key']) : false;
        $tile['unit_owner_username'] = $unit_account ? $unit_account['username'] : '';

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

    public function get_user_full_account($world_key)
    {
        $account = $this->user_model->this_account($world_key);
        $account['treaties'] = $this->game_model->treaties_by_account($account['id']);
        $account['sent_trades'] = $this->game_model->sent_trades($account['id']);
        $account['received_trades'] = $this->game_model->received_trades($account['id']);
        $account['supplies'] = array();
        $supplies = $this->game_model->get_account_supplies($account['id']);
        $account['input_projections'] = $this->game_model->get_input_projections($account['id']);
        $account['output_projections'] = $this->game_model->get_output_projections($account['id']);
        foreach ($supplies as $key => $supply) {
            $account['supplies'][$supply['slug']] = $supply;
        }
        $account['budget'] = $this->game_model->get_account_budget($account);
        $account['market_prices'] = $this->market_prices;
        api_response($account);
    }

    public function get_this_full_account($world_key)
    {
        $account = $this->user_model->this_account($world_key);
        $account['supplies'] = array();
        $supplies = $this->game_model->get_account_supplies($account['id']);
        foreach ($supplies as $key => $supply) {
            $account['supplies'][$supply['slug']] = $supply;
        }
        $account['budget'] = $this->game_model->get_account_budget($account);
        return $account;
    }

    public function get_account_with_supplies($account_key, $raw = false)
    {
        $account = $this->user_model->get_account_by_id($account_key);
        $account['supplies'] = array();
        $supplies = $this->game_model->get_account_supplies($account['id']);
        foreach ($supplies as $key => $supply) {
            $account['supplies'][$supply['slug']] = $supply;
        }
        if ($raw) {
            return $account;
        }
        $account['budget'] = $this->game_model->get_account_budget($account);
        api_response($account);
    }

    public function laws_form()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('world_key', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('input_power_structure', 'Power Structure', 'trim|required|integer|max_length[1]');
        $this->form_validation->set_rules('input_tax_rate', 'Tax Rate', 'trim|integer|greater_than_equal_to[0]|less_than_equal_to[' . MAX_TAX_RATE . ']|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('input_ideology', 'Ideology', 'trim|integer|greater_than_equal_to[1]|less_than_equal_to[2]');

        $world_key = $this->input->post('world_key');
        $account = $this->user_model->this_account($world_key);

        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }

        if (strtotime($account['last_law_change']) > time() - (60 * 60)) {
            api_error_response('law_change_too_soon', 'You must wait an hour between passing laws.');
        }

        $power_structure = $this->input->post('input_power_structure');
        $tax_rate = $this->input->post('input_tax_rate');
        $ideology = $this->input->post('input_ideology');

        // Set account
        $account_key = $account['id'];
        $this->game_model->update_account_laws($account_key, $power_structure, $tax_rate, $ideology);
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
        $account = $this->get_this_full_account($tile['world_key']);
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
        if ($tile['account_key'] == $account['id']) {
            return false;
        }
        if ($tile['account_key']) {
            $treaty = $this->game_model->find_existing_treaty($account['id'], $tile['account_key']);
            if (!$treaty || $treaty['treaty_key'] != WAR_KEY) {
                api_error_response('attack_requires_war', 'You must declare war before attacking this nation');
            }
        }
        return true;
    }

    public function can_move_to($account, $tile)
    {
        if ((int)$tile['terrain_key'] === OCEAN_KEY) {
            return true;
        }

        if ((int)$account['supplies']['support']['amount'] <= 0) {
            api_error_response('not_enough_support_to_move', 'You can not move units without political support');
        }

        if ($tile['unit_key']) {
            $treaty = $this->game_model->find_existing_treaty($account['id'], $tile['account_key']);
            if (!$treaty || $treaty['treaty_key'] != WAR_KEY) {
                api_error_response('tile_is_occupied', 'There is already a friendly unit on this tile');
            }
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
        $account = $this->get_this_full_account($world_key);
        $tile = $this->game_model->get_tile($end_lat, $end_lng, $world_key);
        $previous_tile = $this->game_model->get_tile($start_lat, $start_lng, $world_key);
        if (!$this->game_model->tiles_are_adjacent($tile['lat'], $tile['lng'], $previous_tile['lat'], $previous_tile['lng'])) {
            api_error_response('tiles_not_adjacent', 'Tiles Are Not Adjacent');
        }
        if ($previous_tile['unit_owner_key'] != $account['id']) {
            api_error_response('unit_does_not_belong_to_account', 'Unit Does Not Belong To Account');
        }
        // Keep remove before add, makes dupe bugs less likely
        if ($this->can_claim($account, $tile)) {
            $this->game_model->remove_unit_from_previous_tile($world_key, $previous_tile['lat'], $previous_tile['lng']);
            $data['combat'] = $this->combat_model->combat($account, $tile, $previous_tile);
            if (!$data['combat'] || $data['combat']['victory']) {
                $this->game_model->decrement_account_supply($account['id'], SUPPORT_KEY, SUPPORT_COST_CAPTURE_LAND);
                $this->game_model->claim($tile, $account, $previous_tile['unit_key']);
                $this->game_model->increment_account_supply($account['id'], TILES_KEY);
            }
        }
        else if ($this->can_move_to($account, $tile)) {
            $this->game_model->remove_unit_from_previous_tile($world_key, $previous_tile['lat'], $previous_tile['lng']);
            $data['combat'] = $this->combat_model->combat($account, $tile, $previous_tile);
            if (!$data['combat'] || $data['combat']['victory']) {
                $this->game_model->decrement_account_supply($account['id'], SUPPORT_KEY, SUPPORT_COST_MOVE_UNIT);
                $this->game_model->put_unit_on_tile($tile, $account, $previous_tile['unit_key']);
            }
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
        $settlement = $this->game_model->get_settlement_from_state($settlement_key);
        if ($account['id'] != $tile['account_key']) {
            api_error_response('auth', 'Tile is not yours');
        }
        if ($tile['terrain_key'] == OCEAN_KEY) {
            api_error_response('ocean_terrain_not_allowed', 'Not the correct terrain type');
        }
        if (!$this->game_model->settlement_allowed_on_terrain($tile['terrain_key'], $settlement)) {
            api_error_response('terrain_not_allowed', 'Not the correct terrain type');
        }
        if (!$this->game_model->tile_is_township($tile['settlement_key']) && ($settlement_key == CITY_KEY || $settlement_key == METRO_KEY)) {
            api_error_response('township_upgrade_not_allowed', 'You must be a township to make this upgrade');
        }
        if ($this->game_model->tile_is_township($tile['settlement_key']) && $tile['population'] < $settlement['base_population']) {
            api_error_response('popuation_insufficient', 'Popuation insufficient to upgrade township');
        }
        if ($this->game_model->tile_is_township($tile['settlement_key']) && $settlement_key < $tile['settlement_key']) {
            $this->game_model->update_tile_industry($tile_id, null);
        }
        $this->game_model->update_tile_settlement($tile_id, $settlement_key);
        $tile = $this->game_model->get_tile_by_id($tile_id);
        api_response($tile);
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
        $industry = $this->game_model->get_industry_from_state($industry_key);
        if ($account['id'] != $tile['account_key']) {
            api_error_response('auth', 'Tile is not yours');
        }
        if ($tile['terrain_key'] == OCEAN_KEY) {
            api_error_response('ocean_terrain_not_allowed', 'Not the correct terrain type');
        }
        if (!$this->game_model->tile_is_township($tile['settlement_key'])) {
            api_error_response('tile_must_be_township', 'Tile must be township');
        }
        if ($industry['required_terrain_key'] && $industry['required_terrain_key'] != $tile['terrain_key']) {
            api_error_response('terrain_not_allowed', 'Not the correct terrain type');
        }
        if ($industry['minimum_settlement_size'] && $industry['minimum_settlement_size'] > $tile['settlement_key']) {
            api_error_response('township_too_small', 'Township must be larger');
        }
        if ((int)$industry_key === CAPITOL_INDUSTRY_KEY) {
            $this->game_model->remove_capitol($account['id']);
        }
        $tile = $this->game_model->get_tile_by_id($tile_id);
        $this->game_model->update_tile_industry($tile_id, $industry_key);
        api_response($tile);
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
        $account = $this->get_this_full_account($tile['world_key']);
        if ($account['ideology'] == FREE_MARKET_KEY && $account['supplies']['cash'] < $this->unit_types[$unit_id - 1]['cash_cost']) {
            api_error_response('insufficient_cash', 'You do not have enough cash to complete this action');
        }
        if ($account['ideology'] == SOCIALISM_KEY && $account['supplies']['support'] < $this->unit_types[$unit_id - 1]['support_cost']) {
            api_error_response('insufficient_cash', 'You do not have enough cash to complete this action');
        }
        if ($account['ideology'] == FREE_MARKET_KEY) {
            $this->game_model->decrement_account_supply($account['id'], CASH_KEY, $this->unit_types[$unit_id - 1]['cash_cost']);
        }
        else if ($account['ideology'] == SOCIALISM_KEY) {
            $this->game_model->decrement_account_supply($account['id'], SUPPORT_KEY, $this->unit_types[$unit_id - 1]['support_cost']);
        }
        else {
            api_error_response('must_have_ideology', 'You must have an ideology to complete this action');
        }
        $this->game_model->put_unit_on_tile($tile, $account, $unit_id);
        api_response();
    }

    public function get_trade_request($world_key, $trade_request_key, $trade_partner_key)
    {
        $account = $this->user_model->this_account($world_key);
        if (!$account) {
            api_error_response('auth', 'You must be logged in');
        }
        $data['trade_request'] = $this->game_model->get_trade_request($trade_request_key);
        if (!$data['trade_request']) {
            api_error_response('trade_request_does_not_exist', 'This trade request does not exist');
        }
        if ($account['id'] != $data['trade_request']['request_account_key'] && $account['id'] != $data['trade_request']['receive_account_key']) {
            api_error_response('no_access_to_trade_request', 'You do not have access to view this trade request');
        }
        if ($trade_partner_key == $data['trade_request']['request_account_key']) {
            $this->game_model->mark_trade_request_request_seen($trade_request_key);
        }
        else {
            $this->game_model->mark_trade_request_response_seen($trade_request_key);
        }
        if (!$data['trade_request']) {
            api_error_response('trade_request_not_found', 'Trade request not found');
        }
        $data['request_supplies'] = $this->game_model->get_supply_account_trade_lookup($trade_request_key, $data['trade_request']['receive_account_key']);
        $data['receive_supplies'] = $this->game_model->get_supply_account_trade_lookup($trade_request_key, $data['trade_request']['request_account_key']);
        $data['trade_partner'] = $this->get_account_with_supplies($trade_partner_key, $raw = true);
        api_response($data);
    }

    public function declare_war()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trade_partner_key', 'trade_partner_key', 'required');
        $this->form_validation->set_rules('message', 'message', 'max_length[1000]');
        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }
        $trade_partner_key = $this->input->post('trade_partner_key');
        $world_key = $this->input->post('world_key');
        $message = $this->input->post('message');
        $account = $this->get_this_full_account($world_key);
        if (!$account) {
            api_error_response('auth', 'You must be logged in');
        }
        if ((int)$account['supplies']['tiles']['amount'] <= 0) {
            api_error_response('not_enough_tiles', 'You must have land to engage in diplomacy');
        }
        if ((int)$account['supplies']['support']['amount'] < SUPPORT_COST_DECLARE_WAR) {
            api_error_response('not_enough_support_to_move', 'You can not declare war without at least ' . SUPPORT_COST_DECLARE_WAR . ' political support');
        }
        $treaty_key = false;
        $this->game_model->decrement_account_supply($account['id'], SUPPORT_KEY, SUPPORT_COST_DECLARE_WAR);
        $existing_treaty = $this->game_model->find_existing_treaty($account['id'], $trade_partner_key);
        if ($existing_treaty) {
            $treaty_key = $existing_treaty['id'];
            $this->game_model->update_treaty($existing_treaty['id'], WAR_KEY);
        }
        else {
            $this->game_model->create_treaty($account['id'], $trade_partner_key, WAR_KEY);
        }
        $this->game_model->create_trade_request($account['id'], $trade_partner_key, $message, WAR_KEY);
        api_response();
    }

    public function send_trade_request()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trade_partner_key', 'trade_partner_key', 'required');
        $this->form_validation->set_rules('message', 'message', 'max_length[1000]');
        if ($this->form_validation->run() == FALSE) {
            api_error_response('validation', trim(strip_tags(validation_errors())));
        }
        $trade_partner_key = $this->input->post('trade_partner_key');
        $world_key = $this->input->post('world_key');
        $message = $this->input->post('message');
        $treaty_key = $this->input->post('treaty');
        $supplies_offered = json_decode($this->input->post('supplies_offered'));
        $supplies_demanded = json_decode($this->input->post('supplies_demanded'));

        $account = $this->get_this_full_account($world_key);
        if (!$account) {
            api_error_response('auth', 'You must be logged in');
        }
        if ((int)$account['supplies']['tiles']['amount'] <= 0) {
            api_error_response('not_enough_tiles', 'You must have land to engage in diplomacy');
        }
        $trade_partner = $this->user_model->get_account_by_id($trade_partner_key);
        if (!$account) {
            api_error_response('partner_does_not_exist', 'This trade partner does not exist');
        }
        if ($trade_partner['world_key'] != $world_key) {
            api_error_response('partner_is_in_different_world', 'This trade partner does not exist in the same world');
        }
        if ($this->game_model->open_trade_request_between_accounts_exist($account['id'], $trade_partner_key)) {
            api_error_response('active_trade_request_exists', 'You already have sent an active trade request to this player');
        }
        if (!$this->game_model->sufficient_supplies_to_send_trade_request($supplies_offered, $account['id'])) {
            api_error_response('trade_request_requires_more_supplies', 'Trade request requires more supplies');
        }
        $this->game_model->create_trade_main($account['id'], $trade_partner_key, $message, $treaty_key, $supplies_offered, $supplies_demanded);
        api_response();
    }

    public function accept_trade_request($trade_request_key)
    {
        $world_key = $this->input->post('world_key');
        $response_message = $this->input->post('response_message');
        $account = $this->user_model->this_account($world_key);
        if (!$account) {
            api_error_response('auth', 'You must be logged in');
        }
        $trade_request = $this->game_model->get_trade_request($trade_request_key);
        if (!$trade_request) {
            api_error_response('trade_request_not_found', 'This trade request is not found');
        }
        if ($trade_request['receive_account_key'] != $account['id']) {
            api_error_response('auth', 'Trade request not intended for you');
        }
        if ($trade_request['is_accepted'] || $trade_request['is_rejected'] || $trade_request['is_declared']) {
            api_error_response('trade_request_already_complete', 'Trade request is already complete');
        }
        if (!$this->game_model->sufficient_supplies_to_accept_trade_request($trade_request['id'], $account['id'])) {
            api_error_response('trade_request_requires_more_supplies', 'Trade request requires more supplies');
        }
        $this->game_model->accept_trade_request($trade_request_key, $trade_request['receive_account_key'], $trade_request['request_account_key'], $response_message);
        $existing_treaty = $this->game_model->find_existing_treaty($account['id'], $trade_request['request_account_key']);
        if ($existing_treaty) {
            $treaty_key = $existing_treaty['id'];
            $this->game_model->update_treaty($existing_treaty['id'], $trade_request['treaty_key']);
        }
        else {
            $this->game_model->create_treaty($account['id'], $trade_partner_key, $trade_request['treaty_key']);
        }
        api_response();
    }

    public function reject_trade_request($trade_request_key)
    {
        $world_key = $this->input->post('world_key');
        $response_message = $this->input->post('response_message');
        $account = $this->user_model->this_account($world_key);
        if (!$account) {
            api_error_response('auth', 'You must be logged in');
        }
        $trade_request = $this->game_model->get_trade_request($trade_request_key);
        if (!$trade_request) {
            api_error_response('trade_request_not_found', 'This trade request is not found');
        }
        if ($trade_request['receive_account_key'] != $account['id']) {
            api_error_response('auth', 'Trade request not intended for you');
        }
        if ($trade_request['is_accepted'] || $trade_request['is_rejected'] || $trade_request['is_declared']) {
            api_error_response('trade_request_already_complete', 'Trade request is already complete');
        }
        $this->game_model->reject_trade_request($trade_request_key, $trade_request['request_account_key'], $response_message);
        api_response();
    }

    public function sell()
    {
        $world_key = $this->input->post('world_key');
        $supply_key = $this->input->post('supply_key');
        $account = $this->user_model->this_account($world_key);
        $supply = $this->game_model->get_supply_by_account($account['id'], $supply_key);
        $market_price = $this->game_model->get_market_price_by_supply_key($supply_key);
        if (!$market_price) {
            api_error_response('not_sellable', 'You can\'t sell this supply');
        }
        if ($supply['amount'] < 1) {
            api_error_response('insufficient_supply', 'You don\'t have enough to sell');
        }
        $this->game_model->sell_supply_remove_supply($account['id'], $supply_key);
        $this->game_model->sell_supply_add_cash($account['id'], $market_price['amount']);
        api_response();
    }

    public function tile_form()
    {
        if (!ALLOW_TERRAIN_UPDATE) {
            return;
        }
        $world_key = $this->input->post('world_key');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        // $terrain_key = FERTILE_KEY;
        // $terrain_key = BARREN_KEY;
        // $terrain_key = MOUNTAIN_KEY;
        // $terrain_key = TUNDRA_KEY;
        $terrain_key = COASTAL_KEY;
        // $terrain_key = OCEAN_KEY;
        $this->game_model->update_tile_terrain($world_key, $lng, $lat, $terrain_key);
        api_response();
    }

}