<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('UTC');

class Cron extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
    }

    // Map view
    public function index($token = false)
    {
      // This variable to be changed in live version
      $cron_token = '1234';

      // Use hash equals function to prevent timing attack
      if ( hash_equals($cron_token, $token) ) {

        // Loop through worlds
        $worlds = $this->user_model->get_all_worlds();
        foreach ($worlds as $world) {

          // Start with taxes collected set to 0
          $taxes_collected = 0;

          // Loop through lands
          $claimed_lands = $this->game_model->get_all_lands_in_world_where_claimed($world['id']);

          // Continue to next world if no claimed lands in this world
          if ( empty($claimed_lands) ) {
            continue;
          }

          foreach ($claimed_lands as $land) {
          echo 'land to be taxed';
          var_dump($land);

            // Find tax amount
            $land_tax = $land['price'] * $world['land_tax_rate'];
            echo 'land tax amount to be applied';
            var_dump($land_tax);

            // Find account
            $account = $this->user_model->get_account_by_keys($land['account_key'], $world['id']);
            echo 'owner account';
            var_dump($account);

            // If not enough cash, forfeit all land and reset cash
            if ($account['cash'] < $land_tax) {
              echo 'not enough cash, land will be forfeited and cash balance reset';
              $query_action = $this->game_model->forfeit_all_land_of_account($account['id']);
              $new_cash_balance = 10000000;
              $query_action = $this->game_model->update_account_cash_by_account_id($account['id'], $new_cash_balance);

            // Detuct tax
            } else {
              $taxes_collected += $land_tax;
              $new_cash_balance = ceil($account['cash'] - $land_tax);
              echo 'new cash balance';
              var_dump($new_cash_balance);
              $query_action = $this->game_model->update_account_cash_by_account_id($account['id'], $new_cash_balance);
            }
          }

          echo '<hr>';
          echo 'end of tax collection';
          echo '<br>';

          echo 'taxes collected';
          var_dump($taxes_collected);
          $fair_share = $taxes_collected / count($claimed_lands);
          echo 'fair share';
          var_dump($fair_share);

          echo 'start of fair share distribution';

          echo '<hr>';

          // Loop through lands
          foreach ($claimed_lands as $land) {
          echo 'land to receive fair share';
          var_dump($land);

            // Add fair share to account
            $account = $this->user_model->get_account_by_keys($land['account_key'], $world['id']);
            echo 'account to receive fair share';
            var_dump($account);
            $new_cash_balance = ceil($account['cash'] + $fair_share);
            echo 'new cash balance';
            var_dump($new_cash_balance);
            $query_action = $this->game_model->update_account_cash_by_account_id($account['id'], $new_cash_balance);
          }
        }

        // Generic Page Not Found on fail
        } else {
            $this->load->view('page_not_found');
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