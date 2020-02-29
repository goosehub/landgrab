<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Game extends CI_Controller {
// 
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
        if (MAINTENANCE) {
            return $this->maintenance();
        }

        $this->user_model->record_marketing_hit($marketing_slug);

        $data['world'] = $this->game_model->get_world_by_slug_or_id($world_slug);
        if (!$data['world']) {
            return $this->load->view('errors/page_not_found', $data);
        }

        $data['account'] = $this->user_model->this_account($data['world']['id']);

        if (isset($_GET['json'])) {
            $data['tiles'] = $this->game_model->get_all_tiles_in_world_recently_updated($data['world']['id'], SERVER_MAP_UPDATE_INTERVAL_MS);
        }
        else {
            $data['worlds'] = $this->game_model->get_all_worlds();
	        $data['leaderboards'] = $this->leaderboards($data['world']['id']);
            $data['tiles'] = $this->game_model->get_all_tiles_in_world($data['world']['id']);
	        $data['validation_errors'] = $this->session->flashdata('validation_errors');
	        $data['failed_form'] = $this->session->flashdata('failed_form');
	        $data['just_registered'] = $this->session->flashdata('just_registered');
        }

        if (isset($_GET['json'])) {
            array_walk_recursive($data, "escape_quotes");
            echo json_encode($data);
            return true;
        }

        // Load view
        $this->load->view('header', $data);
        $this->load->view('menus', $data);
        // $this->load->view('budget', $data);
        // $this->load->view('leaderboard', $data);
        $this->load->view('blocks', $data);
        // $this->load->view('land_block', $data);
        $this->load->view('map_script', $data);
        $this->load->view('interface_script', $data);
        // $this->load->view('tutorial_script', $data);
        $this->load->view('chat_script', $data);
        $this->load->view('footer', $data);
    }

    public function leaderboards($world_id)
    {
    	return;
    }

    public function maintenance()
    {
        // Send refresh signal to clients when true
        if (isset($_GET['json'])) {
            $data['refresh'] = $this->maintenance_flag;
            echo json_encode($data);
        }
        else {
            echo '<h1>Landgrab is being updated. This will only take a minute or two. This page will refresh automatically.</h1>';
            echo '<script>window.setTimeout(function(){ window.location.href = "' . base_url() . '"; }, 5000);</script>';
        }
        return false;
    }

}