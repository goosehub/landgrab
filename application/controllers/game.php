<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('UTC');

class Game extends CI_Controller {

	function __construct() {
	    parent::__construct();
	    $this->load->model('game_model', '', TRUE);
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
	        echo $land_square['claimed'];
	        echo '|';
	        echo $land_square['user_key'];
	        echo '|';
	        echo $land_square['land_name'];
	        echo '|';
	        echo $land_square['price'];
	        echo '|';
	        echo $land_square['content'];
	    // If none found, default to this
	    } else {
	        echo 0;
	        echo '|';
	        echo 0;
	        echo '|';
	        echo 'Not Found';
	        echo '|';
	        echo 0;
	        echo '|';
	        echo '';
	    }
	}

	// Claim unclaimed land
	public function claim_land()
	{
		return true;
	}
}