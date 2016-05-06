<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {

	function __construct() {
	    parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('action_model', '', TRUE);
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

        // If logged in, get account specific data
        if ($log_check) {

            // Get account
            $account = $data['account'] = $this->user_model->get_account_by_keys($user_id, $world['id']);
            $account['land_count'] = $data['account']['land_count'] = $this->user_model->get_count_of_account_land($account['id']);

            // Record account as loaded
            $query_action = $this->user_model->account_loaded($account['id']);
        }

        // Get world leaderboards
        $data['leaderboards'] = $this->leaderboards($world);

        // Get all worlds
        $data['worlds'] = $this->user_model->get_all_worlds();

        // Get all lands
        $data['lands'] = $this->game_model->get_all_lands_in_world($world['id']);

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
        $this->load->view('map_script', $data);
        $this->load->view('interface_script', $data);
        $this->load->view('chat_script', $data);
        $this->load->view('footer', $data);
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
        } else {
            echo '{"error": "Input missing"}';
            return false;
        }

	    // Get Land Square
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);

        // Land range false by default
        $land_square['in_range'] = false;

        // Add username to array
        $account = $this->user_model->get_account_by_id($land_square['account_key']);
        $owner = $this->user_model->get_user($account['user_key']);
        if ( isset($owner['username']) && isset($land_square['land_name']) ) {
            $land_square['username'] = $owner['username'];
        } else {
            $land_square['username'] = '';
        }

        // Get account
        $log_check = false;
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $account['land_count'] = $data['account']['land_count'] = $this->user_model->get_count_of_account_land($account['id']);
            $log_check = true;
            // Check if land is in range
            $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world_key);
            $land_square['in_range'] = $this->check_if_land_is_in_range($world_key, $account['id'], $account['land_count'], 
                $world['land_size'], $land_square['lat'], $land_square['lng'], false);
            $land_square['range_check'] = $this->check_if_land_is_in_range($world_key, $land_square['account_key'], 20, 
                $world['land_size'], $land_square['lat'], $land_square['lng'], true);
        }

        // Echo data to client to be parsed
	    if (isset($land_square['land_name'])) {
            // Strip html entities from all untrusted columns, except content as it's stripped on insert
            $land_square['land_name'] = htmlentities($land_square['land_name']);
            $land_square['color'] = htmlentities($land_square['color']);
            $land_square['username'] = htmlentities($land_square['username']);
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
            } else {
                return $land_square;
            }
	    // If none found, default to this
        } else {
	        echo '{"error": "Land not found"}';
	    }
	}

	// Land form
	public function land_form()
    {
        // Authentication
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
        // If user not logged in, return with fail
        } else {
            $world_key = $this->input->post('world_key_input');
            echo '{"status": "fail", "message": "User not logged in"}';
            return false;
        }
        
		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('form_type_input', 'Form Type Input', 'trim|required|alpha|max_length[8]|callback_land_form_validation');
        $this->form_validation->set_rules('coord_slug_input', 'Coord Key Input', 'trim|required|max_length[8]');
        $this->form_validation->set_rules('world_key_input', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('land_name', 'Land Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('content', 'Content', 'trim|max_length[1000]');

        // Fail
	    if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            if (validation_errors() === '') {
                echo '{"status": "fail", "message": "An unknown error occurred"}';
            }
            echo '{"status": "fail", "message": "'. trim(preg_replace('/\s\s+/', ' ', validation_errors() )) . '"}';
            return false;
		// Success
        } else {
            // Set inputs
	    	$claimed = 1;
            $form_type = $this->input->post('form_type_input');
            $coord_slug = $this->input->post('coord_slug_input');
	        $world_key = $this->input->post('world_key_input');
            $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world_key);
            $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
            $land_name = $this->input->post('land_name');
	        $land_type = $this->input->post('land_type');
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $account['land_count'] = $this->user_model->get_count_of_account_land($account['id']);
            $account_key = $account['id'];
            $color = $account['color'];
            $content = $this->input->post('content');
            // $content = $this->sanitize_html($content);
            $content = $content;

            // Do attack logic
            if ($form_type === 'attack') {
                $attack_result = $this->land_attack($land_square, $world, $land_square['land_type'], $account);
                // Return failed attack message
                if (!$attack_result) {
                    echo '{"status": "success", "result": false, "message": "Defeat"}';
                    return false;
                } else {
                    echo '{"status": "success", "result": true, "message": "Victory"}';
                }
            }
            // Claim response
            else if ($form_type === 'claim') {
                echo '{"status": "success", "result": true, "message": "Claimed"}';
            // Update response
            } else {
                echo '{"status": "success", "result": true, "message": "Updated"}';
            }

            // Land type for update
            if ($form_type === 'claim' || $form_type === 'attack') {
                $land_type = 'village';
            } else {
                $land_type = $land_square['land_type'];
            }

            // Update land
	        $query_action = $this->game_model->update_land_data($world_key, $claimed, $coord_slug, $account_key, $land_name, $content, $land_type, $color);

            return true;
	    }
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
        $in_range = $this->check_if_land_is_in_range($world_key, $active_account_key, $active_account['land_count'], $world['land_size'], 
            $land_square['lat'], $land_square['lng'], false);

        // Check for inaccuracies
        // Claiming land that isn't unclaimed
        if ($form_type === 'claim' && $land_square['claimed'] != 0) {
            $this->form_validation->set_message('land_form_validation', 'This land has been claimed');
            return false;
        }
        // Updating land that isn't theirs
        else if ($form_type === 'update' && $land_square['account_key'] != $active_account_key) {
            $this->form_validation->set_message('land_form_validation', 'This land has been bought and is no longer yours');
            return false;
        }
        // Attacking land that is already theirs
        else if ($form_type === 'attack' && $land_square['account_key'] === $active_account_key) {
            $this->form_validation->set_message('land_form_validation', 'This land is already yours');
            return false;
        // Attacking or claiming land that is not in range
        } else if ($form_type != 'update' && !$in_range) {
            $this->form_validation->set_message('land_form_validation', 'This land is not in range');
            return false;
        }

        // Everything checks out
        return true;
	}

    // Land Transaction
    public function land_attack($land_square, $world, $land_type, $account)
    {        
        $defense_dictionary = $this->defense_dictionary();
        // If attacker has no lands and land is not fortified, then attacker wins
        if ($account['land_count'] < 1 && $defense_dictionary[$land_type] < 50) {
            return true;
        }

        // Get attack information
        $active_army = $account['active_army'];

        // Siege logic
        $range_check = $this->check_if_land_is_in_range($world['id'], $land_square['account_key'], 20, 
            $world['land_size'], $land_square['lat'], $land_square['lng'], true);
        if (!$range_check && $land_type === 'castle') {
            $land_type = 'fort';
        } else if (!$range_check && $land_type === 'fort') {
            $land_type = 'tower';
        } else if (!$range_check && $land_type === 'tower') {
            $land_type = 'wall';
        } else if (!$range_check && $land_type === 'wall') {
            $land_type = 'village';
        }

        $defending_army = $defense_dictionary[$land_type];
        $attack_power = rand(0,$active_army);
        $defend_power = rand(0,$defending_army);
        
        // Do random attack with two sides
        if ($attack_power > $defend_power) {
            // On victory, change losers passive army if needed
            $defender_account = $this->user_model->get_account_by_id($land_square['account_key']);
            $land_type_dictionary = $this->land_type_dictionary();
            $new_passive_army = $defender_account['passive_army'] - $land_type_dictionary[$land_square['land_type']];
            $query_action = $this->game_model->update_account_passive_army($land_square['account_key'], $new_passive_army);
            return true;
        } else {
            // On failure, destroy active army of attacker
            $query_action = $this->game_model->update_account_active_army($account['id'], 0);
            return false;
        }
    }

    // Land upgrade form
    public function land_upgrade_form()
    {
        // Authentication
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
        // If user not logged in, return with fail
        } else {
            $world_key = $this->input->post('world_key_input');
            echo '{"status": "fail", "message": "User not logged in"}';
            return false;
        }
        
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('coord_slug_input', 'Coord Key Input', 'trim|required|max_length[8]|callback_land_upgrade_form_validation');
        $this->form_validation->set_rules('world_key_input', 'World Key Input', 'trim|required|integer|max_length[10]');

        // Fail
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            if (validation_errors() === '') {
                echo '{"status": "fail", "message": "An unknown error occurred"}';
            }
            echo '{"status": "fail", "message": "'. trim(preg_replace('/\s\s+/', ' ', validation_errors() )) . '"}';
            return false;
        // Success
        } else {
            // Set inputs
            $coord_slug = $this->input->post('coord_slug_input');
            $world_key = $this->input->post('world_key_input');
            $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
            $upgrade_type = $this->input->post('upgrade_type');
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $account_key = $account['id'];

            // Update passive and active army
            $land_type_dictionary = $this->land_type_dictionary();
            $defending_army = $land_type_dictionary[$upgrade_type];
            $new_active_army = $account['active_army'] - $land_type_dictionary[$upgrade_type];
            $query_action = $this->game_model->update_account_active_army($account_key, $new_active_army);
            $new_passive_army = $account['passive_army'] + $land_type_dictionary[$upgrade_type] - $land_type_dictionary[$land_square['land_type']];
            $query_action = $this->game_model->update_account_passive_army($account_key, $new_passive_army);

            // Update land type
            $query_action = $this->game_model->upgrade_land_type($coord_slug, $world_key, $upgrade_type);

            echo '{"status": "success", "message": "Upgraded"}';

            return true;
        }
    }

    // Validate Land Form Callback
    public function land_upgrade_form_validation()
    {
        // User Information
        if (!$this->session->userdata('logged_in')) {
            $this->form_validation->set_message('land_form_validation', 'You are not currently logged in. Please log in again.');
            return false;
        }

        // Get land info for verifying our inputs
        $session_data = $this->session->userdata('logged_in');
        $user_id = $data['user_id'] = $session_data['id'];
        $coord_slug = $this->input->post('coord_slug_input');
        $world_key = $this->input->post('world_key_input');
        $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world_key);
        $upgrade_type = $this->input->post('upgrade_type');
        $account = $this->user_model->get_account_by_keys($user_id, $world_key);
        $account['land_count'] = $this->user_model->get_count_of_account_land($account['id']);
        $account_key = $account['id'];
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
        $land_type_dictionary = $this->land_type_dictionary();

        // Check for inaccuracies
        // Upgrading land that isn't theirs
        if ($land_square['account_key'] != $account_key) {
            $this->form_validation->set_message('land_form_validation', 'This land is no longer yours');
            return false;
        }
        // Doing an upgade they don't have enough active army for
        if ($account['active_army'] < $land_type_dictionary[$upgrade_type]) {
            $this->form_validation->set_message('land_form_validation', 'You don\'t have enought active army for this upgrade');
            return false;
        }
        // Doing an upgade they don't have enough passive army for
        if ($account['land_count'] < $account['passive_army'] + $land_type_dictionary[$upgrade_type] && $upgrade_type != 'village') {
            $this->form_validation->set_message('land_form_validation', 'You don\'t have enought passive army for this upgrade');
            return false;
        }

        // Everything checks out
        return true;
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
            if (strpos($coord, '-180') !== false) {
                $coord = str_replace('-180', '180', $coord);
            }
            if (strpos($coord, '182') !== false) {
                $coord = str_replace('182', '-178', $coord);
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
        $world_key = $world['id'];

        // Land owned
        $leaderboard_land_owned = $this->leaderboard_model->leaderboard_land_owned($world_key);
        $rank = 1;
        foreach ($leaderboard_land_owned as &$leader) { 
            $leader['rank'] = $rank;
            $leader['account'] = $this->user_model->get_account_by_id($leader['account_key']);
            $leader['user'] = $this->user_model->get_user($leader['account']['user_key']);
            // Math for finding approx land area
            $leader['land_mi'] = number_format($leader['COUNT(*)'] * (70 * $world['land_size']));
            $leader['land_km'] = number_format($leader['COUNT(*)'] * (112 * $world['land_size']));
            $rank++;
        }
        $leaderboards['leaderboard_land_owned'] = $leaderboard_land_owned;

        // Return data
        return $leaderboards;
    }

    // Get army update
    public function get_army_update()
    {
        $account_id = $this->input->get('account_id');
        $account = $this->user_model->get_account_by_id($account_id);
        echo $account['active_army'];
    }

    // Land type cost dictionary
    public function land_type_dictionary()
    {
        $land_type_dictionary['village'] = 0;
        $land_type_dictionary['wall'] = 1;
        $land_type_dictionary['tower'] = 2;
        $land_type_dictionary['fort'] = 10;
        $land_type_dictionary['castle'] = 50;
        return $land_type_dictionary;
    }

    // Land type defense dictionary
    public function defense_dictionary()
    {
        $defense_dictionary['unclaimed'] = 0;
        $defense_dictionary['village'] = 10;
        $defense_dictionary['wall'] = 50;
        $defense_dictionary['tower'] = 100;
        $defense_dictionary['fort'] = 1000;
        $defense_dictionary['castle'] = 10000;
        return $defense_dictionary;
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
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }

        // Replace new lines with break tags
        $html = preg_replace("/\r\n|\r|\n/",'<br/>',$html);

        // Return result
        return $html;
    }

}