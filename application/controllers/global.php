<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Global PHP, to be included with on every function with authentication

// Defaults for unauthenticated users
$log_check = $data['log_check'] = 0;
$user_id = $data['user_id'] = 0;
$cash = $data['cash'] = 0;
$username = $data['username'] = '';

// Logged in information
if ($this->session->userdata('logged_in')) 
{
    // Get user data
    $log_check = $data['log_check'] = true;
    $session_data = $this->session->userdata('logged_in');
    $username = $data['username'] = $session_data['username'];
    $user_id = $data['user_id'] = $session_data['id'];
    $cash = $data['cash'] = $session_data['cash'];
}

?>