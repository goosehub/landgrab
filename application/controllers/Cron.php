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

    // Map view
    public function index($token = false)
    {
      // Use hash equals function to prevent timing attack
      if (!$token) {
        $this->load->view('errors/page_not_found');
        return false;
      }
      if ( !hash_equals(CRON_TOKEN, $token) ) {
        $this->load->view('errors/page_not_found');
        return false;
      }
      echo 'Running Cron - ';

      // Resets
      $world_reset_frequency[1] = '* * * * *';
      // $world_reset_frequency[1] = '0 20 1,15 * *';
      // $world_reset_frequency[1] = '0 20 1,15 * *';
      // $world_reset_frequency[2] = '0 20 1 * *';
      // $world_reset_frequency[3] = '0 20 * * 0';
      // $world_reset_frequency[4] = '0 20 * * *';
      // $world_reset_frequency[5] = '0 */4 * * *';

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


      // Add accounts for new world
/*
      $users = $this->user_model->get_all_users();
      foreach ($users as $user) {
        $color = random_hex_color();
        $nation_name = $user['username'] . ' Nation';
        $nation_flag = 'default_nation_flag.png';
        $leader_portrait = 'default_leader_portrait.png';
        $government = 1;
        $this->user_model->create_player_account($user['id'], 2, $color, $nation_name, $nation_flag, $leader_portrait, $government);
      }
*/
    }

}