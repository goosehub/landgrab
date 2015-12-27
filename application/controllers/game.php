<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('UTC');

class Game extends CI_Controller {

	function __construct() {
	    parent::__construct();
        $this->load->model('game_model', '', TRUE);
	    $this->load->model('user_model', '', TRUE);
	}

	// Map view
	public function index()
	{
		require 'global.php';
		// Get lands
        $data['lands'] = $this->game_model->get_all_lands();
        // Validation erros
        $data['validation_errors'] = $this->session->flashdata('validation_errors');
        $data['failed_form'] = $this->session->flashdata('failed_form');
        $data['just_registered'] = $this->session->flashdata('just_registered');

		$this->load->view('map', $data);
	}

	// Get infomation on single land
	public function get_single_land()
	{
		// Set coord input
		$coord_key = $_GET['coord_key'];

	    // Get Land Square
        $query_result = $this->game_model->get_single_land($coord_key);
        $land_square = isset($query_result[0]) ? $query_result[0] : false;

	    // Echo data to client to be parsed
	    if ($land_square) {
	    	echo json_encode($land_square);
	    // If none found, default to this
	    } else {
	        echo 'Error: Land not found';
	    }
	}

	// Claim unclaimed land
	public function land_form()
    {
        require 'global.php';
		$_POST['price'] = str_replace(',', '', $_POST['price']);
		$_POST['coord_key_input'] = str_replace(',', '|', $_POST['coord_key_input']);
		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('form_type_input', 'Form Type Input', 'trim|required|alpha|max_length[8]|callback_land_form_validation');
        $this->form_validation->set_rules('coord_key_input', 'Coord Key Input', 'trim|required|max_length[8]');
        $this->form_validation->set_rules('lng_input', 'Lng Input', 'trim|required|max_length[4]');
        $this->form_validation->set_rules('lat_input', 'Lat Input', 'trim|required|max_length[4]');
        $this->form_validation->set_rules('land_name', 'Land Name', 'trim|max_length[50]');
        $this->form_validation->set_rules('price', 'Price', 'trim|required|integer|max_length[20]');
        $this->form_validation->set_rules('content', 'Content', 'trim|max_length[1000]');
        $this->form_validation->set_rules('primary_color', 'color', 'trim|required|max_length[7]');

		// Fail
	    if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('failed_form', 'error_block');
            $this->session->set_flashdata('validation_errors', validation_errors());
            redirect('', 'refresh');
		// Success
	    } else {
	    	$claimed = 1;
	        $coord_key = $this->input->post('coord_key_input');
	        $lat = $this->input->post('lat_input');
	        $lng = $this->input->post('lng_input');
	        $land_name = $this->input->post('land_name');
	        $price = $this->input->post('price');
	        $content = $this->input->post('content');
	        $primary_color = $this->input->post('primary_color');
	        $user_key = $user_id;
	        $query_action = $this->game_model->update_land_data($claimed, $coord_key, $lat, $lng, $user_key, $land_name, $price, $content, $primary_color);
	        redirect('', 'refresh');
	    }
	}

	// Validate Login Callback
	public function land_form_validation($form_type_input)
	{
        require 'global.php';
        // Get land info for verifying our inputs
        $coord_key = $this->input->post('coord_key_input');
        $query_result = $this->game_model->get_single_land($coord_key);
        $land_square = isset($query_result[0]) ? $query_result[0] : false;

        // Check for inaccuracies
        if ($form_type_input === 'claim' && $land_square['claimed'] != 0) {
            $this->form_validation->set_message('land_form_validation', 'This land has been claimed');
            return false;
        }
        else if ($form_type_input === 'update' && $land_square['user_key'] != $user_id) {
            $this->form_validation->set_message('land_form_validation', 'This land has been bought and is no longer yours');
            return false;
        }
        else if ($form_type_input === 'buy' && $land_square['user_key'] === $user_id) {
            $this->form_validation->set_message('land_form_validation', 'This land is already yours');
            return false;
        }
        else if ($cash < $land_square['price'])
        {
            $this->form_validation->set_message('land_form_validation', 'You don\'t have enough cash to buy this land');
            return false;
        }

        // Get seller and buying party info
        $selling_owner_id = $land_square['user_key'];
        $buying_owner_id = $user_id;
        $amount = $land_square['price'];
        $selling_owner = $this->user_model->get_user($selling_owner_id);
        $buying_owner = $this->user_model->get_user($buying_owner_id);

        // Do sale
        $new_selling_owner_cash = $selling_owner['cash'] + $amount;
        $new_buying_owner_cash = $buying_owner['cash'] - $amount;
        $query_action = $this->game_model->land_sale($selling_owner_id, $buying_owner_id, $new_selling_owner_cash, $new_buying_owner_cash);

        return true;
	}
}