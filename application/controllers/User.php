<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class User extends CI_Controller {

    protected $democracy_key = 1;
    protected $oligarchy_key = 2;
    protected $autocracy_key = 3;

	function __construct() {
	    parent::__construct();
	    $this->load->model('user_model', '', TRUE);
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
        $timestamp = date('Y-m-d H:i:s', time() - 20 * 60);
        $ip_fails = $this->user_model->check_ip_request_since_timestamp($ip, 'login', $timestamp);
        $login_limit = 10;
        if (count($ip_fails) > $login_limit && !is_dev()) {
            echo 'Too many login attempts from this IP. Please wait 20 minutes.';
            die();
        }

		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[32]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[64]|callback_login_validation');
        
        $world_key = $this->input->post('world_key');
		// Fail
        if ($this->form_validation->run() == FALSE) {
            // Record failed request
            $result = $this->user_model->record_ip_request($ip, 'login');   

            // Set fail message and redirect to map
        	$this->session->set_flashdata('failed_form', 'login');
        	$this->session->set_flashdata('validation_errors', validation_errors());
            redirect('world/' . $world_key, 'refresh');
		// Success
        } else {
            redirect('world/' . $world_key, 'refresh');
        }
	}

	// Validate Login Callback
	public function login_validation($password)
	{
		// Get other parameters
        $username = $this->input->post('username');

        // Compare to database
        $result = $this->user_model->login($username, $password);

        // Fail
        if (!$result || !password_verify($password, $result['password'])) {
            $this->form_validation->set_message('login_validation', 'Invalid username or password');
            return false;
		// Success
		} else {
			// Login
            $sess_array = array(
                'id' => $result['id'],
                'username' => $result['username']
            );
            $this->session->set_userdata('logged_in', $sess_array);
            return TRUE;
        }
	}

	// Register
	public function register()
	{
        // Optional password
        $matches = 'matches[confirm]|';

        $password_optional = false;
        if ($password_optional) {
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
        // Fail
        if ($this->form_validation->run() == FALSE) {
        	$this->session->set_flashdata('failed_form', 'register');
        	$this->session->set_flashdata('validation_errors', validation_errors());
            redirect('world/' . $world_key, 'refresh');
        // Success
        } else {
            $this->session->set_flashdata('just_registered', true);
            redirect('world/' . $world_key, 'refresh');
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
        $ip_frequency_register = 4;
        $user_id = $this->user_model->register($username, $password, $email, $facebook_id, $ip, $ip_frequency_register, $ab_test);
        // Fail
        if ($user_id === 'ip_fail') {
            $this->form_validation->set_message('register_validation', 'This IP has already registered in the last ' . $ip_frequency_register . ' hours');
            return false;
        }
        if (!$user_id) {
            $this->form_validation->set_message('register_validation', 'Username already exists');
            return false;
        }
        // Success
        // Set variables
        $worlds = $this->user_model->get_all_worlds();
        
        // Create account for each world
        foreach ($worlds as $world)
        {
            // Random color for each account
            $color = random_hex_color();

            // Default these values
            $nation_name = $username . ' Nation';
            $nation_flag = 'default_nation_flag.png';
            $leader_portrait = 'default_leader_portrait.png';
            $government = 1;

            $account_id = $this->user_model->create_player_account($user_id, $world['id'], $color, $nation_name, $nation_flag, $leader_portrait, $government);
        }

		// Login
        $sess_array = array();
        $sess_array = array(
            'id' => $user_id,
            'username' => $username
        );
        $this->session->set_userdata('logged_in', $sess_array);
        return true;
    }

	// Logout
    public function logout() {
        $this->session->unset_userdata('logged_in');
        redirect('', 'refresh');
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
        if (!$this->session->userdata('logged_in')) {
            return false;
        }
        $session_data = $this->session->userdata('logged_in');
        $user_id = $data['user_id'] = $session_data['id'];
        
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('world_key', 'World Key Input', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('nation_color', 'Nation Color', 'trim|required|max_length[7]');
        $this->form_validation->set_rules('nation_name', 'Nation Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('nation_flag', 'Nation Flag', 'trim|max_length[500]');
        $this->form_validation->set_rules('leader_portrait', 'Leader Portrait', 'trim|max_length[500]');
        $this->form_validation->set_rules('existing_nation_flag', 'Existing Nation Flag', 'trim|max_length[500]');
        $this->form_validation->set_rules('existing_leader_portrait', 'Existing Leader Portrait', 'trim|max_length[500]');

        $world_key = $this->input->post('world_key');

        // Form Validation Fail
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            redirect('world/' . $world_key, 'refresh');
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
        $nation_name = $this->input->post('nation_name');

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
        $account = $this->user_model->get_account_by_keys($user_id, $world_key);
        $account_key = $account['id'];
        $this->user_model->update_account_info($account_key, $nation_color, $nation_name, $nation_flag, $leader_portrait);

        // Progress Tutorial
        if ($account['tutorial'] < 1) {
            $this->user_model->update_account_tutorial($account_key, 1);
        }

        // Redirect to game
        redirect('world/' . $world_key, 'refresh');
    }
}