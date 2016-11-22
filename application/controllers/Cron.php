<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Cron extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
    }

    // Map view
    public function index($token = false)
    {
      // Use hash equals function to prevent timing attack
      // if ( hash_equals(CRON_TOKEN, $token) ) {
      if (true) {
        // Stopwatch start
        echo 'start: ' . time() . ' - ';

        // Set cron frequency multiplier by minute
        $cron_frequency = 1;

        $war_weariness_decrease = 1;
        $this->game_model->universal_decrease_war_weariness($war_weariness_decrease);
/*
        $users = $this->user_model->get_all_users();
        foreach ($users as $user) {
          $color = random_hex_color();
          $nation_name = $user['username'];
          $nation_flag = 'default_nation_flag.png';
          $leader_name = $user['username'];
          $leader_portrait = 'default_leader_portrait.png';
          $this->user_model->create_player_account($user['id'], 1, $color, $nation_name, $nation_flag, $leader_name, $leader_portrait);
        }
*/
        // Loop to get to each account individually if needed in the future
/*
        // Get all worlds
        $worlds = $this->user_model->get_all_worlds();
        // Loop through worlds
        foreach ($worlds as $world) {
          $world_key = $world['id'];
          // Get all acounts in world
          $active_accounts_in_world = $this->game_model->get_active_accounts_in_world($world_key);
          // Loop through accounts
          foreach ($active_accounts_in_world as $account) {
            $account_key = $account['id'];
            // Do stuff
          } // End account loop
        } // End world loop
*/
        // Stopwatch end
        echo 'end: ' . time() . ' - ';

        // Taxes complete, good job! echo for cron email. time() keeps email from going to spam as exact duplicate
        echo 'Cron Successful. Timestamp: ' . time();

      // Generic Page Not Found on fail
      } else {
          $this->load->view('errors/page_not_found');
      }
    }

    // Create hash_equals function if not exists
    protected static function _createHashEquals() {
        if(!function_exists('hash_equals')) {
          function hash_equals($str1, $str2) {
            if(strlen($str1) != strlen($str2)) {
              return false;
            } else {
              $res = $str1 ^ $str2;
              $ret = 0;
              for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
              return !$ret;
            }
          }
        }
    }

}

// Random color function for generating primary color
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}
function random_hex_color() {
    return '#' . random_color_part() . random_color_part() . random_color_part();
}