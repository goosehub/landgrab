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

		// Get data
		$json = json_decode(file_get_contents("data.json"), true);

		// Request for some data on all grids
		if ($request === 'all')
		{
		    return;
		}
		// Request for all data on one grid
		else
		{
		    // Find grid
		    $grid_square = isset($json[$request]) ? $json[$request] : false;

		    // echo data to client
		    if ( isset($grid_square['content']) )
		    {
		        echo $grid_square['content'];
		        echo '|';
		        echo $grid_square['owner'];
		    }
		    // If unfound, default to this
		    else
		    {
		        echo 'This land is unclaimed';
		        echo '|';
		        echo 'Unowned';
		    }
		}
	}
}