<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class User extends CI_Controller {

    // This is to only be used in difficult to replicate debugging cases 
    protected $password_override = false;

    // Makes it so passwords will be generated if left empty
    protected $password_optional = true;

    // Limits
    protected $login_limit_window = 30;
    protected $login_limit = 10;

    // Minutes between registering
    protected $ip_frequency_register = 60;

	function __construct() {
	    parent::__construct();
        $this->load->model('user_model', '', TRUE);
	    $this->load->model('game_model', '', TRUE);
	}

	// Unused index action
	public function index()
	{
	}

	// Login
	public function login()
	{
        // Check if this is ip has failed login too many times
        $ip = $_SERVER['REMOTE_ADDR'];
        $timestamp = date('Y-m-d H:i:s', time() - $this->login_limit_window * 60);
        $ip_fails = $this->user_model->check_ip_request_since_timestamp($ip, 'login', $timestamp);
        if (count($ip_fails) > $this->login_limit && !is_dev()) {
            echo 'Too many login attempts from this IP. Please wait ' . $this->login_limit_window . ' minutes.';
            die();
        }

		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[32]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[64]|callback_login_validation');
        
        $world_key = $this->input->post('world_key');
        $world = $this->game_model->get_world_by_id($world_key);
		// Fail
        if ($this->form_validation->run() == FALSE) {
            // Record failed request
            $result = $this->user_model->record_ip_request($ip, 'login');   

            // Set fail message and redirect to map
        	$this->session->set_flashdata('failed_form', 'login');
        	$this->session->set_flashdata('validation_errors', validation_errors());
            redirect(base_url() . 'world/' . $world['slug'], 'refresh');
		// Success
        } else {
            redirect(base_url() . 'world/' . $world['slug'], 'refresh');
        }
	}

	// Validate Login Callback
	public function login_validation($password)
	{
		// Get other parameters
        $username = $this->input->post('username');

        // Compare to database
        $user = $this->user_model->login($username, $password);

        // Username not found
        if (!$user) {
            $this->form_validation->set_message('login_validation', 'Invalid username or password');
            return false;
        }
        // Password does not match
        else if (!$this->password_override && !password_verify($password, $user['password'])) {
            $this->form_validation->set_message('login_validation', 'Invalid username or password');
            return false;
        }
		// Success
        $this->user_model->create_login_session($user['id'], $user['username']);
        return true;
	}

	// Register
	public function register()
	{
        // Optional password
        $matches = 'matches[confirm]|';
        if ($this->password_optional) {
            if (!isset($_POST['password']) || $_POST['password'] === '') {
                $random_password = mt_rand(10000000,99999999); ;
                $_POST['password'] = $random_password;
                $_POST['confirm'] = $random_password;
                $matches = '';
            }
        }

		// Validation
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[64]|' . $matches . 'callback_register_validation');
        $this->form_validation->set_rules('confirm', 'Confirm', 'trim|required');

        $world_key = $this->input->post('world_key');
        $world = $this->game_model->get_world_by_id($world_key);
        // Fail
        if ($this->form_validation->run() == FALSE) {
        	$this->session->set_flashdata('failed_form', 'register');
        	$this->session->set_flashdata('validation_errors', validation_errors());
            redirect(base_url() . 'world/' . $world['slug'], 'refresh');
        // Success
        } else {
            $this->session->set_flashdata('just_registered', true);
            redirect(base_url() . 'world/' . $world['slug'], 'refresh');
        }
	}

	// Validate Register Callback
    public function register_validation($password) {
        // Set parameters
        $email = 'placeholder@gmail.com';
        $username = $this->input->post('username');
        $ab_test = $this->input->post('ab_test');
        // Email Validation
        $this->load->helper('email');
        if (!valid_email($email)) {
            $this->form_validation->set_message('register_validation', 'This is not a working email address');
            return false;
        }
        // Attempt new user register
        $facebook_id = 0;
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = $this->user_model->register($username, $password, $email, $facebook_id, $ip, $this->ip_frequency_register, $ab_test);
        // Fail
        if ($user_id === 'ip_fail') {
            $this->form_validation->set_message('register_validation', 'This IP has already registered in the last ' . $this->ip_frequency_register . ' minutes');
            return false;
        }
        if (!$user_id) {
            $this->form_validation->set_message('register_validation', 'Username already exists');
            return false;
        }

        $this->user_model->create_login_session($user_id, $username);

        return true;
    }

	// Logout
    public function logout() {
        $this->session->unset_userdata('user');
        redirect('?login', 'refresh');
    }

    // Page Not Found
	public function page_not_found()
	{
		$this->load->view('errors/page_not_found');
	}

    // Update account information
    public function update_account_info()
    {
        // User Information
        if (!$this->session->userdata('user')) {
            echo 'You must be logged in.';
            return false;
        }

        $world_key = $this->input->post('world_key');
        $world = $this->game_model->get_world_by_id($world_key);
        $account = $this->user_model->this_account($world_key);
        
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('world_key', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('nation_color', 'Nation Color', 'trim|required|max_length[7]');
        $this->form_validation->set_rules('cash_crop_key', 'Cash Crop', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('nation_name', 'Nation Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('nation_flag', 'Nation Flag', 'trim|max_length[500]');
        $this->form_validation->set_rules('leader_portrait', 'Leader Portrait', 'trim|max_length[500]');
        $this->form_validation->set_rules('existing_nation_flag', 'Existing Nation Flag', 'trim|max_length[500]');
        $this->form_validation->set_rules('existing_leader_portrait', 'Existing Leader Portrait', 'trim|max_length[500]');

        // Form Validation Fail
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            redirect('world/' . $world['slug'], 'refresh');
            return false;
        }

        // Image upload Config
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size']      = '1000000';
        $config['max_width']     = '5000';
        $config['max_height']    = '5000';
        $config['encrypt_name']  = TRUE;
        $this->load->library('upload', $config);

        // Nation flag
        $nation_flag = $this->input->post('existing_nation_flag');
        if ( $_FILES['nation_flag']['name'] && !$this->upload->do_upload('nation_flag') ) {
            $this->session->set_flashdata('validation_errors', $this->upload->display_errors());
            echo $this->upload->display_errors();
            return false;
        }
        else if ($_FILES['nation_flag']['name']) {
            $file = $this->upload->data();
            $nation_flag = $file['file_name'];
        }

        // Leader Portriat
        $leader_portrait = $this->input->post('existing_leader_portrait');
        if ( $_FILES['leader_portrait']['name'] && !$this->upload->do_upload('leader_portrait') ) {
            echo $this->upload->display_errors();
            return false;
        }
        else if ($_FILES['leader_portrait']['name']) {
            $file = $this->upload->data();
            $leader_portrait = $file['file_name'];
        }

        // Set non image data
        $nation_color = $this->input->post('nation_color');
        $cash_crop_key = $this->input->post('cash_crop_key');
        $nation_name = $this->input->post('nation_name');
        $redirect_lng = $this->input->post('redirect_lng');
        $redirect_lat = $this->input->post('redirect_lat');

        $cash_crop_key = $this->enforce_cash_crop_key($account, $cash_crop_key);

        // Add hash to color if needed
        if (0 !== strpos($nation_color, '#')) {
            $nation_color = '#' . $nation_color;
        }

        // Validate color
        // http://stackoverflow.com/a/13392880/3774582
        if ( !preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $nation_color) ) {
            echo $nation_color . ' is not a valid hex color';
            return false;
        }

        // Set account
        $account_key = $account['id'];
        $this->user_model->update_account_info($account_key, $nation_color, $nation_name, $nation_flag, $leader_portrait, $cash_crop_key);

        // Redirect to game
        $redirect_string = $redirect_lng ? '?lng=' . $redirect_lng . '&lat=' . $redirect_lat : '';
        redirect('world/' . $world_key . $redirect_string, 'refresh');
    }

    public function enforce_cash_crop_key($account, $cash_crop_key)
    {
        $supplies = $this->game_model->get_account_supplies($account['id']);
        foreach ($supplies as $key => $supply) {
            $account['supplies'][$supply['slug']] = $supply;
        }
        if ($account['supplies']['tiles'] > 0) {
            $cash_crop_key = $account['cash_crop_key'];
        }
        return $cash_crop_key;
    }

    public function update_tutorial($world_key, $tutorial)
    {
        $account = $this->user_model->this_account($world_key);
        if ($account['tutorial'] > 8) {
            return;
        }
        $this->user_model->update_user_tutorial($account['user_key'], $tutorial);
        api_response();
    }

    public function update_password()
    {
        // User Information
        if (!$this->session->userdata('user')) {
            echo 'You must be logged in.';
            return false;
        }

        // Validation
        $this->form_validation->set_rules('current_password', 'Current Password', 'trim|required|min_length[6]|max_length[64]');
        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|min_length[6]|max_length[64]|matches[confirm]');
        $this->form_validation->set_rules('confirm', 'Confirm', 'trim|required');

        // Fail
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'update_password');
            $this->session->set_flashdata('validation_errors', validation_errors());
            // Just echo for now
            echo validation_errors();
            return false;
            redirect(base_url(), 'refresh');
        }
        // Success
        else {
            $session_data = $this->session->userdata('user');
            $user_id = $session_data['id'];
            $new_password = $this->input->post('new_password');
            $this->user_model->update_password($user_id, $new_password);
            redirect(base_url(), 'refresh');
        }

    }
}