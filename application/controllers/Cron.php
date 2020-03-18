<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Cron extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('cron_model', '', TRUE);
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('leaderboard_model', '', TRUE);
    }

    public function every_minute($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
        echo 'every_minute CRON - ' . PHP_EOL;
        // $this->cron_model->increase_support();
    }

    public function every_hour($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
        echo 'every_hour CRON - ' . PHP_EOL;
        // $this->cron_model->census_population();
        // $this->cron_model->settlement_output();
        // $this->cron_model->township_output_input();
        // $this->cron_model->industry_output_input();
        // $this->cron_model->income_collect();
        // $this->cron_model->update_cache_leaderboards();
        $this->cron_model->world_resets();
    }

    public function every_day($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
        echo 'every_day CRON - ' . PHP_EOL;
    }

    private function verify_token($token = false) {
        if (!$token) {
            $this->load->view('errors/page_not_found');
            return false;
        }
        $auth = json_decode(file_get_contents('auth.php'));
        // Use hash equals function to prevent timing attack
        if ( !hash_equals($auth->token, $token) ) {
            $this->load->view('errors/page_not_found');
            return false;
        }
        return true;
    }

}