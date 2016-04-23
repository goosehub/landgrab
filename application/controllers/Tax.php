<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Tax extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('tax_model', '', TRUE);
        $this->load->model('transaction_model', '', TRUE);
    }

    // Map view
    public function index($token = false)
    {
      // Use hash equals function to prevent timing attack
      if ( hash_equals(CRON_TOKEN, $token) ) {
        // Stopwatch start
        echo 'start: ' . time() . ' - ';

        // Get all worlds
        $worlds = $this->user_model->get_all_worlds();

        // Loop through worlds
        foreach ($worlds as $world) {
          $world_key = $world['id'];

          // Get info for tax logic
          $world_info = $this->tax_model->get_world_tax_info($world_key);

          // Get total amount of lands
          $land_tally = $world_info[0]['land_tally'];

          // Continue if no land to preserve resources and to avoid divide by 0
          if ($land_tally < 1) { continue; }

          // Get total taxes due
          $taxes_due = ceil($world_info[0]['price_tally'] * 0.01);

          // Get all acounts in world
          $accounts_in_world = $this->tax_model->get_accounts_in_world($world_key, $world['land_tax_rate']);

          // Loop through accounts
          foreach ($accounts_in_world as $account) {
            $account_key = $account['id'];

            // Get lands and price sum
            $account_lands = $this->tax_model->get_account_for_taxes($account_key);

            // Skip if person has no land
            if ($account_lands[0]['land_tally'] < 1) { continue; }

            // Caluclate rebate and post rebate balance
            $account_rebate = $world['land_rebate'] * $account_lands[0]['land_tally'];
            $post_rebate_account_balance = $account['cash'] + $account_rebate;
            
            // Calculate taxes and final account balance
            $account_taxes_due = ceil($account_lands[0]['price_tally'] * 0.01);
            $final_account_balance = $post_rebate_account_balance - $account_taxes_due;

            // Bankruptcy
            if ( $final_account_balance < 1 ) {
              
              // Forfeit land
              $query_action = $this->tax_model->forfeit_all_land_of_account($account_key, $world['claim_fee']);

              // Reset account balance
              $new_cash_balance = 100000;
              $query_action = $this->game_model->update_account_cash_by_account_id($account_key, $new_cash_balance);

              // Record into transaction log
              $query_action = $this->transaction_model->new_transaction_record(0, $account_key, 'bankruptcy', $new_cash_balance, $world_key, 0, '', '');

              // Continue to next account
              continue;
            }

            // Record rebate into transaction log
            $query_action = $this->transaction_model->new_transaction_record(0, $account_key, 'rebate', $account_rebate, $world_key, 0, '', '');

            // Record taxes into transaction log
            $query_action = $this->transaction_model->new_transaction_record($account_key, 0, 'taxes', $account_taxes_due, $world_key, 0, '', '');

            // Apply final account balance to account
            $query_action = $this->game_model->update_account_cash_by_account_id($account_key, $final_account_balance);

          } // End account loop

        } // End world loop

        // Stopwatch end
        echo 'end: ' . time() . ' - ';

        // Taxes complete, good job! echo for cron email. time() keeps email from going to spam as exact duplicate
        echo 'Tax Controller Successful. Timestamp: ' . time();

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