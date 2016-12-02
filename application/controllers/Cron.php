<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Cron extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
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

      // Decrement war weariness
      echo 'Decrementing Universal War Weariness - ';
      $war_weariness_decrease = 5;
      $this->game_model->universal_decrease_war_weariness($war_weariness_decrease);

      // Resets
      $world_reset_frequency[1] = '0 20 1 * *';
      $world_reset_frequency[2] = '0 21 1,15 0 *';
      $world_reset_frequency[3] = '0 22 * * *';
      $world_reset_frequency[4] = '0 0,12 * * *';
      $world_reset_frequency[5] = '0 * * * *';

      $now = date('Y-m-d H:i:s');
      $worlds = $this->user_model->get_all_worlds();
      foreach ($worlds as $world) {
        // Check if it's time to run
        $time_to_reset = parse_crontab($now, $world_reset_frequency[$world['id']]);
        if (!$time_to_reset) {
          continue;
        }
        echo 'Resetting world ' . $world['id'] . ' - ' . $world['slug'] . ' - ';
        $limit = 1;
        $leader = $this->leaderboard_model->leaderboard_land_owned($world['id'], $limit);
        if ( empty($leader) ) {
          echo ' - No leader found - ';
          return false;
        }
        $leader = $leader[0];
        $last_winner_account_key = $leader['account_key'];
        $last_winner_land_count = $leader['total'];
        $this->game_model->update_world_with_last_winner($world['id'], $last_winner_account_key, $last_winner_land_count);
        $this->game_model->update_all_lands_in_world($world['id'], $account_key = 0, $land_name = '', $content = '', $land_type = 1, $color = '#000000', $capitol = 0);
        $this->game_model->world_set_war_weariness($world['id'], $war_weariness = 0);
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

      // Fix to reset only land type modifiers
/*
      $lands = $this->game_model->get_all_lands_in_world(1);

      $this->game_model->truncate_modifiers();
      foreach ($lands as $l) {
        $this->game_model->add_modifier_to_land($l['id'], $l['land_type']);
      }
*/
      // Stopwatch end
      // echo 'end: ' . time() . ' - ';

      // Taxes complete, good job! echo for cron email. time() keeps email from going to spam as exact duplicate
      // echo 'Cron Successful. Timestamp: ' . time();

    // Generic Page Not Found on fail
    }

}