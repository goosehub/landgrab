<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('UTC');

class User extends CI_Controller {

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
		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|callback_login_validation');
        
		// Fail
        if ($this->form_validation->run() == FALSE) {
        	$this->session->set_flashdata('failed_form', 'login');
        	$this->session->set_flashdata('validation_errors', validation_errors());
            redirect('', 'refresh');
		// Success
        } else {
            redirect('', 'refresh');
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
		if (! $result) {
            $this->form_validation->set_message('check_database', 'Invalid username or password');
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
		// Validation
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|callback_register_validation|min_length[6]|matches[confirm]');
        $this->form_validation->set_rules('confirm', 'Confirm', 'trim|required');
        // Fail
        if ($this->form_validation->run() == FALSE) {
        	$this->session->set_flashdata('failed_form', 'register');
        	$this->session->set_flashdata('validation_errors', validation_errors());
            redirect('', 'refresh');
        // Success
        } else {
            $this->session->set_flashdata('just_registered', true);
            redirect('', 'refresh');
        }
	}

	// Validate Register Callback
    public function register_validation($password) {
        // Set parameters
        $email = "placeholder@gmail.com";
        $username = $this->input->post('username');
        // Email Validation
        $this->load->helper('email');
        if (!valid_email($email)) {
            $this->form_validation->set_message('insert_database', 'This is not a working email address');
            return false;
        } else {
            // Attempt new user register
            $facebook_id = 0;
            $user_id = $this->user_model->register($username, $password, $email, $facebook_id);
            // Fail
            if (! $user_id) {
                $this->form_validation->set_message('insert_database', 'Username already exists');
                return false;
            // Success
            } else {
                // Create account for each world
                $world_keys = $this->user_model->get_all_world_keys();
                $cash = 1000000;
                foreach ($world_keys as $world)
                {
                    $account_id = $this->user_model->create_player_account($user_id, $world['id'], $cash);
                }
				// Login
                $sess_array = array();
                $sess_array = array(
                    'id' => $user_id,
                    'username' => $username,
                    'cash' => $cash
                );
                $this->session->set_userdata('logged_in', $sess_array);
                return true;
            }
        }
    }

	// Logout
    public function logout() {
        $this->session->unset_userdata('logged_in');
        redirect('', 'refresh');
    }

    // Page Not Found
	public function page_not_found()
	{
		$this->load->view('page_not_found');
	}
}