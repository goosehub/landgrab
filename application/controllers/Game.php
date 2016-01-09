<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {

	function __construct() {
	    parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('transaction_model', '', TRUE);
	    $this->load->model('leaderboard_model', '', TRUE);
	}

	// Map view
	public function index($world_slug = 3)
	{
        // Defaults for unauthenticated users
        $log_check = $data['log_check'] = $data['user_id'] = false;

        // User Information
        if ($this->session->userdata('logged_in')) {
            // Get user data
            $log_check = $data['log_check'] = true;
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
            $data['user'] = $this->user_model->get_user($user_id);
            if (! isset($data['user']['username']) ) {
                redirect('user/logout', 'refresh');
            }
        }

        // Get world
        $world = $data['world'] = $this->game_model->get_world_by_slug_or_id($world_slug);
        if (!$world) {
            $this->load->view('page_not_found', $data);
            return false;
        }
        $world_key = $world['id'];

        if ($log_check) {
            // Get account
            $account = $data['account'] = $this->user_model->get_account_by_keys($user_id, $world['id']);

            // Get recently sold lands
            $data['recently_sold_lands'] = $this->transaction_model->sold_lands_by_account_over_period($account['id'], $account['last_load']);
            // Get last week of sales
            $day_ago = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -1) );
            $data['sold_land_history'] = $this->transaction_model->sold_lands_by_account_over_period($account['id'], $day_ago);

            // 
            // Get account financial information
            // 

            // Taxes and Rebate
            $land_sum_and_count = $this->game_model->get_sum_and_count_of_account_land($account['id']);
            $total_lands = $data['total_lands'] = $land_sum_and_count['count'];

            // If less than 1 land, check if bankruptcy since last page load
            if ($total_lands < 1) { 
                $this->transaction_model->check_for_bankruptcy($account['id'], $account['last_load']); 
            }

            $hourly_taxes = $data['hourly_taxes'] = $land_sum_and_count['sum'] * $world['land_tax_rate'];
            $estimated_rebate = $data['estimated_rebate'] = $world['latest_rebate'] * $land_sum_and_count['count'];
            $income = $data['income'] = $estimated_rebate - $hourly_taxes;
            $data['income_class'] = 'green_money';
            $data['income_prefix'] = '+';
            if ($income < 0) {
                $data['income_prefix'] = '-';
                $data['income_class'] = 'red_money';
            }

            // Set timespan to 7 days
            $timespan_days = 7;

            // Purchases and Sales
            $purchases = $data['purchases'] = $this->transaction_model->get_transaction_purchases($account['id'], $timespan_days);
            $sales = $data['sales'] = $this->transaction_model->get_transaction_sales($account['id'], $timespan_days);
            $yield = $data['yield'] = $sales['sum'] - $purchases['sum'];
            $data['yield_class'] = 'green_money';
            $data['yield_prefix'] = '+';
            if ($yield < 0) {
                $data['yield_prefix'] = '-';
                $data['yield_class'] = 'red_money';
            }

            // Total Profit and Losses
            $losses = $data['losses'] = $this->transaction_model->get_transaction_losses($account['id'], $timespan_days);
            $gains = $data['gains'] = $this->transaction_model->get_transaction_gains($account['id'], $timespan_days);
            $profit = $data['profit'] = $gains['sum'] - $losses['sum'];
            $data['profit_class'] = 'green_money';
            $data['profit_prefix'] = '+';
            if ($profit < 0) {
                $data['profit_prefix'] = '-';
                $data['profit_class'] = 'red_money';
            }

            // Record account as loaded
            $query_action = $this->user_model->account_loaded($account['id']);
        }

        // Get worlds
        $data['worlds'] = $this->user_model->get_all_worlds();

        // Get lands
        $data['lands'] = $this->game_model->get_all_lands_in_world($world['id']);

        // Get leaderboards
        $data['leaderboard_net_value_data'] = $this->leaderboard_model->leaderboard_net_value($world_key);
        // var_dump($data['leaderboard_net_value_data']);
        $data['leaderboard_land_owned_data'] = $this->leaderboard_model->leaderboard_land_owned($world_key);
        $data['leaderboard_cash_owned_data'] = $this->leaderboard_model->leaderboard_cash_owned($world_key);
        $data['leaderboard_highest_valued_land_data'] = $this->leaderboard_model->leaderboard_highest_valued_land($world_key);
        $data['leaderboard_cheapest_land_data'] = $this->leaderboard_model->leaderboard_cheapest_land($world_key);

        // Validation erros
        $data['validation_errors'] = $this->session->flashdata('validation_errors');
        $data['failed_form'] = $this->session->flashdata('failed_form');
        $data['just_registered'] = $this->session->flashdata('just_registered');

        // Load view
        $this->load->view('header', $data);
        $this->load->view('menus', $data);
        $this->load->view('blocks', $data);
        $this->load->view('map_script', $data);
        $this->load->view('interface_script', $data);
        $this->load->view('footer', $data);
	}

	// Get infomation on single land
	public function get_single_land()
	{
        // Get input
        $coord_slug = $_GET['coord_slug'];
        $world_key = $_GET['world_key'];

	    // Get Land Square
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);

        // Add username to array
        $account = $this->user_model->get_account_by_id($land_square['account_key']);
        $owner = $this->user_model->get_user($account['user_key']);
        if ( isset($owner['username']) && isset($land_square['land_name']) ) {
            $land_square['username'] = $owner['username'];
        } else {
            $land_square['username'] = '';
        }

        // Set token for account
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $token = md5(uniqid(rand(), true));
            $query_action = $this->user_model->set_token($account['id'], $token);
            $land_square['token'] = $token;
        } else {
            $land_square['token'] = '';
        }

        // Echo data to client to be parsed
	    if (isset($land_square['land_name'])) {
            // Strip html entities from all untrusted columns, except content as it's stripped on insert
            $land_square['land_name'] = htmlentities($land_square['land_name']);
            $land_square['primary_color'] = htmlentities($land_square['primary_color']);
            $land_square['secondary_color'] = htmlentities($land_square['secondary_color']);
            $land_square['username'] = htmlentities($land_square['username']);
	    	echo json_encode($land_square);
	    // If none found, default to this
	    } else {
	        echo '{"error": "Land not found"}';
	    }
	}

	// Claim unclaimed land
	public function land_form()
    {
        // User Information
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
        }
        else
        {
            $world_key = $this->input->post('world_key_input');
            echo '{"status": "fail", "message": "User not logged in"}';
            return false;
        }

        // Remove cents if exists
        if ( isset($_POST['price']) )
        {
            if (substr($_POST['price'], -3, 1) == '.') {
                $_POST['price'] = substr($_POST['price'], 0, -3);
            }
            // Remove dollarsign from price input if exists
            $_POST['price'] = str_replace('$', '', $_POST['price']);
            // Remove commas from price input if exists
            $_POST['price'] = str_replace(',', '', $_POST['price']);
            // Remove periods from price input if exists (some cultures use periods instead of commas)
            $_POST['price'] = str_replace('.', '', $_POST['price']);
            // Remove dashes to prevent negative inputs in price
            $_POST['price'] = str_replace('-', '', $_POST['price']);
        }
        
		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('form_type_input', 'Form Type Input', 'trim|required|alpha|max_length[8]|callback_land_form_validation');
        $this->form_validation->set_rules('coord_slug_input', 'Coord Key Input', 'trim|required|max_length[8]');
        $this->form_validation->set_rules('world_key_input', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('lng_input', 'Lng Input', 'trim|required|max_length[4]');
        $this->form_validation->set_rules('lat_input', 'Lat Input', 'trim|required|max_length[4]');
        $this->form_validation->set_rules('land_name', 'Land Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('price', 'Price', 'trim|required|integer|max_length[20]');
        $this->form_validation->set_rules('content', 'Content', 'trim|max_length[1000]');
        // $this->form_validation->set_rules('token', 'Token', 'trim|max_length[1000]');

        $world_key = $this->input->post('world_key_input');
        // Fail
	    if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            if (validation_errors() === '') {
                echo '{"status": "fail", "message": "An unknown error occurred"}';
                // validation_errors() === 'An unknown error occurred';
            }
            echo '{"status": "fail", "message": "'. validation_errors() . '"}';
            return false;
		// Success
	    } else {
	    	$claimed = 1;
            $form_type = $this->input->post('form_type_input');
            $coord_slug = $this->input->post('coord_slug_input');
	        $world_key = $this->input->post('world_key_input');
	        $lat = $this->input->post('lat_input');
	        $lng = $this->input->post('lng_input');
	        $land_name = $this->input->post('land_name');
	        $price = $this->input->post('price');
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $account_key = $account['id'];
            $primary_color = $account['primary_color'];

            // Only allow whitelisted tags for content, and add break tags in place of new lines
	        $content = $this->input->post('content');
            $whitelisted_tags = '<a><abbr><acronym><address><area><b><bdo><big><blockquote><br><button><caption><center><cite><code><col><colgroup><dd><del><dfn><dir><div><dl><dt><em><fieldset><font><form><h1><h2><h3><h4><h5><h6><hr><i><img><input><ins><kbd><label><legend><li><map><menu><ol><optgroup><option><p><pre><q><s><samp><select><small><span><strike><strong><sub><sup><table><tbody><td><textarea><tfoot><th><thead><u><tr><tt><u><ul><var>';
            $content = strip_tags(nl2br($content), $whitelisted_tags);

            // Do Database action
	        $query_action = $this->game_model->update_land_data($world_key, $claimed, $coord_slug, $lat, $lng, $account_key, $land_name, $price, $content, $primary_color);
            // Return to map
            echo '{"status": "success"}';
            return true;
	    }
	}

	// Validate Login Callback
	public function land_form_validation($form_type_input)
	{
        // User Information
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
        }

        // Get land info for verifying our inputs
        $form_type = $this->input->post('form_type_input');
        $coord_slug = $this->input->post('coord_slug_input');
        $world_key = $this->input->post('world_key_input');
        $token = $this->input->post('token');
        $buyer_account = $this->user_model->get_account_by_keys($user_id, $world_key);
        $buyer_account_key = $buyer_account['id'];
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);

        // Check if token is correct
        if ($token != $buyer_account['token']) {
            // $this->form_validation->set_message('land_form_validation', 'Token is wrong. Someone else may be using your account.');
            // return false;
        }
        // Check for inaccuracies
        else if ($form_type === 'claim' && $land_square['claimed'] != 0) {
            $this->form_validation->set_message('land_form_validation', 'This land has been claimed');
            return false;
        }
        else if ($form_type === 'update' && $land_square['account_key'] != $buyer_account_key) {
            $this->form_validation->set_message('land_form_validation', 'This land has been bought and is no longer yours');
            return false;
        }
        else if ($form_type === 'buy' && $land_square['account_key'] === $buyer_account_key) {
            $this->form_validation->set_message('land_form_validation', 'This land is already yours');
            return false;
        }
        else if ($form_type === 'buy' && $buyer_account['cash'] < $_POST['price'])
        {
            $this->form_validation->set_message('land_form_validation', 'You don\'t have enough cash to buy this land');
            return false;
        }

        // Do transaction, and return true if transaction succeeds
        $amount = $land_square['price'];
        $name_at_sale = $land_square['land_name'];
        $seller_account_key = $land_square['account_key'];
        return $this->land_transaction($form_type, $world_key, $coord_slug, $amount, $name_at_sale, $seller_account_key, $buyer_account);
	}

    // Process Market Order
    public function market_order()
    {
        // User Information
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
        }
        else
        {
            $world_key = $this->input->post('world_key');
            redirect('world/' . $world_key, 'refresh');
        }

        // Remove cents if exists
        if (substr($_POST['max_price'], -3, 1) == '.') {
            $_POST['max_price'] = substr($_POST['max_price'], 0, -3);
        }
        // Remove dollarsign from max_price input if exists
        $_POST['max_price'] = str_replace('$', '', $_POST['max_price']);
        // Remove commas from max_price input if exists
        $_POST['max_price'] = str_replace(',', '', $_POST['max_price']);
        // Remove periods from max_price input if exists (some cultures use periods instead of commas)
        $_POST['max_price'] = str_replace('.', '', $_POST['max_price']);
        // Remove dashes to prevent negative inputs in max_price
        $_POST['max_price'] = str_replace('-', '', $_POST['max_price']);
        
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('token', 'Token', 'trim|max_length[1000]|callback_market_order_validation');
        $this->form_validation->set_rules('max_lands', 'Max Lands', 'trim|required|integer|max_length[20]|greater_than[0]');
        $this->form_validation->set_rules('max_price', 'Max Price', 'trim|required|integer|max_length[20]|greater_than[-1]');
        $this->form_validation->set_rules('world_key', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('min_lat', 'Min Lat', 'trim|required|max_length[4]|greater_than[-85]|less_than[85]');
        $this->form_validation->set_rules('max_lat', 'Max Lat', 'trim|required|max_length[4]|greater_than[-85]|less_than[85]');
        $this->form_validation->set_rules('min_lng', 'Min Lng', 'trim|required|max_length[4]|greater_than[-181]|less_than[181]');
        $this->form_validation->set_rules('max_lng', 'Max Lng', 'trim|required|max_length[4]|greater_than[-181]|less_than[181]');

        $this->form_validation->set_rules('new_land_name', 'Land Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('new_price', 'Price', 'trim|required|integer|max_length[20]');
        $this->form_validation->set_rules('new_content', 'Content', 'trim|max_length[1000]');

        $world_key = $this->input->post('world_key');
        // Fail
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            redirect('world/' . $world_key, 'refresh');
        // Success
        } else {
            $claimed = 1;
            $world_key = $this->input->post('world_key');
            $max_lands = $this->input->post('max_lands');
            $max_price = $this->input->post('max_price');
            $min_lat = $this->input->post('min_lat');
            $max_lat = $this->input->post('max_lat');
            $min_lng = $this->input->post('min_lng');
            $max_lng = $this->input->post('max_lng');
            $new_land_name = $this->input->post('new_land_name');
            $new_price = $this->input->post('new_price');
            $new_content = $this->input->post('new_content');
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $account_key = $account['id'];
            $primary_color = $account['primary_color'];

            // Only allow whitelisted tags for content, and add break tags in place of new lines
            $new_content = $this->input->post('new_content');
            $whitelisted_tags = '<a><abbr><acronym><address><area><b><bdo><big><blockquote><br><button><caption><center><cite><code><col><colgroup><dd><del><dfn><dir><div><dl><dt><em><fieldset><font><form><h1><h2><h3><h4><h5><h6><hr><i><img><input><ins><kbd><label><legend><li><map><menu><ol><optgroup><option><p><pre><q><s><samp><select><small><span><strike><strong><sub><sup><table><tbody><td><textarea><tfoot><th><thead><u><tr><tt><u><ul><var>';
            $new_content = strip_tags(nl2br($new_content), $whitelisted_tags);

            // Do Database action
            $market_order_lands = $this->game_model->market_order_select($world_key, $account_key, $max_lands, $max_price, $min_lat, $max_lat, $min_lng, $max_lng);
            $claimed = 1;
            foreach ($market_order_lands as $land) {
                $coord_slug = $land['coord_slug'];
                $lat = $land['lat'];
                $lng = $land['lng'];
                $amount = $land['price'];
                $name_at_sale = $land['land_name'];
                $seller_account_key = $land['account_key'];
                $buyer_account = $account;
                if ($land['claimed'] === '0') {
                    $transaction_type = 'claim';
                } else {
                    $transaction_type = 'buy';
                }
                // Do transaction
                $transaction_result = $this->land_transaction($transaction_type, $world_key, $coord_slug, $amount, $name_at_sale, $seller_account_key, $buyer_account);
                $account = $this->user_model->get_account_by_keys($user_id, $world_key);

                // If transaction successful, give land to user
                if ($transaction_result) {
                    $query_action = $this->game_model->update_land_data($world_key, $claimed, $coord_slug, $lat, $lng, $account_key, 
                        $new_land_name, $new_price, $new_content, $primary_color);
                } else {
                    $this->load->view('page_not_found');
                    return false;
                }
            }
            // Return to map
            redirect('world/' . $world_key, 'refresh');
        }
    }

    // Validate Market Order Form
    public function market_order_validation($token)
    {
        // User Information
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $data['user_id'] = $session_data['id'];
        }
        $world_key = $this->input->post('world_key');
        $account = $this->user_model->get_account_by_keys($user_id, $world_key);

        // Verify token
