<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('UTC');

class Game extends CI_Controller {

	function __construct() {
	    parent::__construct();
	    $this->load->model('game_model', '', TRUE);
	}

	public function index($param = false)
	{
		require 'global.php';
		// Get grids
        $data['grids'] = $this->game_model->get_all_grids();
        // Validation erros
        $data['validation_errors'] = $this->session->flashdata('validation_errors');
        $data['failed_form'] = $this->session->flashdata('failed_form');

		$this->load->view('map', $data);
	}

	public function ajax($param = false)
	{
		// Set coord input
		$coord_key = $_GET['request'];

	    // Get Grid Square
        $query_result = $this->game_model->get_single_grid($coord_key);
        $grid_square = isset($query_result[0]) ? $query_result[0] : false;

	    // Echo data to client to be parsed
	    if ($grid_square) {
	        echo $grid_square['owner'];
	        echo '|';
	        echo $grid_square['content'];
	        echo '|';
	        echo 1;
	    // If none found, default to this
	    } else {
	        echo 'Unowned';
	        echo '|';
	        echo 'This land is unclaimed';
	        echo '|';
	        echo 0;
	    }
	}
}