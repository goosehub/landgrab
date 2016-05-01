<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Cron extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('cron_model', '', TRUE);
        $this->load->model('transaction_model', '', TRUE);
    }

    // Map view
    public function index($token = false)
    {
      // Use hash equals function to prevent timing attack
      if ( hash_equals(CRON_TOKEN, $token) ) {
        // Stopwatch start
        echo 'start: ' . time() . ' - ';

        // Set cron frequency multiplier by minute
        $cron_frequency = 5;

        // Get all worlds
        $worlds = $this->user_model->get_all_worlds();

        // Loop through worlds
        foreach ($worlds as $world) {
          $world_key = $world['id'];

          // Get info for tax logic
          $world_info = $this->cron_model->get_world_tax_info($world_key);

          // Get total amount of lands
          $land_tally = $world_info[0]['land_tally'];

          // Continue if no land to preserve resources and to avoid divide by 0
          if ($land_tally < 1) { continue; }

          // Get all acounts in world
          $accounts_in_world = $this->cron_model->get_accounts_in_world($world_key, $world['land_tax_rate']);

          // Loop through accounts
          foreach ($accounts_in_world as $account) {
            $account_key = $account['id'];

            // Get lands and price sum
            $account_lands = $this->cron_model->get_account_for_taxes($account_key);

            // Skip if person has no land
            if ($account_lands[0]['land_tally'] < 1) { continue; }

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