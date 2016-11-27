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
      // if (true) {
        // Stopwatch start
        // echo 'start: ' . time() . ' - ';

        // Set cron frequency multiplier by minute
        $cron_frequency = 1;

        // Decrease war weariness
        $war_weariness_decrease = 3;
        for ($i = 0; $i < $war_weariness_decrease; $i++) {
          $this->game_model->universal_decrease_war_weariness(1);
        }

        // Fix to reset only land type modifiers
/*
        $lands = $this->game_model->get_all_lands_in_world(1);

        $this->game_model->truncate_modifiers();
        foreach ($lands as $l) {
          $this->game_model->add_modifier_to_land($l['id'], $l['land_type']);
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
        // echo 'end: ' . time() . ' - ';

        // Taxes complete, good job! echo for cron email. time() keeps email from going to spam as exact duplicate
        // echo 'Cron Successful. Timestamp: ' . time();

      // Generic Page Not Found on fail
      } else {
          $this->load->view('errors/page_not_found');
      }
    }

}