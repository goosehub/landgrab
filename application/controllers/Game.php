<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {

    protected $unclaimed_key = 1;
    protected $village_key = 2;
    protected $town_key = 3;
    protected $city_key = 4;
    protected $metropolis_key = 5;
    protected $fortification_key = 6;
    protected $capitol_key = 10;

    protected $democracy_key = 1;
    protected $oligarchy_key = 2;
    protected $autocracy_key = 3;

    protected $war_weariness_increase_land_count = 300;

	function __construct() {
	    parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
	    $this->load->model('leaderboard_model', '', TRUE);
	}

	// Game view and update json
	public function index($world_slug = 1, $marketing_slug = false)
	{
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
        $data['last_winner_account'] = $this->user_model->get_account_by_id($world['last_winner_account_key']);
        if (!$data['last_winner_account']) {
            $data['last_winner_account']['username'] = 'No Winner';
            $data['last_winner_account']['leader_portrait'] = 'default_leader_portrait.png';
            $data['last_winner_account']['nation_flag'] = 'default_nation_flag.png';
        } else {
            $data['last_winner_account'] = $this->get_full_account($data['last_winner_account']);
        }

        $next_reset_dictionary = $this->next_reset_dictionary();
        $data['next_reset'] = $next_reset_dictionary[$world['id']];

        $data['war_weariness_increase_land_count'] = $this->war_weariness_increase_land_count;


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
            $modify_effect_dictionary = $data['modify_effect_dictionary'] = $this->game_model->get_all_modify_effects();
        }

        // Get all lands
        $server_update_timespan = 30;
        $data['update_timespan'] = ($server_update_timespan / 2) * 1000;
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
            // Send refresh signal to clients when true
            $data['refresh'] = false;

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
        // Get account
        $account['land_count'] = $account['land_count'] = $this->user_model->get_count_of_account_land($account['id']);
        $account['stats'] = $this->game_model->get_sum_effects_for_account($account['id']);

        // Democracy Taxes
        $account['stats']['corruption_rate'] = 100;
        if ($account['government'] == $this->democracy_key) {
            $account['stats']['corruption_rate'] = 0;
        }
        // Oligarchy Taxes
        else if ($account['government'] == $this->oligarchy_key) {
            $account['stats']['corruption_rate'] = 10;
        }
        // Autocracy Taxes
        else if ($account['government'] == $this->autocracy_key) {
            $account['stats']['corruption_rate'] = 30;
        }

        $tax_unpopularity = 2;
        $account['effective_tax_rate'] = ceil($account['tax_rate'] * (100 - $account['stats']['corruption_rate']) / 100 );
        $account['stats']['tax_income'] = ceil( $account['stats']['gdp'] * ($account['effective_tax_rate'] / 100) );
        $account['stats']['corruption_total'] = abs(ceil($account['stats']['tax_income'] - $account['stats']['gdp'] * ($account['tax_rate'] / 100) ) );
        if ($account['stats']['corruption_rate'] === 0) {
            $account['stats']['corruption_total'] = 0;
        }
        $account['stats']['military_spending'] = $account['stats']['tax_income'] * ($account['military_budget'] / 100);
        $account['stats']['military_after'] = ceil($account['stats']['military'] + $account['stats']['military_spending'] + $account['stats']['military']);
        $account['stats']['entitlements'] = ceil($account['stats']['tax_income'] * ($account['entitlements_budget'] / 100) );
        $entitlments_nerf = 30;
        $account['stats']['entitlements_effect'] = ceil( ($account['effective_tax_rate'] * $account['entitlements_budget']) / $entitlments_nerf);
        $account['stats']['treasury_after'] = ceil($account['stats']['tax_income'] - $account['stats']['military_spending'] - $account['stats']['entitlements'] + $account['stats']['treasury']);
        $account['stats']['support'] = 100 - $account['war_weariness'] - ($account['tax_rate'] * $tax_unpopularity) + $account['stats']['entitlements_effect'] + $account['stats']['support'];
        $account['stats']['war_weariness'] = $account['war_weariness'];
        $account['stats']['building_maintenance'] = abs($account['stats']['treasury']);

        // See if functioning
        $account['functioning'] = true;
        if ($account['government'] == $this->democracy_key && $account['stats']['support'] < 50) {
            $account['functioning'] = false;
        }
        else if ($account['government'] == $this->oligarchy_key && $account['stats']['support'] < 30) {
            $account['functioning'] = false;
        }
        else if ($account['government'] == $this->autocracy_key && $account['stats']['support'] < 10) {
            $account['functioning'] = false;
        }

        // Get Username

        $user = $this->user_model->get_user($account['user_key']);
        $account['username'] = $user['username'];

        // Record account as loaded
        $query_action = $this->user_model->account_loaded($account['id']);

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
        $land_square['effects'] = $this->game_model->get_effects_of_land($land_square['id']);
        $land_square['sum_effects'] = $this->game_model->get_sum_effects_of_land($land_square['id']);
        $land_square['sum_modifiers'] = $this->game_model->get_sum_modifiers_for_land($land_square['id']);
        if ($land_square['account_key'] != 0) {
            $account = $land_square['account'] = $this->user_model->get_account_by_id($land_square['account_key']);
            // This shouldn't happen, but it does
            // This is a workaround to ensure issue is not noticed
            if (!$account) {
                // Unclaim land
                $this->game_model->update_land_data($land_square['id'], 0, '', '', 1, '#000000');
                $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
                $land_square['effects'] = $this->game_model->get_effects_of_land($land_square['id']);
                $land_square['sum_effects'] = $this->game_model->get_sum_effects_of_land($land_square['id']);
                $land_square['sum_modifiers'] = $this->game_model->get_sum_modifiers_for_land($land_square['id']);
                $query_action = $this->game_model->update_land_capitol_status($land_square['id'], $capitol = 0);
                $account = false;
            } else {
                $account = $land_square['account'] = $this->get_full_account($account);
            }
        } 
        else {
            $account = false;
        }

        // War Weariness
        $land_square['war_weariness'] = 0;
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $session_data['id'];
            $requester_account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $requester_account = $this->get_full_account($requester_account);
            $land_square['war_weariness'] = $this->war_weariness_calculate($requester_account, $land_square['account_key'], $land_square);
            $land_square['valid_upgrades'] = $this->account_valid_upgrades($requester_account['id']);
        }

        // Land range false by default
        $land_square['in_range'] = false;

        // Add username to array
        $land_square['username'] = '';
        if ($account) {
            $owner = $this->user_model->get_user($account['user_key']);
            if ( isset($owner['username']) && isset($land_square['land_name']) ) {
                $land_square['username'] = $owner['username'];
            }
        }

        // Get account
        $log_check = false;
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world_key);
            $account['land_count'] = $data['account']['land_count'] = $this->user_model->get_count_of_account_land($account['id']);
            $log_check = true;
            // Check if land is in range
            $land_square['in_range'] = $this->check_if_land_is_in_range($world_key, $account['id'], $account['land_count'], 
                $world['land_size'], $land_square['lat'], $land_square['lng'], false);
            $land_square['range_check'] = $this->check_if_land_is_in_range($world_key, $land_square['account_key'], 20, 
                $world['land_size'], $land_square['lat'], $land_square['lng'], true);
        }

        // Echo data to client to be parsed
	    if (isset($land_square['land_name'])) {
            // Strip html entities from all untrusted columns, except content as it's stripped on insert
            $land_square['land_name'] = htmlspecialchars($land_square['land_name']);
            $land_square['color'] = htmlspecialchars($land_square['color']);
            $land_square['username'] = htmlspecialchars($land_square['username']);
            if ($json_output) {
                // Filter tags except img with src only
                function filter(&$value) {
                  // $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                  $value = strip_tags($value, '<img>');
                  // $value = preg_replace('#&lt;(/?(?:img))&gt;#', '<\1>', $value);
                  $value = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\ssrc=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i",'<$1$2$3>', $value);
                  $value = nl2br($value);
                }
                array_walk_recursive($land_square, "filter");
                echo json_encode($land_square);
            }
            else {
                return $land_square;
            }
        }
	    // If none found, default to this
        else {
	        echo '{"error": "Land not found"}';
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
        // Authentication
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
        }
        // If user not logged in, return with fail
        else {
            echo '{"status": "fail", "message": "User not logged in"}';
            return false;
        }
        
		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('form_type_input', 'Form Type Input', 'trim|required|max_length[32]|callback_land_form_validation');
        $this->form_validation->set_rules('coord_slug_input', 'Coord Key Input', 'trim|required|max_length[8]');
        $this->form_validation->set_rules('world_key_input', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('land_name', 'Land Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('content', 'Content', 'trim|max_length[1000]');

        // Fail
	    if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            return false;
        } 
		// Success

        // Set inputs
        $coord_slug = $this->input->post('coord_slug_input');
        $world_key = $this->input->post('world_key_input');
        $form_type = $this->input->post('form_type_input');
        $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world_key);
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
        $land_square['sum_effects'] = $this->game_model->get_sum_effects_of_land($land_square['id']);
        $land_key = $land_square['id'];
        $land_name = $this->input->post('land_name');
        $land_type = $this->input->post('land_type');
        $account = $this->user_model->get_account_by_keys($user_id, $world_key);
        $account = $this->get_full_account($account);
        $account['land_count'] = $this->user_model->get_count_of_account_land($account['id']);
        $account_key = $account['id'];
        $color = $account['color'];
        $content = $this->input->post('content');

        // Content
        // $content = $this->sanitize_html($content);
        $content = $content;

        if ( is_numeric($form_type) ) {
            $action_type = 'build';
        }
        else if ($land_square['land_type'] === $this->unclaimed_key) {
            $action_type = 'claim';
        }
        else if ($account['id'] === $land_square['account_key']) {
            $action_type = 'update';
        }
        else {
            $action_type = 'attack';
        }

        // Prevent building when no treasury
        if ($action_type === 'build' && $account['stats']['treasury_after'] < 0 && $form_type != $this->village_key) {
            echo '{"status": "fail", "message": "Your revenue is too low to build. Try raising taxes."}';
            return false;
        }

        // Prevent new players from taking towns or larger
        if ($account['land_count'] < 1 && $land_square['land_type'] >= $this->town_key) {
            echo '{"status": "fail", "message": "You must begin your nation at a village or unclaimed land"}';
            return false;
        }

        if ($action_type != 'update' && !$account['functioning'] && $account['tutorial'] >= 2 && $form_type != $this->village_key) {
            echo '{"status": "fail", "message": "Your political support is too low for your government to function."}';
            return false;
        }

        // Upgrade Logic
        $effects = $data['modify_effect_dictionary'] = $this->game_model->get_all_modify_effects();
        if ( $action_type === 'build' ) {
            $result = $this->land_form_upgrade($effects, $form_type, $world_key, $account_key, $land_key, $coord_slug);
            if (!$result) {
                echo '{"status": "fail", "message": "Unable to build on your land. Please report this bug using top right menu."}';
                return false;
            }
            echo '{"status": "success", "result": true, "message": "Built"}';
            return true;
        }

        // Do war weariness logic
        if ($action_type === 'attack' || $action_type === 'claim') {
            $war_weariness = $this->war_weariness_calculate($account, $land_square['account_key'], $land_square);
            $this->game_model->add_war_weariness_to_account($account['id'], $war_weariness);
        }

        // Make capitol if tutorial
        if ($account['tutorial'] < 2) {
            $land_type = $this->town_key;
            $query_action = $this->game_model->update_land_capitol_status($land_key, $capitol = 1);
            $query_action = $this->user_model->update_account_tutorial($account_key, 2);
            $query_action = $this->game_model->remove_modifiers_from_land($land_key);
            $query_action = $this->game_model->add_modifier_to_land($land_key, $this->town_key);
            $query_action = $this->game_model->add_modifier_to_land($land_key, $this->capitol_key);
            // Add town to square
        }
        // Update
        else if ($action_type === 'update') {
            $land_type = $land_square['land_type'];
        // Attack or claim
        } 
        else {
            $land_type = $this->village_key;
            $land_name = $account['nation_name'];
            $content = '';
            $query_action = $this->game_model->remove_modifiers_from_land($land_key);
            $query_action = $this->game_model->add_modifier_to_land($land_key, $this->village_key);
            $query_action = $this->game_model->update_land_capitol_status($land_key, $capitol = 0);
        }

        // Tutorial progress for update or build
        if ($account['tutorial'] === '3' && ($action_type === 'update' || $action_type === 'build') ) {
            $query_action = $this->user_model->update_account_tutorial($account_key, 4);
        }
        // Tutorial progress for additional attack or claim
        else if ($account['tutorial'] === '4' && ($action_type === 'attack' || $action_type === 'claim') ) {
            $query_action = $this->user_model->update_account_tutorial($account_key, 5);
        }

        // Update land
        $query_action = $this->game_model->update_land_data($land_square['id'], $account_key, $land_name, $content, $land_type, $color);

        // Reset War Weariness if attacked player has no land now
        $defender_new_land_count = $this->game_model->count_lands_of_account($land_square['account_key']);
        if ($defender_new_land_count['count'] == 0) {
            // Disabled to nerf "snipers"
            // $this->game_model->set_war_weariness_from_account($land_square['account_key'], 0);
        }
        
        // Attack response
        if ($action_type === 'attack') {
            echo '{"status": "success", "result": true, "message": "Captured"}';
        }
        // Claim response
        else if ($action_type === 'claim') {
            echo '{"status": "success", "result": true, "message": "Claimed"}';
        } 
        // Update response
        else {
            echo '{"status": "success", "result": true, "message": "Updated"}';
        }

        return true;
	}

	// Validate Land Form Callback
	public function land_form_validation($form_type_input)
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
        $active_account = $this->user_model->get_account_by_keys($user_id, $world_key);
        $active_account['land_count'] = $this->user_model->get_count_of_account_land($active_account['id']);
        $active_account_key = $active_account['id'];
        $active_user = $this->user_model->get_user($active_account_key);
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
        $name_at_action = $land_square['land_name'];
        $passive_account_key = $land_square['account_key'];
        $passive_user = $this->user_model->get_user($passive_account_key);
        $in_range = $this->check_if_land_is_in_range($world_key, $active_account_key, $active_account['land_count'], $world['land_size'], $land_square['lat'], $land_square['lng'], false);

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
                $query_action = $this->game_model->remove_capitol_from_account($account_key);
                $query_action = $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $query_action = $this->game_model->update_land_capitol_status($land_key, $capitol = 1);
                break;
            }
            // Village
            else if ($effect['name'] === 'village' && $form_type === $effect['id']) {
                $query_action = $this->game_model->remove_modifiers_from_land($land_key);
                $query_action = $this->game_model->update_land_capitol_status($land_key, $capitol = 0);
                $query_action = $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $query_action = $this->game_model->upgrade_land_type($land_key, $this->village_key);
                break;
            }
            // Town
            else if ($effect['name'] === 'town' && $form_type === $effect['id']) {
                $query_action = $this->game_model->remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys);
                $query_action = $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $query_action = $this->game_model->upgrade_land_type($land_key, $this->town_key);
                break;
            }
            // City
            else if ($effect['name'] === 'city' && $form_type === $effect['id']) {
                $query_action = $this->game_model->remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys);
                $query_action = $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $query_action = $this->game_model->upgrade_land_type($land_key, $this->city_key);
                break;
            }
            // Metropolis
            else if ($effect['name'] === 'metropolis' && $form_type === $effect['id']) {
                $query_action = $this->game_model->remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys);
                $query_action = $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $query_action = $this->game_model->upgrade_land_type($land_key, $this->metropolis_key);
                break;
            }
            // Fortification
            else if ($effect['name'] === 'fortification' && $form_type === $effect['id']) {
                $query_action = $this->game_model->remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys);
                $query_action = $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                $query_action = $this->game_model->upgrade_land_type($land_key, $this->fortification_key);
                break;
            }
            // Regular Upgrades
            else if ($form_type === $effect['id']) {
                $query_action = $this->game_model->add_modifier_to_land($land_key, $effect['id']);
                break;
            }
        }
        return true;
    }

    public function war_weariness_calculate($account, $defender_account, $land_square)
    {
        // Used to make cron more effecient
        if ( !$account['active_account'] ) {
            // Mark account as active
            $this->game_model->update_account_active_state($account['id'], 1);
        }

        // Start at 1
        $war_weariness = 1;

        // Increase war weariness on larger players
        $war_weariness += floor($account['land_count'] / $this->war_weariness_increase_land_count);

        // If unclaimed, just 1 war weariness
        if ($defender_account == 0) {
            return $war_weariness;
        }

        // Get accounts
        $defender_account = $this->user_model->get_account_by_id($defender_account);
        if (!$defender_account) {
            return $war_weariness;
        }
        $defender_account = $this->get_full_account($defender_account);

        // War Weariness Military Algorithm
        if ($account['stats']['military_after'] >= $defender_account['stats']['military_after'] * 2) {
            $war_weariness += 1;
        }
        else if ($account['stats']['military_after'] >= $defender_account['stats']['military_after']) {
            $war_weariness += 2;
        }
        else if ($account['stats']['military_after'] * 2 >= $defender_account['stats']['military_after']) {
            $war_weariness += 3;
        }
        else if ($account['stats']['military_after'] * 2 * 2 >= $defender_account['stats']['military_after']) {
            $war_weariness += 4;
        }
        else if ($account['stats']['military_after'] * 2 * 2 * 2 >= $defender_account['stats']['military_after']) {
            $war_weariness += 5;
        }
        else if ($account['stats']['military_after'] * 2 * 2 * 2 * 2 >= $defender_account['stats']['military_after']) {
            $war_weariness += 6;
        }
        else if ($account['stats']['military_after'] * 2 * 2 * 2 * 2 * 2 >= $defender_account['stats']['military_after']) {
            $war_weariness += 7;
        }
        else {
            $war_weariness += 8;
        }
        // War Weariness Defense Bonus
        $modify_effect_dictionary = $this->game_model->get_all_modify_effects();
        foreach ($modify_effect_dictionary as $effect) {
            if ($land_square['land_type'] == $effect['id'] && $effect['defense'] > 0) {
                $war_weariness = $war_weariness * $effect['defense'];
            }
            if ($effect['name'] === 'capitol' && $land_square['capitol'] == 1) {
                $war_weariness = $war_weariness * $effect['defense'];
            }
        }

        return $war_weariness;
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
        // This is accounts to get, number shown is determined with use of CSS
        $limit = 10;
        $land_leaders = $this->leaderboard_model->leaderboard_land_owned($world['id'], $limit);
        $leaders = false;
        foreach ($land_leaders as $leader) {
            if ($leader['account_key'] == 0) {
                continue;
            }
            $leader_account = $this->user_model->get_account_by_id($leader['account_key']);
            $this_leader = $this->get_full_account($leader_account);
            $leader_user = $this->user_model->get_user($this_leader['user_key']);
            $this_leader['username'] = $leader_user['username'];
            $leaders[] = $this_leader;
        }
        return $leaders;
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
        $next_reset[1] = '1st and 15th of every Month at 8PM ET';
        $next_reset[2] = '10th, 20th, and 30th of every month at 8PM ET';
        $next_reset[3] = '5th, 10th, 15th, 20th, 25th, 30th of every month at 8PM ET';
        $next_reset[4] = 'Every even numbered day at 8PM ET';
        $next_reset[5] = 'Every day at 8PM ET';
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