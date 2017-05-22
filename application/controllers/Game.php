<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {

    // Land Key Constants
    protected $unclaimed_key = 1;
    protected $village_key = 2;
    protected $town_key = 3;
    protected $city_key = 4;
    protected $metropolis_key = 5;
    protected $fortification_key = 6;
    protected $capitol_key = 10;
    protected $embassy_key = 17;

    // Government Key Constants
    protected $democracy_key = 1;
    protected $oligarchy_key = 2;
    protected $autocracy_key = 3;

    // Balance Constants
    protected $democracy_tax_weariness_nerf = 3;
    protected $democracy_corruption_rate = 5;
    protected $oligarchy_tax_weariness_nerf = 2;
    protected $oligarchy_corruption_rate = 20;
    protected $autocracy_tax_weariness_nerf = 0;
    protected $autocracy_corruption_rate = 50;
    protected $weariness_increase_land_count = 300;
    protected $tax_weariness_nerf = 2;
    protected $sniper_land_minimum = 100;
    protected $entitlments_nerf = 40;
    protected $base_support = 100;
    protected $building_minimum = 0;
    protected $ww_nerf = 10;
    protected $defence_bonus = 1.5;
    protected $attack_bonus = 1.5;

    // Shared data
    protected $effects;

    // Files
    protected $leaderboard_filepath = 'json/leaderboard_';
    
    // Server Pooling Constants
    protected $leaderboard_update_interval_minutes = 5;
    protected $map_update_interval = 60;
    protected $maintenance_flag = false;

	function __construct() {
	    parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
	    $this->load->model('leaderboard_model', '', TRUE);

        // Force ssl
        if (!is_dev()) {
            force_ssl();
        }

        $this->effects = $data['modify_effect_dictionary'] = $this->game_model->get_all_modify_effects();
	}

	// Game view and update json
	public function index($world_slug = 1, $marketing_slug = false)
	{
        // Send refresh signal to clients when true
        if ($this->maintenance_flag) {
            if (isset($_GET['json'])) {
                $data['refresh'] = $this->maintenance_flag;
                echo json_encode($data);
            }
            else {
                echo '<h1>Landgrab is being updated. This will only take a minute or two. This page will refresh automatically.</h1>';
                echo '<script>window.setTimeout(function(){ window.location.href = "' . base_url() . '"; }, 5000);</script>';
            }
            return false;
        }

        // Record marketing slug into analytics table
        if ($marketing_slug) {
            $this->user_model->record_marketing_hit($marketing_slug);
        }

        // Authentication
        $log_check = $data['log_check'] = $data['user_id'] = false;
        if ($this->session->userdata('logged_in')) {
            $log_check = $data['log_check'] = true;
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
            $data['user'] = $this->user_model->get_user($user_id);
            if (! isset($data['user']['username']) ) {
                redirect('user/logout', 'refresh');
                return false;
            }
        }

        // Get world
        $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world_slug);

        // Return 404 if world not found
        if (!$world) {
            $this->load->view('errors/page_not_found', $data);
            return false;
        }

        // If logged in, get full account information
        if ($log_check) {
            $account = $this->user_model->get_account_by_keys($user_id, $world['id']);
            if (!$account) {
                echo 'There was an issue loading your account. Please log out by following <a href="' . base_url() . 'user/logout">This Link</a>. Afterwards, please report this bug to goosepostbox@gmail.com';
                exit();
            }
            $data['account'] = $this->get_full_account($account);
        }

        // Get last winner
        if (!isset($_GET['json'])) {
            $data['last_winner_account'] = $this->user_model->get_account_by_id($world['last_winner_account_key']);
            if (!$data['last_winner_account']) {
                $data['last_winner_account']['username'] = 'No Winner';
                $data['last_winner_account']['leader_portrait'] = 'default_leader_portrait.png';
                $data['last_winner_account']['nation_flag'] = 'default_nation_flag.png';
            } 
            else {
                $data['last_winner_account'] = $this->get_full_account($data['last_winner_account']);
            }
        }

        $next_reset_dictionary = $this->next_reset_dictionary();
        $data['next_reset'] = $next_reset_dictionary[$world['id']];

        // Set public constants
        $data['democracy_corruption_rate'] = $this->democracy_corruption_rate;
        $data['oligarchy_corruption_rate'] = $this->oligarchy_corruption_rate;
        $data['autocracy_corruption_rate'] = $this->autocracy_corruption_rate;
        $data['weariness_increase_land_count'] = $this->weariness_increase_land_count;
        $data['sniper_land_minimum'] = $this->sniper_land_minimum;
        $data['building_minimum'] = $this->building_minimum;


        // Get world leaderboards
        if (!isset($_GET['json'])) {
            $data['leaderboards'] = $this->leaderboards($world);
        }

        // Get all worlds
        if (!isset($_GET['json'])) {
            $data['worlds'] = $this->user_model->get_all_worlds();
        }

        // Get dictionaries
        if (!isset($_GET['json'])) {
            $data['government_dictionary'] = $this->government_dictionary();
            $data['stroke_color_dictionary'] = $this->stroke_color_dictionary();
            $data['land_type_key_dictionary'] = $this->land_type_key_dictionary();
            $data['modify_effect_dictionary'] = $this->effects;
            $data['embassy_effect'] = $this->game_model->get_embassy_effect();
        }

        // Get all lands
        $data['leaderboard_update_interval_minutes'] = $this->leaderboard_update_interval_minutes;
        $data['update_timespan'] = $this->map_update_interval * 1000;
        $server_update_timespan = $this->map_update_interval * 2;
        if (isset($_GET['json'])) {
            $data['lands'] = $this->game_model->get_all_lands_in_world_recently_updated($world['id'], $server_update_timespan);
        }
        else {
            $data['lands'] = $this->game_model->get_all_lands_in_world($world['id']);
        }

        // Validation errors
        $data['validation_errors'] = $this->session->flashdata('validation_errors');
        $data['failed_form'] = $this->session->flashdata('failed_form');
        $data['just_registered'] = $this->session->flashdata('just_registered');

        // If data request, encode data in json and deliver
        if (isset($_GET['json'])) {
            // Encode and send data
            function filter(&$value) {
              $value = nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            }
            array_walk_recursive($data, "filter");
            echo json_encode($data);
            return true;
        }

        // Load view
        $this->load->view('header', $data);
        $this->load->view('menus', $data);
        $this->load->view('budget', $data);
        $this->load->view('leaderboard', $data);
        $this->load->view('blocks', $data);
        $this->load->view('land_block', $data);
        $this->load->view('map_script', $data);
        $this->load->view('interface_script', $data);
        $this->load->view('tutorial_script', $data);
        $this->load->view('chat_script', $data);
        $this->load->view('footer', $data);
	}

    public function get_full_account($account)
    {
        $account['stats'] = $this->game_model->get_sum_effects_for_account($account['id']);

        // Democracy Taxes
        $account['stats']['corruption_rate'] = 100;
        if ((int)$account['government'] === $this->democracy_key) {
            $account['stats']['corruption_rate'] = $this->democracy_corruption_rate;
            $tax_weariness = $this->tax_weariness_calculate($account['tax_rate'], $this->democracy_tax_weariness_nerf);
        }
        // Oligarchy Taxes
        else if ((int)$account['government'] === $this->oligarchy_key) {
            $account['stats']['corruption_rate'] = $this->oligarchy_corruption_rate;
            $tax_weariness = $this->tax_weariness_calculate($account['tax_rate'], $this->oligarchy_tax_weariness_nerf);
        }
        // Autocracy Taxes
        else if ((int)$account['government'] === $this->autocracy_key) {
            $account['stats']['corruption_rate'] = $this->autocracy_corruption_rate;
            $tax_weariness = $this->autocracy_tax_weariness_nerf;
        }

        // Calculated Stats
        $account['stats']['tax_income_total'] = $this->get_percent_of($account['stats']['gdp'], $account['tax_rate']);
        $account['stats']['corruption_total'] = $this->get_percent_of($account['stats']['tax_income_total'], $account['stats']['corruption_rate']);
        $account['stats']['effective_tax_rate'] = $account['tax_rate'] - $account['stats']['corruption_rate'];
        $account['stats']['tax_income'] = $account['stats']['tax_income_total'] - $account['stats']['corruption_total'];
        $account['stats']['military_spending'] = $this->get_percent_of($account['stats']['tax_income'], $account['military_budget']);
        $account['stats']['military_total'] = $account['stats']['military_spending'] + $account['stats']['military'];
        $account['stats']['entitlements'] = $this->get_percent_of($account['stats']['tax_income'], $account['entitlements_budget']);
        $account['stats']['treasury_after'] = $account['stats']['tax_income'] - $account['stats']['military_spending'] - $account['stats']['entitlements'] + $account['stats']['treasury'];
        $account['stats']['entitlements_effect'] = $this->entitlements_effect_calculate($account['stats']['effective_tax_rate'] * $account['entitlements_budget'], $this->entitlments_nerf);
        $account['stats']['support'] = $this->base_support - $account['weariness'] - $tax_weariness + $account['stats']['entitlements_effect'] + $account['stats']['support'];
        $account['stats']['weariness'] = $account['weariness'];
        $account['stats']['building_maintenance'] = abs($account['stats']['treasury']);

        // See if functioning
        $account['functioning'] = true;
        if ($account['stats']['support'] < 0) {
            $account['functioning'] = false;
        }

        // Record account as loaded
        $this->user_model->account_loaded($account['id']);

        // Return account
        return $account;
    }

	// Get infomation on single land
	public function get_single_land($coord_slug = false, $world_key = false)
	{
        // Get input
        if ($coord_slug && $world_key) {
            $json_output = false;
        }
        else if (isset($_GET['coord_slug']) && isset($_GET['world_key']) ) {
            $json_output = true;
            $coord_slug = $_GET['coord_slug'];
            $world_key = $_GET['world_key'];
        }
        else {
            echo '{"error": "Input missing"}';
            return false;
        }

	    // Get Land Square
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
        $land_square['sum_effects'] = $this->game_model->get_sum_effects_of_land($land_square['id']);
        $land_square['sum_modifiers'] = $this->game_model->get_sum_modifiers_for_land($land_square['id']);
        $land_square['embassy_list'] = false;
        if ($land_square['capitol']) {
            $land_square['embassy_list'] = $this->game_model->get_embassys_of_land($land_square['id']);
        }
        $account = false;
        if ($land_square['account_key'] != 0) {
            $account = $land_square['account'] = $this->user_model->get_account_by_id($land_square['account_key']);
            // This shouldn't happen, but it does
            // This is a workaround to ensure issue is not noticed
            if (!$account) {
                // Unclaim land
                $this->game_model->update_land_data($land_square['id'], 0, '', '', 1, '#000000');
                $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
                $land_square['sum_effects'] = $this->game_model->get_sum_effects_of_land($land_square['id']);
                $land_square['sum_modifiers'] = $this->game_model->get_sum_modifiers_for_land($land_square['id']);
                $this->game_model->update_land_capitol_status($land_square['id'], $capitol = 0);
            } 
            else {
                $account = $land_square['account'] = $this->get_full_account($account);
            }
        }

        // Weariness
        $land_square['weariness'] = 0;
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $session_data['id'];
            $requester_account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $requester_account = $this->get_full_account($requester_account);
            $land_square['weariness'] = $this->weariness_calculate($requester_account, $land_square, $account);
            $land_square['valid_upgrades'] = $this->account_valid_upgrades($requester_account['id']);
        }

        // Land range false by default
        $land_square['in_range'] = false;

        // Add username to array
        $land_square['username'] = '';
        if ($account) {
            $land_square['username'] = $account['username'];
        }

        // Get account
        $log_check = false;
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $session_data['id'];
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $world = $this->game_model->get_world_by_slug_or_id($world_key);
            $account['land_count'] = $this->game_model->get_count_of_account_land($account['id']);
            $log_check = true;
            // Check if land is in range
            $land_square['in_range'] = $this->check_if_land_is_in_range($world_key, $account['id'], $account['land_count'], 
                $world['land_size'], $land_square['lat'], $land_square['lng'], false);
        }

        // If none found, default to this
        if (!isset($land_square['land_name'])) {
            echo '{"error": "Land not found"}';
            return false;
        }
        
        // Strip html entities from all untrusted columns, except content as it's stripped on insert
        $land_square['land_name'] = htmlspecialchars($land_square['land_name']);
        $land_square['color'] = htmlspecialchars($land_square['color']);
        $land_square['username'] = htmlspecialchars($land_square['username']);
        if ($json_output) {
            // Filter tags except img with src only
            function filter(&$value) {
                // $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                // $value = strip_tags($value, '<img>');
                // $value = preg_replace('#&lt;(/?(?:img))&gt;#', '<\1>', $value);
                // $value = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\ssrc=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i",'<$1$2$3>', $value);
                $value = strip_tags($value);
                $value = nl2br($value);
            }
            array_walk_recursive($land_square, "filter");
            echo json_encode($land_square);
        }
        else {
            return $land_square;
        }
	}

    // Find which land upgrades are valid
    public function account_valid_upgrades($account_key)
    {
        // Get counts of each land type
        $land_type_counts = $this->game_model->count_lands_of_type_by_account($account_key);
        $village_counts = $town_counts = $city_counts = $metropolis_counts = 0;
        foreach ($land_type_counts as $land_type_count) {
            if ($land_type_count['land_type'] == $this->village_key) {
                $village_counts = $land_type_count['count'];
            }
            else if ($land_type_count['land_type'] == $this->town_key) {
                $town_counts = $land_type_count['count'];
            }
            else if ($land_type_count['land_type'] == $this->city_key) {
                $city_counts = $land_type_count['count'];
            }
            else if ($land_type_count['land_type'] == $this->metropolis_key) {
                $metropolis_counts = $land_type_count['count'];
            }
        }
        // Return how many surplus or deficiency exists
        $minimum_of_previous = 5;
        $valid_upgrades['town'] = $village_counts - (max($town_counts, 1) * $minimum_of_previous);
        $valid_upgrades['city'] = $town_counts - (max($city_counts, 1) * $minimum_of_previous);
        $valid_upgrades['metropolis'] = $city_counts - (max($metropolis_counts, 1) * $minimum_of_previous);
        return $valid_upgrades;
    }

	// Land form
	public function land_form()
    {
        // If user not logged in, return with fail
        if (!$this->session->userdata('logged_in')) {
            echo '{error": "User not logged in"}';
            return false;
        }

		// Basic Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('form_type_input', 'Form Type Input', 'trim|required|max_length[32]');
        $this->form_validation->set_rules('coord_slug_input', 'Coord Key Input', 'trim|required|max_length[8]');
        $this->form_validation->set_rules('world_key_input', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('land_name', 'Land Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('content', 'Content', 'trim|max_length[1000]');
        
        // Fail Basic Validation
        if ($this->form_validation->run() == FALSE) {
            echo '{"error": "' . trim(strip_tags(validation_errors())) . '"}';
            return false;
        }

        // Account
        $session_data = $this->session->userdata('logged_in');
        $user_id = $data['user_id'] = $session_data['id'];

        // Set inputs
        $coord_slug = $this->input->post('coord_slug_input');
        $world_key = $this->input->post('world_key_input');
        $form_type = $this->input->post('form_type_input');
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
        $land_square['sum_effects'] = $this->game_model->get_sum_effects_of_land($land_square['id']);
        $land_key = $land_square['id'];
        $land_name = $this->input->post('land_name');
        $land_type = $this->input->post('land_type');
        $account = $this->user_model->get_account_by_keys($user_id, $world_key);
        $account = $this->get_full_account($account);
        $account_key = $account['id'];
        $color = $account['color'];
        $content = $this->input->post('content');

        // Advance Validation
        $land_form_validation = $this->land_form_validation($land_square, $account);
        
        // Fail
        if (!$land_form_validation) {
            // Error message echoed by above function
            return false;
        }

        // Content
        // $content = $this->sanitize_html($content);
        $content = $content;

        if ($form_type === 'build_embassy') {
            $action_type = $form_type;
            $player_has_embassy = $this->game_model->get_embassy_by_player_and_land($account_key, $land_key);
            if (!empty($player_has_embassy)) {
                echo '{"status": "fail", "message": "You already have an embassy here."}';
                return false;
            }
            $this->game_model->add_player_embassy($account_key, $land_key, $world_key, $this->embassy_key);
            echo '{"status": "success", "result": true, "land_key": "' . $land_square['id'] . '", "message": "Embassy Built"}';
            return true;
        }
        else if ($form_type === 'remove_embassy') {
            $action_type = $form_type;
            $player_has_embassy = $this->game_model->get_embassy_by_player_and_land($account_key, $land_key);
            if (empty($player_has_embassy)) {
                echo '{"status": "fail", "message": "You don\'t have an embassy here."}';
                return false;
            }
            $this->game_model->remove_player_embassy($account_key, $land_key, $this->embassy_key);
            echo '{"status": "success", "result": true, "land_key": "' . $land_square['id'] . '", "message": "Embassy Removed"}';
            return true;
        }
        else if ( is_numeric($form_type) && $form_type < 0 ) {
            $action_type = 'demolish';
        }
        else if ( is_numeric($form_type) ) {
            $action_type = 'build';
        }
        else if ($land_square['land_type'] == $this->unclaimed_key) {
            $action_type = 'claim';
        }
        else if ($account['id'] === $land_square['account_key']) {
            $action_type = 'update';
        }
        else {
            $action_type = 'attack';
        }

        // Demolish
        if ($action_type === 'demolish') {
            $this->game_model->remove_modifier_from_land($land_square['id'], abs($form_type), 1);
            echo '{"status": "success", "result": true, "land_key": "' . $land_square['id'] . '", "message": "Building Demolished"}';
            return true;
        }

        // Prevent building when no treasury
        if ($action_type === 'build' && $account['stats']['treasury_after'] <= $this->building_minimum && $form_type != $this->village_key && $form_type != $this->town_key && $form_type != $this->city_key && $form_type != $this->capitol_key) {
            echo '{"status": "fail", "message": "Your revenue is too low to build. Try raising taxes or demolishing buildings."}';
            return false;
        }

        // Prevent new players from taking towns or larger
        if ($account['stats']['land_count'] < 1 && $land_square['land_type'] >= $this->town_key) {
            echo '{"status": "fail", "message": "You must begin your nation at a village or unclaimed land"}';
            return false;
        }

        // Prevent new players from taking towns or larger
        if ($action_type === 'attack' && $account['stats']['land_count'] < $this->sniper_land_minimum && (int) $land_square['land_type'] === $this->metropolis_key) {
            echo '{"status": "fail", "message": "You must have at least ' . $this->sniper_land_minimum . ' lands to take a Metropolis"}';
            return false;
        }

        // Prevent attacking without political support
        if ($action_type != 'update' && $account['stats']['support'] < 0 && $account['tutorial'] >= 2 && $form_type != $this->village_key) {
            echo '{"status": "fail", "message": "Your political support is too low for your government to function."}';
            return false;
        }

        // Upgrade Logic
        $effects = $data['modify_effect_dictionary'] = $this->effects;
        if ($action_type === 'build') {
            $result = $this->land_form_upgrade($effects, $form_type, $world_key, $account_key, $land_key, $coord_slug);
            if (!$result) {
                echo '{"status": "fail", "message": "Unable to build on your land. Please report this bug using top right menu."}';
                return false;
            }
            echo '{"status": "success", "result": true, "land_key": "' . $land_square['id'] . '", "message": "Built"}';
            return true;
        }

        // Do weariness logic
        if ($action_type === 'attack' || $action_type === 'claim') {
            $weariness = $this->weariness_calculate($account, $land_square, false);
            $this->game_model->add_weariness_to_account($account['id'], $weariness);
        }

        // Make capitol if tutorial
        if ($account['tutorial'] < 2) {
            $land_type = $this->town_key;
            $this->game_model->update_land_capitol_status($land_key, $capitol = 1);
            $this->user_model->update_account_tutorial($account_key, 2);
            $this->game_model->remove_modifiers_from_land($land_key);
            $this->game_model->add_modifier_to_land($land_key, $this->town_key);
            $this->game_model->add_modifier_to_land($land_key, $this->capitol_key);
            // Add town to square
        }
        // Update
        else if ($action_type === 'update') {
            $land_type = $land_square['land_type'];
        } 
        // Attack or claim
        else if ($action_type === 'attack' || $action_type === 'claim') {
            $land_type = $this->village_key;
            $land_name = $account['nation_name'];
            $content = '';
            if ($land_square['capitol']) {
                $this->game_model->remove_all_embassy_of_land($land_key);
                $this->game_model->remove_modifier_from_land($land_key, $this->embassy_key, 9999);
                $this->game_model->update_land_capitol_status($land_key, $capitol = 0);
            }
            if ($land_square['land_type'] != $this->unclaimed_key || $land_square['land_type'] != $this->village_key) {
                $this->game_model->remove_modifiers_from_land($land_key);
            }
            $this->game_model->add_modifier_to_land($land_key, $this->village_key);
        }

        // Tutorial progress for update or build
        if ($account['tutorial'] === '3' && ($action_type === 'update' || $action_type === 'build') ) {
            $this->user_model->update_account_tutorial($account_key, 4);
        }
        // Tutorial progress for additional attack or claim
        else if ($account['tutorial'] === '4' && ($action_type === 'attack' || $action_type === 'claim') ) {
            $this->user_model->update_account_tutorial($account_key, 5);
        }

        // Update land
        $this->game_model->update_land_data($land_square['id'], $account_key, $land_name, $content, $land_type, $color);

        // Reset weariness if attacked player has no land now
        // Disabled to nerf "snipers" and for better performance
        /* $defender_new_land_count = $this->game_model->count_lands_of_account($land_square['account_key']);
        if ($defender_new_land_count['count'] == 0) {
            $this->game_model->set_weariness_from_account($land_square['account_key'], 0);
        } */

        // Attack response
        if ($action_type === 'attack') {
            echo '{"status": "success", "result": true, "land_key": "' . $land_square['id'] . '", "message": "Captured"}';
        }
        // Claim response
        else if ($action_type === 'claim') {
            echo '{"status": "success", "result": true, "land_key": "' . $land_square['id'] . '", "message": "Claimed"}';
        } 
        // Update response
        else if ($action_type === 'update') {
            echo '{"status": "success", "result": true, "land_key": "' . $land_square['id'] . '", "message": "Updated"}';
        }

        return true;
	}

	// Validate Land Form Callback
	public function land_form_validation($land_square, $active_account)
	{
        // User Information
        if (!$this->session->userdata('logged_in')) {
            $this->form_validation->set_message('land_form_validation', 'You are not currently logged in. Please log in again.');
            return false;
        }

        // Get land info for verifying our inputs
        $session_data = $this->session->userdata('logged_in');
        $user_id = $data['user_id'] = $session_data['id'];
        $form_type = $this->input->post('form_type_input');
        $coord_slug = $this->input->post('coord_slug_input');
        $world_key = $this->input->post('world_key_input');
        $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world_key);
        $active_account_key = $active_account['id'];
        $name_at_action = $land_square['land_name'];
        $passive_account_key = $land_square['account_key'];
        $in_range = $this->check_if_land_is_in_range($world_key, $active_account_key, $active_account['stats']['land_count'], $world['land_size'], $land_square['lat'], $land_square['lng'], false);

        // Town, City, Metro, Capitol check

        // Upgrade treasury check

        // Check for inaccuracies
        // Claiming land that isn't unclaimed
        if ($form_type === 'claim' && $land_square['land_type'] != $this->unclaimed_key) {
            echo '{"status": "fail", "message": "This land has been claimed"}';
            return false;
        }
        // Updating land that isn't theirs
        else if ($form_type === 'update' && $land_square['account_key'] != $active_account_key) {
            echo '{"status": "fail", "message": "This land has been bought and is no longer yours"}';
            return false;
        }
        // Attacking land that is already theirs
        else if ($form_type === 'attack' && $land_square['account_key'] === $active_account_key) {
            echo '{"status": "fail", "message": "This land is already yours"}';
            return false;
        }
        // Attacking or claiming land that is not in range
        else if ( ($form_type === 'claim' || $form_type === 'attack') && !$in_range) {
            echo '{"status": "fail", "message": "This land is not in range"}';
            return false;
        }
        // Attacking or claiming land that is not in range
        else if ( is_numeric($form_type) && $land_square['account_key'] != $active_account_key) {
            echo '{"status": "fail", "message": "This land has been bought and is no longer yours"}';
            return false;
        }

        // Check for valid previous lands for land upgrade
        $valid_upgrades = $this->account_valid_upgrades($active_account_key);
        if ($form_type == $this->town_key && $valid_upgrades['town'] < 0) {
            echo '{"status": "fail", "message": "You need more villages first. You may have lost a few since you opened the form."}';
            return false;
        }
        if ($form_type == $this->city_key && $valid_upgrades['city'] < 0) {
            echo '{"status": "fail", "message": "You need more towns first. You may have lost a few since you opened the form."}';
            return false;
        }
        if ($form_type == $this->metropolis_key && $valid_upgrades['metropolis'] < 0) {
            echo '{"status": "fail", "message": "You need more cities first. You may have lost a few since you opened the form."}';
            return false;
        }
        if ($form_type == $this->fortification_key && $land_square['land_type'] != $this->village_key) {
            echo '{"status": "fail", "message": "Only Villages may become Foritications."}';
            return false;
        }

        // Everything checks out
        return true;
	}

    public function land_form_upgrade($effects, $form_type, $world_key, $account_key, $land_key, $coord_slug)
    {
        foreach ($effects as $effect) {
            $land_type_effect_keys = array($this->unclaimed_key, $this->village_key, $this->town_key, $this->city_key, $this->metropolis_key, $this->fortification_key);
            // Capitol
            if ($effect['name'] === 'capitol' && $form_type === $effect['id']) {
                $this->game_model->remove_capitol_from_account($account_key, $this->capitol_key, $this->embassy_key);
                $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $this->game_model->update_land_capitol_status($land_key, $capitol = 1);
                break;
            }
            // Village
            else if ($effect['name'] === 'village' && $form_type === $effect['id']) {
                $this->game_model->remove_modifiers_from_land($land_key);
                $this->game_model->update_land_capitol_status($land_key, $capitol = 0);
                $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $this->game_model->upgrade_land_type($land_key, $this->village_key);
                break;
            }
            // Town
            else if ($effect['name'] === 'town' && $form_type === $effect['id']) {
                $this->game_model->remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys);
                $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $this->game_model->upgrade_land_type($land_key, $this->town_key);
                break;
            }
            // City
            else if ($effect['name'] === 'city' && $form_type === $effect['id']) {
                $this->game_model->remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys);
                $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $this->game_model->upgrade_land_type($land_key, $this->city_key);
                break;
            }
            // Metropolis
            else if ($effect['name'] === 'metropolis' && $form_type === $effect['id']) {
                $this->game_model->remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys);
                $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $this->game_model->upgrade_land_type($land_key, $this->metropolis_key);
                break;
            }
            // Fortification
            else if ($effect['name'] === 'fortification' && $form_type === $effect['id']) {
                $this->game_model->remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys);
                $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $this->game_model->upgrade_land_type($land_key, $this->fortification_key);
                break;
            }
            // Regular Upgrades
            else if ($form_type === $effect['id']) {
                $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                break;
            }
        }
        return true;
    }

    public function weariness_calculate($attacking_account, $land_square, $defender_account = false)
    {
        // Used to keep track of which accounts are active
        if (!$attacking_account['active_account']) {
            // Mark account as active
            $this->game_model->update_account_active_state($attacking_account['id'], 1);
        }

        // Start at 1
        $weariness = 1;

        // Increase weariness on larger players
        $weariness += floor($attacking_account['stats']['land_count'] / $this->weariness_increase_land_count);

        // If unclaimed, we're done here
        if ($land_square['account_key'] == 0) {
            return $weariness;
        }

        // Get accounts
        if (!$defender_account) {
            $defender_account = $this->user_model->get_account_by_id($land_square['account_key']);
            $defender_account = $this->get_full_account($defender_account);
        }
        if (!$defender_account) {
            return $weariness;
        }

        // Weariness Military Algorithm
        if ($attacking_account['stats']['military_total'] >= $defender_account['stats']['military_total'] * $this->ww_nerf) {
            $weariness += 1;
        }
        else if ($attacking_account['stats']['military_total'] >= $defender_account['stats']['military_total']) {
            $weariness += 2;
        }
        else if ($attacking_account['stats']['military_total'] * $this->ww_nerf >= $defender_account['stats']['military_total']) {
            $weariness += 3;
        }
        else if ($attacking_account['stats']['military_total'] * $this->ww_nerf * $this->ww_nerf >= $defender_account['stats']['military_total']) {
            $weariness += 4;
        }
        else if ($attacking_account['stats']['military_total'] * $this->ww_nerf * $this->ww_nerf * $this->ww_nerf >= $defender_account['stats']['military_total']) {
            $weariness += 5;
        }
        else if ($attacking_account['stats']['military_total'] * $this->ww_nerf * $this->ww_nerf * $this->ww_nerf * $this->ww_nerf >= $defender_account['stats']['military_total']) {
            $weariness += 6;
        }
        else if ($attacking_account['stats']['military_total'] * $this->ww_nerf * $this->ww_nerf * $this->ww_nerf * $this->ww_nerf * $this->ww_nerf >= $defender_account['stats']['military_total']) {
            $weariness += 7;
        }
        else {
            $weariness += 8;
        }

        // Weariness Land Type Defense Bonus
        $modify_effect_dictionary = $this->effects;
        foreach ($modify_effect_dictionary as $effect) {
            if ($land_square['land_type'] == $effect['id'] && $effect['defense'] > 0) {
                $weariness = $weariness * $effect['defense'];
            }
            if ($effect['name'] === 'capitol' && $land_square['capitol'] == 1) {
                $weariness = $weariness * $effect['defense'];
            }
        }

        // Get Leaderboard Cached in JSON
        $leaderboard = json_decode(file_get_contents($this->leaderboard_filepath . $land_square['world_key'] . '.json'), true);

        // Population Defence Bonus
        $highest_population_account_key = $this->get_highest_account_key_from_leaderboard_stat($leaderboard, 'population');
        if ($defender_account['id'] === $highest_population_account_key) {
            $weariness = floor($weariness * $this->defence_bonus);
        }

        // Culture Attack Bonus
        $highest_culture_account_key = $this->get_highest_account_key_from_leaderboard_stat($leaderboard, 'culture');
        if ($attacking_account['id'] === $highest_culture_account_key) {
            $weariness = floor($weariness / $this->attack_bonus);
        }

        return $weariness;
    }

    // Budget
    public function budget_form()
    {
        // User Information
        if (!$this->session->userdata('logged_in')) {
            return false;
        }
        $session_data = $this->session->userdata('logged_in');
        $user_id = $data['user_id'] = $session_data['id'];
        
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('world_key', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('input_government', 'Form of Government', 'trim|required|integer|max_length[1]');
        $this->form_validation->set_rules('input_tax_rate', 'Tax Rate', 'trim|integer|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('input_military_budget', 'Military Budget', 'trim|integer|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('input_entitlements_budget', 'Entitlements Budget', 'trim|integer|greater_than_equal_to[0]|less_than_equal_to[100]');

        $world_key = $this->input->post('world_key');

        // Fail
        if ($this->form_validation->run() == FALSE) {
            echo '{"error": "' + validation_errors() + '"}';
            return false;
        }
        // Success
        else {
            $government = $this->input->post('input_government');
            $tax_rate = $this->input->post('input_tax_rate');
            $military_budget = $this->input->post('input_military_budget');
            $entitlements_budget = $this->input->post('input_entitlements_budget');

            if ($military_budget + $entitlements_budget > 100) {
                echo '{"status": "fail", "message": "Military and Entitlements combined can not be more than 100%"}';
                return false;
            }

            // Set account
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $account_key = $account['id'];
            $this->game_model->update_account_budget($account_key, $government, $tax_rate, $military_budget, $entitlements_budget);

            // Progress Tutorial
            if ($account['tutorial'] < 3) {
                $this->user_model->update_account_tutorial($account_key, 3);
            }

            // Success
            echo '{"status": "success", "result": true, "message": "Budget Updated"}';
        }
    }

    public function get_highest_account_key_from_leaderboard_stat($leaders, $key)
    {
        $highest_account_key = 0;
        $highest_of_value = 0;
        if (empty($leaders)) {
            return 0;
        }
        foreach ($leaders as $leader) {
            if ($leader['stats'][$key] >= $highest_of_value) {
                $highest_of_value = $leader['stats'][$key];
                $highest_account_key = $leader['id'];
            }
        }
        return $highest_account_key;
    }

    // Check if land is in range for account
    public function check_if_land_is_in_range($world_key, $account_key, $land_count, $land_size, $lat, $lng, $siege)
    {
        // All land in range if no land
        if ($land_count < 1) {
            return true;
        }
        // Check surrounding lands
        $coord_array = [];
        if ($siege === false) {
            $coord_array[] = ($lat) . ',' . ($lng);
        }
        $coord_array[] = ($lat + $land_size) . ',' . ($lng);
        $coord_array[] = ($lat) . ',' . ($lng + $land_size);
        $coord_array[] = ($lat - $land_size) . ',' . ($lng);
        $coord_array[] = ($lat) . ',' . ($lng - $land_size);
        // Fix lng crossover point
        foreach ($coord_array as &$coord) {
            // All
            if (strpos($coord, '-180') !== false) {
                $coord = str_replace('-180', '180', $coord);
            }
            // Huge
            if (strpos($coord, '182') !== false) {
                $coord = str_replace('182', '-178', $coord);
            }
            // Big
            if (strpos($coord, '183') !== false) {
                $coord = str_replace('183', '-177', $coord);
            }
            // Medium
            if (strpos($coord, '184') !== false) {
                $coord = str_replace('184', '-176', $coord);
            }
            // Small
            if (strpos($coord, '186') !== false) {
                $coord = str_replace('186', '-174', $coord);
            }
            // Tiny
            if (strpos($coord, '192') !== false) {
                $coord = str_replace('192', '-168', $coord);
            }
        }
        $coord_matches = $this->game_model->land_range_check($world_key, $account_key, $coord_array);
        if (!empty($coord_matches) ) {
            return true;
        }
        return false;
    }

    // Get leaderboards
    public function leaderboards($world)
    {
        if (isset($_GET['json'])) {
            $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world);
        }
        // This is accounts to get, number shown is determined with use of CSS
        $limit = 1000;
        $land_leaders = $this->leaderboard_model->leaderboard_land_owned($world['id'], $limit);
        $leaders = false;
        foreach ($land_leaders as $leader) {
            if ($leader['account_key'] == 0) {
                continue;
            }
            $leader_account = $this->user_model->get_account_by_id($leader['account_key']);
            $this_leader = $this->get_full_account($leader_account);
            $leaders[] = $this_leader;
        }

        // Record into file as json as simple form of caching
        $json_leaderboard = json_encode($leaders);
        file_put_contents($this->leaderboard_filepath . $world['id'] . '.json', $json_leaderboard);

        if (isset($_GET['json'])) {
            echo $json_leaderboard;
            return true;
        }
        return $leaders;
    }

    public function entitlements_effect_calculate($value, $nerf)
    {
        $value = $value === 0 ? 1 : $value;
        $nerf = $nerf === 0 ? 1 : $nerf;
        return floor($value / $nerf);
    }

    // Creates expodential returns
    public function tax_weariness_calculate($value, $boost)
    {
        // This complicated algo ensures a few things
        // That higher value always causes a higher result
        // That higher boost always causes a higher result
        // That values of 20-60 cause reasonable amounts of weariness depending on boost between 1-4
        $value = $value === 0 ? 1 : $value;
        $boost = $boost === 0 ? 1 : $boost;
        $original_algo = floor( pow($value, $boost) / pow( $boost * $boost, $boost ) );
        return floor( ($original_algo + ($value * $boost) ) / $this->tax_weariness_nerf );
    }

    public function get_percent_of($number, $percentage) {
        return ceil( $number * ($percentage / 100) );
    }

    // Land dictionary for reference
    public function government_dictionary()
    {
        $government_dictionary[1] = 'Democracy';
        $government_dictionary[2] = 'Oligarchy';
        $government_dictionary[3] = 'Autocracy';
        return $government_dictionary;
    }

    public function stroke_color_dictionary()
    {
        $stroke_color_dictionary['village'] = '#428BCA';
        $stroke_color_dictionary['town'] = '#00E300';
        $stroke_color_dictionary['city'] = '#FFD900';
        $stroke_color_dictionary['metropolis'] = '#A600A6';
        $stroke_color_dictionary['fortification'] = '#222222';
        $stroke_color_dictionary['capitol'] = '#FF0000';
        return $stroke_color_dictionary;
    }

    public function land_type_key_dictionary()
    {
        $land_type_key_dictionary['unclaimed'] = $this->unclaimed_key;
        $land_type_key_dictionary['village'] = $this->village_key;
        $land_type_key_dictionary['town'] = $this->town_key;
        $land_type_key_dictionary['city'] = $this->city_key;
        $land_type_key_dictionary['metropolis'] = $this->metropolis_key;
        $land_type_key_dictionary['fortification'] = $this->fortification_key;
        $land_type_key_dictionary['capitol'] = $this->capitol_key;
        return $land_type_key_dictionary;
    }

    public function next_reset_dictionary()
    {
        $next_reset[1] = '1st and 15th of the month at 8 PM ET';
        $next_reset[2] = 'Every month on the first at 8PM ET';
        $next_reset[3] = 'Every Sunday at 8 PM ET';
        $next_reset[4] = 'Every day at 8PM ET';
        $next_reset[5] = 'Every 4 Hours';
        return $next_reset;
    }

    // Function to close tags
    public function sanitize_html($html) {
        // Content input allow only gmail whitelisted tags
        $whitelisted_tags = '<a><abbr><acronym><address><area><b><bdo><big><blockquote><br><button><caption><center><cite><code><col><colgroup><dd><del><dfn><dir><div><dl><dt><em><fieldset><font><form><h1><h2><h3><h4><h5><h6><hr><i><img><input><ins><kbd><label><legend><li><map><menu><ol><optgroup><option><p><pre><q><s><samp><select><small><span><strike><strong><sub><sup><table><tbody><td><textarea><tfoot><th><thead><u><tr><tt><u><ul><var>';
        $html = strip_tags($html, $whitelisted_tags);
        // Disallow these character combination to prevent potential javascript injection
        $disallowed_strings = ['onerror', 'onload', 'onclick', 'ondblclick', 'onkeydown', 'onkeypress', 'onkeyup', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup'];
        $html = str_replace($disallowed_strings, '', $html);
        // Close open tags
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i=0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</'.$openedtags[$i].'>';
            } 
            else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        // Replace new lines with break tags
        $html = preg_replace("/\r\n|\r|\n/",'<br/>',$html);
        // Return result
        return $html;
    }

}