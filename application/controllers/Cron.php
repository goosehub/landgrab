<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Cron extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('cron_model', '', TRUE);
        $this->load->model('leaderboard_model', '', TRUE);
    }

    public function index($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
    }

    public function every_minute($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
    }

    public function every_hour($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
    }

    public function every_day($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
    }

    public function every_reset($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }

        echo 'Running Cron - ';

        $world_reset_frequency[1] = '* * * * *';

        $now = date('Y-m-d H:i:s');
        $worlds = $this->game_model->get_all('world');
        foreach ($worlds as $world) {
            // Check if it's time to run
            $time_to_reset = parse_crontab($now, $world_reset_frequency[$world['id']]);
            if (!$time_to_reset) {
                continue;
            }
            echo 'Running for world ' . $world['id'] . ' - ' . $world['slug'] . ' - ';
            // $this->game_model->backup();
            // $this->game_model->reset_tiles();
            // $this->game_model->reset_trades();
            // $this->game_model->reset_accounts();
            $this->cron_model->regenerate_resources($world['id']);
        }
    }

    public function verify_token($token = false) {
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