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
      if ( hash_equals(CRON_TOKEN, $token) ) {
        // Stopwatch start
        echo 'start: ' . time() . ' - ';

        // Set cron frequency multiplier by minute
        $cron_frequency = 1;

        // Set army refill rate by minutes
        $army_refill_rate = 10;
        if ($_SERVER["HTTP_HOST"] === 'localhost') {
          $army_refill_rate = 1;
        }

        // Get all worlds
        $worlds = $this->user_model->get_all_worlds();

        // Loop through worlds
        foreach ($worlds as $world) {
          $world_key = $world['id'];

          // Get all acounts in world
          $accounts_in_world = $this->game_model->get_accounts_in_world($world_key);

          // Loop through accounts
          foreach ($accounts_in_world as $account) {
            $account_key = $account['id'];

            // Passive army fix at top of each hour
            if (date('i') == '00') {
              $query_action = $this->game_model->passive_army_fix($account_key);
              continue;
            }

            // Get land total
            $account_lands = $this->user_model->get_count_of_account_land($account_key);

            // Get potential active army
            $potential_active_army = $account_lands - $account['passive_army'];

            // Potential active army at minimum of 20
            if ($potential_active_army < 20) {
              $potential_active_army = 20;
            }

            // Continue to next account if account has no land, no potential active army, or already at potential active army
            if ($account_lands < 1 || $potential_active_army < 1 || $account['active_army'] >= $potential_active_army) {
              continue;
            }

            // Add to potential active army
            $active_army = $account['active_army'] + ceil( ($account_lands / $army_refill_rate) * $cron_frequency);

            // Reduce active army to potential maximum if more than potential maximum
            if ($active_army > $potential_active_army) {
              $active_army = $potential_active_army;
            }

            // Active army at minimum of 20
            if ($active_army < 20) {
              $active_army = 20;
            }

            // Update account active_army
            $query_action = $this->game_model->update_account_active_army($account_key, $active_army);

          } // End account loop

        } // End world loop

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