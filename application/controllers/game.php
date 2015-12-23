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
}