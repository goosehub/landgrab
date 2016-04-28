<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {

	function __construct() {
	    parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('tax_model', '', TRUE);
        $this->load->model('transaction_model', '', TRUE);
	    $this->load->model('leaderboard_model', '', TRUE);
	}

	// Game view and update json
	public function index($world_slug = 5, $marketing_slug = false)
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

            // Get account sales
            $data['sales'] = $this->sales($account);

            // Get account financials
            $data['financials'] = $this->financials($account, $world);

            // Record account as loaded
            $query_action = $this->user_model->account_loaded($account['id']);
        }

        // Get world leaderboards
        $data['leaderboards'] = $this->leaderboards($world);

        // Get all worlds
        $data['worlds'] = $this->user_model->get_all_worlds();

        // Get all lands
        $data['lands'] = $this->game_model->get_all_lands_in_world($world['id']);

        // Auctions
        $data['auctions'] = $this->game_model->get_active_auctions($world['id']);
        foreach ($data['auctions'] as &$auction) {
            $auction['land_data'] = $this->game_model->get_single_land($world['id'], $auction['coord_slug']);
            $auction['land_data']['land_name'] .= ' (' . $auction['land_data']['coord_slug'] . ')';
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

        // Auction Interface
        if (isset($_GET['land']) && isset($_GET['auction'])) {
            $data['auction_data'] = $this->game_model->get_auction_info($_GET['auction']);
            $data['auction_data']['land'] = $this->get_single_land($data['auction_data']['coord_slug'], $data['auction_data']['world_key']);
            $data['auction_data']['account'] = $this->user_model->get_account_by_id($data['auction_data']['current_bid_account_key']);
            $data['auction_data']['user'] = $this->user_model->get_user($data['auction_data']['account']['user_key']);
            $data['auction_data']['current_bid_username'] = $data['auction_data']['user']['username'];
            $data['auction_data']['auction_time_left'] = (strtotime($data['auction_data']['last_bid_timestamp']) + 300) - time();
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
        } else if (isset($_GET['coord_slug']) && isset($_GET['world_key']) ) {
            $json_output = true;
            $coord_slug = $_GET['coord_slug'];
            $world_key = $_GET['world_key'];
        } else {
            echo '{"error": "Input missing"}';
            return false;
        }

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
            $land_square['username'] = htmlentities($land_square['username']);
            if ($json_output) {
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

	// Trade and Update Land
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

        // Deformat inputs
        $_POST['price'] = $this->money_deformat($_POST['price']);
        
		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('form_type_input', 'Form Type Input', 'trim|required|alpha|max_length[8]|callback_land_form_validation');
        $this->form_validation->set_rules('coord_slug_input', 'Coord Key Input', 'trim|required|max_length[8]');
        $this->form_validation->set_rules('world_key_input', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('land_name', 'Land Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('price', 'Price', 'trim|required|integer|max_length[20]');
        $this->form_validation->set_rules('content', 'Content', 'trim|max_length[1000]');
        // $this->form_validation->set_rules('token', 'Token', 'trim|max_length[1000]');

        // Fail
	    if ($this->form_validation->run() == FALSE) {

            // Set Fail Errors
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            if (validation_errors() === '') {
                echo '{"status": "fail", "message": "An unknown error occurred"}';
            }

            // Return to game as failure with new lines removed
            echo '{"status": "fail", "message": "'. trim(preg_replace('/\s\s+/', ' ', validation_errors() )) . '"}';
            return false;

		// Success
	    } else {

            // Set inputs
	    	$claimed = 1;
            $form_type = $this->input->post('form_type_input');
            $coord_slug = $this->input->post('coord_slug_input');
	        $world_key = $this->input->post('world_key_input');
	        $land_name = $this->input->post('land_name');
            $price = $this->input->post('price');
            $account = $this->user_model->get_account_by_keys($user_id, $world_key);
            $account_key = $account['id'];
            $primary_color = $account['primary_color'];
            $content = $this->input->post('content');
            // $content = $this->sanitize_html($content);
            $content = $content;

            // Do Database action
	        $query_action = $this->game_model->update_land_data($world_key, $claimed, $coord_slug, $account_key, $land_name, $price, $content, $primary_color);

            // Return to game as success
            echo '{"status": "success"}';
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
        $token = $this->input->post('token');
        $buyer_account = $this->user_model->get_account_by_keys($user_id, $world_key);
        $buyer_account_key = $buyer_account['id'];
        $buyer_user = $this->user_model->get_user($buyer_account_key);
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
        $amount = $land_square['price'];
        $name_at_sale = $land_square['land_name'];
        $seller_account_key = $land_square['account_key'];
        $seller_user = $this->user_model->get_user($seller_account_key);

        // Check for bot patterns and/or consolidation trading
        // $bot_check = $this->bot_detection($buyer_user, $seller_user, $buyer_account_key, $seller_account_key, $amount);
        // if ($bot_check) {
        //     echo '{"error": "' . $bot_check . '"}';
        //     die();
        // }

        // Check if token is correct
        // if ($token != $buyer_account['token']) {
            // $this->form_validation->set_message('land_form_validation', 'Token is wrong. Someone else may be using your account.');
            // return false;
        // }
        // Check for inaccuracies
        if ($form_type === 'claim' && $land_square['claimed'] != 0) {
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
        } else if ($this->game_model->check_if_land_is_active_auction($coord_slug, $world_key)) {
            $this->form_validation->set_message('land_form_validation', 'This land is currently up for auction');
            return false;
        }

        // Do transaction, and return true if transaction succeeds
        return $this->land_transaction($form_type, $world_key, $coord_slug, $amount, $name_at_sale, $seller_account_key, $buyer_account);
	}

    // Land Transaction
    public function land_transaction($transaction_type, $world_key, $coord_slug, $amount, $name_at_sale, $seller_account_key, $buyer_account)
    {
        // Do transaction
        $new_buying_owner_cash = $buyer_account['cash'] - $amount;
        $buyer_account_key = $buyer_account['id'];
        if ($transaction_type === 'buy') {

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
        }
        return true;
    }

    // Look for bot patterns
    public function bot_detection($buyer_user, $seller_user, $buyer_account_key, $seller_account_key, $amount)
    {
        // Note
        // The methods are kept seperate to allow easy configuration as bot fighting evolves
        // For speed and resource reasons, be sure to make conditions to do a pattern check minimal

        // 
        // Search for suspicious number sales between the same ip, indicating consolidation accounts
        // 

        // Do check when ips match between buyer and seller
        if ($buyer_user['ip'] === $seller_user['ip']) {
            // Get sales to search for bot patterns in the last hour
            $search_in_hours = 2;
            $start_search = date('Y-m-d H:i:s', time() - (60 * 60 * $search_in_hours));
            $found_suspicious_sales = 0;
            $sales_search = $this->transaction_model->sold_lands_by_account_over_period($seller_account_key, $start_search);

            // Not likely a bot if only a few sales
            $total_sale_min = 3;
            if ( count($sales_search) <= $total_sale_min) {
                return false;
            }

            foreach ($sales_search as $sale) {
                $paying_account = $this->user_model->get_account_by_id($sale['paying_account_key']);
                $recipient_account = $this->user_model->get_account_by_id($sale['recipient_account_key']);
                $paying_user = $this->user_model->get_user($paying_account['user_key']);
                $recipient_user = $this->user_model->get_user($recipient_account['user_key']);
                if ($paying_user['ip'] === $recipient_user['ip']) {
                    $found_suspicious_sales++;
                }
            }
            $suspicious_ratio_limit = 0.5;
            $suspicious_total_limit = count($sales_search) * $suspicious_ratio_limit;

            // Log ip, send message, and return false if suspicion is over the limit
            if ($found_suspicious_sales > $suspicious_total_limit) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $result = $this->user_model->record_ip_request($ip, 'suspicious_sales_by_ip');   

                return 'You seem to be a bot, or using muliple accounts. If this was a mistake, please contact me at goosepostbox@gmail.com.';
            }
        }

        // 
        // Search for frequent sales of a suspicious amount, indicating consolidation accounts
        // 

        // Do check when current sale is in the suspicious range
        $suspicious_max = 100000;
        $suspicious_min = 90000;
        if ($amount <= $suspicious_max && $amount >= $suspicious_min) {
            // If same IP as well, mark as bot
            if ($buyer_user['ip'] === $seller_user['ip']) {
                return 'You seem to be a bot, or using muliple accounts. If this was a mistake, please contact me at goosepostbox@gmail.com.';
            }

            // Get sales to search for bot patterns in the last hour
            $search_in_hours = 2;
            $start_search = date('Y-m-d H:i:s', time() - (60 * 60 * $search_in_hours));
            $sales_search = $this->transaction_model->sold_lands_by_account_over_period($account_key, $start_search);

            // Not likely a bot if only a few sales
            $total_sale_min = 3;
            if ( count($sales_search) <= $total_sale_min) {
                return false;
            }

            // Check for bot behavior
            $found_suspicious_sales = 0;
            foreach ($sales_search as $sale) {
                if ($sale['amount'] <= $suspicious_max && $sale['amount'] >= $suspicious_min) {
                    $found_suspicious_sales++;
                }
            }
            $suspicious_ratio_limit = 0.5;
            $suspicious_total_limit = count($sales_search) * $suspicious_ratio_limit;

            // Log ip, send message, and return false if suspicion is over the limit
            if ($found_suspicious_sales > $suspicious_total_limit) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $result = $this->user_model->record_ip_request($ip, 'suspicious_sales_by_amount');   

                return 'You seem to be a bot, or using muliple accounts. If this was a mistake, please contact me at goosepostbox@gmail.com.';
            }

        }

        // If here, pass and return false
        return false;
    }

    // Get Sales
    public function sales($account)
    {
        $this->load->helper('date');

        $account_key = $account['id'];

        // Get lands since last update
        $sales_since_last_update = $this->transaction_model->sold_lands_by_account_over_period($account_key, $account['last_load']);

        // Get sales history
        $day_ago = date('Y-m-d H:i:s', time() - (60 * 60 * 24 * 1) );
        $sales_history = $this->transaction_model->sold_lands_by_account_over_period($account_key, $day_ago);

        foreach ($sales_history as &$transaction) {
            // Add usernames to sales history
            $paying_account = $this->user_model->get_account_by_id($transaction['paying_account_key']);
            $paying_user = $this->user_model->get_user($paying_account['user_key']);
            $transaction['paying_username'] = $paying_user['username'];
            // Format time
            $transaction['when'] = timespan(strtotime($transaction['created']), time());
        }
        $sales['sales_history'] = $sales_history;

        // Add usernames to sales since last update
        foreach ($sales_since_last_update as &$transaction) {
            $paying_account = $this->user_model->get_account_by_id($transaction['paying_account_key']);
            $paying_user = $this->user_model->get_user($paying_account['user_key']);
            $transaction['paying_username'] = $paying_user['username'];
        }
        $sales['sales_since_last_update'] = $sales_since_last_update;

        // Return data
        return $sales;
    }

    // Get Financials
    public function financials($account, $world)
    {
        $account_key = $account['id'];

        // Get account information
        $financials['cash'] = $account['cash'];
        $land_sum_and_count = $this->game_model->get_sum_and_count_of_account_land($account_key);
        $player_land_count = $financials['player_land_count'] = $land_sum_and_count['count'];

        // Check if bankruptcy since last page load
        $financials['bankruptcy'] = false;
        // if ($player_land_count < 1) { 
            $financials['bankruptcy'] = $this->transaction_model->check_for_bankruptcy($account_key, $account['last_load']); 
        // }

        // Set timespan days, match in financial menu language
        $timespan_days = 1;

        // Unique Sales
        $unique_sales = $this->tax_model->get_account_unique_sales_tally($account_key);
        $unique_sales = count($unique_sales);
        $financials['unique_sales'] = $unique_sales;

        // Monopoly Tax
        $monopoly_tax = floor($land_sum_and_count['count'] / 100) * floor($land_sum_and_count['count'] / 100) * 10;
        $financials['monopoly_tax'] = $monopoly_tax;

        // Owned Cities
        $owned_cities = $this->tax_model->get_owned_cities($account_key);
        $owned_cities = $financials['owned_cities'] = $owned_cities[0]['owned_cities'];

        // Income
        // $city_bonus = $owned_cities * 60 * 24;
        $city_bonus = 0;
        $periodic_taxes = $financials['periodic_taxes'] = ( ($land_sum_and_count['sum'] * $world['land_tax_rate']) - $monopoly_tax) * 60 * 24;
        $periodic_rebate = $financials['periodic_rebate'] = ( ($world['land_rebate'] * $land_sum_and_count['count']) + $unique_sales + $city_bonus) * 60 * 24;
        $income = $financials['income'] = $periodic_rebate - $periodic_taxes;
        $financials['income_class'] = 'green_money';
        $financials['income_prefix'] = '+';
        if ($income < 0) {
            $financials['income_class'] = 'red_money';
            $financials['income_prefix'] = '-';
        }

        // Trades
        $purchases = $financials['purchases'] = $this->transaction_model->get_transaction_purchases($account_key, $timespan_days);
        $sales = $financials['sales'] = $this->transaction_model->get_transaction_sales($account_key, $timespan_days);
        $trades_profit = $financials['trades_profit'] = $sales['sum'] - $purchases['sum'];
        $financials['trades_profit_class'] = 'green_money';
        $financials['trades_profit_prefix'] = '+';
        if ($trades_profit < 0) {
            $financials['trades_profit_class'] = 'red_money';
            $financials['trades_profit_prefix'] = '-';
        }
/*        
        // Balance
        $losses = $financials['losses'] = $this->transaction_model->get_transaction_losses($account_key, $timespan_days);
        $gains = $financials['gains'] = $this->transaction_model->get_transaction_gains($account_key, $timespan_days);
        $profit = $financials['profit'] = $gains['sum'] - $losses['sum'];
        $financials['profit_class'] = 'green_money';
        $financials['profit_prefix'] = '+';
        if ($profit < 0) {
            $financials['profit_class'] = 'red_money';
            $financials['profit_prefix'] = '-';
        }
*/
        // Temporary loses
        $losses['sum'] = $financials['losses']['sum'] = 0;
        $gains['sum'] = $financials['gains']['sum'] = 0;
        $profit = $financials['profit'] = $gains['sum'] - $losses['sum'];
        $financials['profit_class'] = 'green_money';
        $financials['profit_prefix'] = '+';
        if ($profit < 0) {
            $financials['profit_class'] = 'red_money';
            $financials['profit_prefix'] = '-';
        }

        // Set nulls to 0
        $purchases['sum'] = $financials['purchases']['sum'] = is_null($purchases['sum']) ? 0 : $purchases['sum'];
        $purchases['sum'] = $financials['purchases']['sum'] = is_null($purchases['sum']) ? 0 : $purchases['sum'];
        $sales['sum'] = $financials['sales']['sum'] = is_null($sales['sum']) ? 0 : $sales['sum'];
        $losses['sum'] = $financials['losses']['sum'] = is_null($losses['sum']) ? 0 : $losses['sum'];
        $gains['sum'] = $financials['gains']['sum'] = is_null($gains['sum']) ? 0 : $gains['sum'];

        // Return data
        return $financials;
    }

    // Get leaderboards
    public function leaderboards($world)
    {
        $world_key = $world['id'];

        // Net Value
        // $data['leaderboard_net_value_data'] = $this->leaderboard_model->leaderboard_net_value($world_key);

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

        // Cash owned
        $leaderboard_cash_owned = $this->leaderboard_model->leaderboard_cash_owned($world_key);
        $rank = 1;
        foreach ($leaderboard_cash_owned as &$leader) { 
            $leader['rank'] = $rank;
            $leader['user'] = $this->user_model->get_user($leader['user_key']);
            $rank++;
        }
        $leaderboards['leaderboard_cash_owned'] = $leaderboard_cash_owned;

        // Highest value land
        $leaderboard_highest_valued_land = $this->leaderboard_model->leaderboard_highest_valued_land($world_key);
        $rank = 1;
        foreach ($leaderboard_highest_valued_land as &$leader) {
            $leader['rank'] = $rank;
            $leader['account'] = $this->user_model->get_account_by_id($leader['account_key']);
            $leader['user'] = $this->user_model->get_user($leader['account']['user_key']);
            $leader['content'] = mb_substr(strip_tags($leader['content']), 0, 42);
            if (strlen(strip_tags($leader['content'])) === 42) { 
                $leader['content'] .= '...'; 
            } 
            $rank++;
        }
        $leaderboards['leaderboard_highest_valued_land'] = $leaderboard_highest_valued_land;

        // Cheapest land
        $leaderboard_cheapest_land = $this->leaderboard_model->leaderboard_cheapest_land($world_key);
        $rank = 1;
        foreach ($leaderboard_cheapest_land as &$leader) {
            $leader['rank'] = $rank;
            $leader['account'] = $this->user_model->get_account_by_id($leader['account_key']);
            $leader['user'] = $this->user_model->get_user($leader['account']['user_key']);
            $leader['content'] = mb_substr(strip_tags($leader['content']), 0, 42);
            if (strlen(strip_tags($leader['content'])) === 42) { 
                $leader['content'] .= '...'; 
            }
            $rank++;
        }
        $leaderboards['leaderboard_cheapest_land'] = $leaderboard_cheapest_land;

        // Return data
        return $leaderboards;
    }

    public function money_deformat($string) {
        if ( !isset($string) ) {
            return '';
        }
        // Detect cents, and remove if exists
        if (substr($string, -3, 1) == '.') {
            $string = substr($string, 0, -3);
        }
        // Remove dollarsign from price input if exists
        $string = str_replace('$', '', $string);
        // Remove commas from price input if exists
        $string = str_replace(',', '', $string);
        // Remove periods from price input if exists (some cultures use periods instead of commas)
        $string = str_replace('.', '', $string);
        // Remove dashes to prevent negative inputs in price
        $string = str_replace('-', '', $string);
        return $string;
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

    // Update auction information
    public function auction_update()
    {
        // Set data
        $auction = $this->game_model->get_auction_info($_GET['auction_id']);
        $auction['land'] = $this->get_single_land($auction['coord_slug'], $auction['world_key']);
        $auction['account'] = $this->user_model->get_account_by_id($auction['current_bid_account_key']);
        $auction['user'] = $this->user_model->get_user($auction['account']['user_key']);
        $auction['current_bid_username'] = $auction['user']['username'];
        $auction['auction_time_left'] = (strtotime($auction['last_bid_timestamp']) + 300) - time();
        $auction_id = $auction['id'];
        $world_key = $auction['world_key'];
        $coord_slug = $auction['coord_slug'];
        $land_name = $auction['land']['land_name'];
        $claimed = 1;
        $price = $auction['current_bid'];
        $content = $auction['land']['content'];

        // Encode and send data
        function filter(&$value) {
          $value = nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }
        array_walk_recursive($auction, "filter");
        echo json_encode($auction);
        return true;

        // End auction if auction is over
        if ($auction['auction_time_left'] < 1) {
            // Do transaction
            $amount = $auction['current_bid'];
            $buyer_account = $this->user_model->get_account_by_id($auction['current_bid_account_key']);
            $new_buying_owner_cash = $buyer_account['cash'] - $amount;
            $buyer_account_key = $buyer_account['id'];
            $primary_color = $buyer_account['primary_color'];

            // Get seller and buying party info
            $seller_account_key = $auction['seller_account_key'];
            $seller_account = $this->user_model->get_account_by_id($seller_account_key);

            // Find new cash balances
            $new_selling_owner_cash = $seller_account['cash'] + $amount;

            // Do sale
            $query_action = $this->game_model->update_account_cash_by_account_id($seller_account_key, $new_selling_owner_cash);
            $query_action = $this->game_model->update_account_cash_by_account_id($buyer_account_key, $new_buying_owner_cash);

            // Record into transaction log
            $query_action = $this->transaction_model->new_transaction_record($buyer_account_key, $seller_account_key, 'buy', 
                $amount, $world_key, $coord_slug, $land_name, '');

            // Update land
            $query_action = $this->game_model->update_land_data($world_key, $claimed, $coord_slug, $buyer_account_key, $land_name, $price, $content, $primary_color);

            // Update auction
            return true;
        }
    }

    // Make square into city
    public function make_city()
    {
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('coord_slug', 'Coord Slug', 'trim|required|max_length[8]|callback_make_city_validation');
        $this->form_validation->set_rules('world_key', 'World Key', 'trim|required|integer|max_length[10]');

        // Fail
        if ($this->form_validation->run() == FALSE) {
            // Set Fail Errors
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            if (validation_errors() === '') {
                echo '{"status": "fail", "message": "An unknown error occurred"}';
            }

            // Return to game as failure with new lines removed
            echo '{"status": "fail", "message": "'. trim(preg_replace('/\s\s+/', ' ', validation_errors() )) . '"}';
            return false;

        // Success
        } else {
            // Return to game as success
            echo '{"status": "success"}';
            return true;
        }
    }

    // Validate make city request
    public function make_city_validation()
    {
        // User Information
        if (!$this->session->userdata('logged_in')) {
            return false;
        }
        $session_data = $this->session->userdata('logged_in');
        $user_id = $data['user_id'] = $session_data['id'];

        // Get Data
        $coord_slug = $this->input->post('coord_slug');
        $world_key = $this->input->post('world_key');
        $buyer_account = $this->user_model->get_account_by_keys($user_id, $world_key);
        $buyer_account_key = $buyer_account['id'];
        $buyer_user = $this->user_model->get_user($buyer_account_key);
        $land_square = $this->game_model->get_single_land($world_key, $coord_slug);
        $amount = 100000;
        $new_buying_owner_cash = $buyer_account['cash'] - $amount;
        $buyer_account_key = $buyer_account['id'];

        // Check that this is the proper owner
        if ($buyer_account_key != $land_square['account_key']) {
            return false;
        }

        // Check if active auction
        if ($this->game_model->check_if_land_is_active_auction($coord_slug, $world_key) ) {
            return false;
        }

        // Apply charge for auction
        $query_action = $this->game_model->update_account_cash_by_account_id($buyer_account_key, $new_buying_owner_cash);

        // Make land into city
        $query_action = $this->game_model->make_land_into_city($coord_slug, $world_key);

        return true;
    }

}