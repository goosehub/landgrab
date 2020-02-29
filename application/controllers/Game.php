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

        $data['world'] = $this->game_model->get_world_by_slug($world_slug);
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
            return api_response($data);
        }

        // Load view
        $this->load->view('header', $data);
        $this->load->view('menus', $data);
        // $this->load->view('budget', $data);
        // $this->load->view('leaderboard', $data);
        $this->load->view('blocks', $data);
        $this->load->view('tile_block', $data);
        $this->load->view('variables', $data);
        $this->load->view('shared', $data);
        $this->load->view('map_script', $data);
        $this->load->view('interface_script', $data);
        // $this->load->view('tutorial_script', $data);
        $this->load->view('chat_script', $data);
        $this->load->view('footer', $data);
    }

    // Get infomation on single land
    public function get_single_tile()
    {
        $world_key = $_GET['world_key'];
        $lat = $_GET['lat'];
        $lng = $_GET['lng'];
        $tile = $this->game_model->get_single_tile($lng, $lat, $world_key);
        if (!$tile) {
            echo '{"error": "tile not found"}';
            return false;
        }
        $tile['account'] = $tile['account_key'] ? $this->user_model->get_account_by_id($tile['account_key']) : false;

        $tile['username'] = $tile['account'] ? $tile['account']['username'] : '';

        $account = $this->user_model->this_account($world_key);
        $tile['in_range'] = false;
        if ($account) {
            $world = $this->game_model->get_world_by_id($world_key);
            $account['tile_count'] = $this->game_model->get_count_of_account_tile($account['id']);
            $tile['in_range'] = $this->check_if_tile_is_in_range($world_key, $account['id'], $account['tile_count'], $world['tile_size'], $tile['lat'], $tile['lng']);
        }
        
        // Strip html entities from all untrusted columns, except content as it's stripped on insert
        $tile['tile_name'] = htmlspecialchars($tile['tile_name']);
        $tile['color'] = htmlspecialchars($tile['color']);
        $tile['username'] = htmlspecialchars($tile['username']);
        return api_response($tile);
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

    public function check_if_tile_is_in_range($world_key, $account_key, $tile_count, $tile_size, $lat, $lng)
    {
        // All tile in range if no tile
        if ($tile_count < 1) {
            return true;
        }
        // Check surrounding tiles
        $coords = array(
            array(
                'lat' => $lat + $tile_size,
                'lng' => $lng,
            ),
            array(
                'lat' => $lat,
                'lng' => $lng + $tile_size === 182 ? 180 : $lng + $tile_size,
            ),
            array(
                'lat' => $lat - $tile_size,
                'lng' => $lng,
            ),
            array(
                'lat' => $lat,
                'lng' => $lng - $tile_size === 182 ? 180 : $lng - $tile_size,
            ),
        );
        $coord_matches = $this->game_model->tile_range_check($world_key, $account_key, $coords);
        if (!empty($coord_matches) ) {
            return true;
        }
        return false;
    }

    public function tile_form()
    {
        $world_key = $_POST['world_key'];
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        $terrain_key = 1;
        $this->game_model->update_tile_terrain($lng, $lat, $terrain_key);
    }

}