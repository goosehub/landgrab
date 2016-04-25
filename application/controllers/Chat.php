<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Chat extends CI_Controller {

	function __construct() {
	    parent::__construct();
        $this->load->model('chat_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
	}

	// Load chats
	public function load()
	{
        // Set parameters
        $world_key = $_POST['world_key'];
        $limit = 12;

        // Get chats and reverse array
        $chats = $this->chat_model->load_chat_by_limit($world_key, $limit);
        $chats = array_reverse($chats);

        // Echo out chats
        foreach ($chats as $chat) {
            echo $chat['username'] . ': ' . $chat['message'] . '<br>';
        }
        return true;
    }

    // For new chats
    public function new_chat()
    {                
        // Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('world_key', 'World Key', 'trim|required|integer|max_length[10]');
        $this->form_validation->set_rules('chat_input', 'Chat Message', 'trim|required|max_length[144]');
        // $this->form_validation->set_rules('token', 'Token', 'trim|max_length[1000]');

        if ($this->form_validation->run() == FALSE) {
            return false;
        } else {
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
                $username = $data['user']['username'];
            }

            // Set variables
            $world_key = $_POST['world_key'];
            $message = htmlspecialchars($_POST['chat_input']);

            // Insert chat
            $result = $this->chat_model->new_chat($user_id, $username, $message, $world_key);
            return true;
        }
    }

}