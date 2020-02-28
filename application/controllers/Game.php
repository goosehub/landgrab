<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {
	function __construct() {
	    parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
	    $this->load->model('leaderboard_model', '', TRUE);

        // Force ssl
        if (!is_dev()) {
            force_ssl();
        }
	}

	// Game view and update json
	public function index($world_slug = 1, $marketing_slug = false)
	{
        echo 'marco';
	}

}