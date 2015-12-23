<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('UTC');
session_start();

class Game extends CI_Controller {

	function __construct() {
	    parent::__construct();
	    $this->load->model('game_model', '', TRUE);
	}

	public function index($param = false)
	{
        $data['grids'] = $this->game_model->get_all_grids();
		$this->load->view('game');
	}

	public function ajax($param = false)
	{
		// Set coord input
		$request = $_GET['request'];

	    // Get Grid Square
        $query_result = $this->game_model->get_single_grid($request);
        $grid_square = isset($query_result[0]) ? $query_result[0] : false;

	    // echo data to client
	    if ($grid_square)
	    {
	        echo $grid_square['owner'];
	        echo '|';
	        echo $grid_square['content'];
	    }
	    // If unfound, default to this
	    else
	    {
	        echo 'Unowned';
	        echo '|';
	        echo 'This land is unclaimed';
	    }
	}
	public function not_found()
	{
		$this->load->view('not_found');
	}
}