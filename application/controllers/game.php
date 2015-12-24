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
        $data['grids'] = $this->game_model->get_all_grids();
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
	    // If none found, default to this
	    } else {
	        echo 'Unowned';
	        echo '|';
	        echo 'This land is unclaimed';
	    }
	}
}