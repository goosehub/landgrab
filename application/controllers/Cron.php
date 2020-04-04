<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/New_York');

class Cron extends CI_Controller {

    public $logged_microtime;

    function __construct() {
        parent::__construct();
        $this->load->model('cron_model', '', TRUE);
        $this->load->model('game_model', '', TRUE);
        $this->load->model('user_model', '', TRUE);
        $this->load->model('leaderboard_model', '', TRUE);
        $this->logged_microtime = microtime(true);
    }

    public function every_minute($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
        echo 'every_minute CRON - ' . PHP_EOL;
        $this->microtime('start_crons');
        $this->cron_model->increase_support(); $this->microtime('increase_support');
        // $this->cron_model->update_cache_leaderboards(); $this->microtime('update_cache_leaderboards');
    }

    public function every_hour($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
        echo 'every_hour CRON - ' . PHP_EOL;
        echo '<br>';
        $this->microtime('start_crons');

        // Debugs
        // $this->cron_model->market_prices_debug();
        $this->cron_model->grow_population(); $this->microtime('grow_population');
        dd();

        // Order matters
        $this->cron_model->zero_negative_account_supply(); $this->microtime('zero_negative_account_supply');
        $this->cron_model->update_market_prices(); $this->microtime('update_market_prices');
        $this->cron_model->grow_population(); $this->microtime('grow_population');
        $this->cron_model->diverse_imports_bonus(); $this->microtime('diverse_imports_bonus');
        $this->cron_model->census_population(); $this->microtime('census_population');
        $this->cron_model->consume_gdp_bonus_supplies(); $this->microtime('consume_gdp_bonus_supplies');
        $this->cron_model->resource_output(); $this->microtime('resource_output');
        $this->cron_model->settlement_output(); $this->microtime('settlement_output');
        $this->cron_model->township_input(); $this->microtime('township_input');
        $this->cron_model->industry_input(); $this->microtime('industry_input');
        $this->cron_model->industry_output(); $this->microtime('industry_output');
        $this->cron_model->shrink_population(); $this->microtime('shrink_population');
        $this->cron_model->downgrade_townships(); $this->microtime('downgrade_townships');
        $this->cron_model->settlement_income_collect(); $this->microtime('settlement_income_collect');
        $this->cron_model->industry_income_collect(); $this->microtime('industry_income_collect');
        $this->cron_model->punish_negative_money(); $this->microtime('punish_negative_money');
        $this->cron_model->mark_active_accounts_as_active(); $this->microtime('mark_active_accounts_as_active');

        // $this->cron_model->system_chat_messages(); $this->microtime('system_chat_messages');
    }

    public function every_day($token = false)
    {
        $valid = $this->verify_token($token);
        if (!$valid) { return; }
        echo 'every_day CRON - ' . PHP_EOL;
        $this->cron_model->world_resets(); $this->microtime('world_resets');
    }

    public function microtime($string = 'microtime')
    {
        $microseconds = microtime(true) - $this->logged_microtime;
        echo '<br>' . $string . ' | ' . $microseconds . '<br>';
        $this->logged_microtime = microtime(true);
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