/*        if ($token != $account['token']) {
            $this->form_validation->set_message('market_order_validation', 'Token is wrong. Someone else may be using your account.');
            return false;
        }*/

        $max_lands = $this->input->post('max_lands');
        $max_price = $this->input->post('max_price');
        $potential_max_cost = $max_price * $max_lands;
        if ($potential_max_cost > $account['cash']) {
            $this->form_validation->set_message('market_order_validation', 'You don\'t have enough cash to place that order.');
            return false;
        }

        // Return validation true if not returned false yet
        return true;
    }
    // Land Transaction
    public function land_transaction($transaction_type, $world_key, $coord_slug, $amount, $name_at_sale, $seller_account_key, $buyer_account)
    {
        // Do transaction
        $new_buying_owner_cash = $buyer_account['cash'] - $amount;
        $buyer_account_key = $buyer_account['id'];
        if ($transaction_type === 'buy')
        {
            // Get seller and buying party info
            $seller_account = $this->user_model->get_account_by_id($seller_account_key);

            // Find new cash balances
            $new_selling_owner_cash = $seller_account['cash'] + $amount;

            // Do sale
            $query_action = $this->game_model->update_account_cash_by_account_id($seller_account_key, $new_selling_owner_cash);
            $query_action = $this->game_model->update_account_cash_by_account_id($buyer_account_key, $new_buying_owner_cash);

            // Record into transaction log
            $query_action = $this->transaction_model->new_transaction_record($buyer_account_key, $seller_account_key, $transaction_type, 
                $amount, $world_key, $coord_slug, $name_at_sale, '');
        }

        // Record into transaction log
        if ($transaction_type === 'claim') {
            $query_action = $this->game_model->update_account_cash_by_account_id($buyer_account_key, $new_buying_owner_cash);
            $query_action = $this->transaction_model->new_transaction_record($buyer_account_key, $seller_account_key, $transaction_type, 
                $amount, $world_key, $coord_slug, '', '');
            $query_action = $this->transaction_model->add_to_world_bank($world_key, $amount);
        }
        return true;
    }
}