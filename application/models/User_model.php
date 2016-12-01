<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class user_model extends CI_Model
{
 // Get all users
 function get_all_users()
 {
    $this->db->select('id, username, created');
    $this->db->from('user');
    $query = $this->db->get();
    return $query->result_array();
 }
 // Get user
 function get_user($user_id)
 {
    $this->db->select('id, username, created');
    $this->db->from('user');
    $this->db->where('id', $user_id);
    $this->db->limit(1);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Get account by keys
 function get_account_by_keys($user_key, $world_key)
 {
    $this->db->select('*');
    $this->db->from('account');
    $this->db->where('user_key', $user_key);
    $this->db->where('world_key', $world_key);
    $this->db->limit(1);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Get account by keys
 function get_account_by_id($account_id)
 {
    $this->db->select('*');
    $this->db->from('account');
    $this->db->where('id', $account_id);
    $this->db->limit(1);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Get count of land by account
 function get_count_of_account_land($account_key)
 {
    $this->db->select('COUNT(*) as count');
    $this->db->from('land');
    $this->db->where('account_key', $account_key);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]['count']) ? $result[0]['count'] : 0;
 }
 // Get all worlds
 function get_all_worlds()
 {
    $this->db->select('*');
    $this->db->from('world');
    $query = $this->db->get();
    return $query->result_array();
 }
 // Login
 function login($username, $password)
 {
    $this->db->select('*');
    $this->db->from('user');
    $this->db->where('username', $username);
    $this->db->limit(1);
    $query = $this->db->get();
    if ($query->num_rows() == 1) {
        $result = $query->result_array();
        return isset($result[0]) ? $result[0] : false;
    } else {
        return false;
    }
 }
 // Register
 function register($username, $password, $email, $facebook_id, $ip, $ip_frequency_register)
 {
    // Check for excessive IPs registers
    $this->db->select('username');
    $this->db->from('user');
    $this->db->where('ip', $ip);
    $this->db->where('created > NOW() - INTERVAL ' . $ip_frequency_register . ' HOUR');
    $this->db->limit(1);
    $query = $this->db->get();

    // Disabled for now
    if ($query->num_rows() > 0 && !is_dev()) {
        return 'ip_fail';
    }

    $this->db->select('username');
    $this->db->from('user');
    $this->db->where('username', $username);
    $this->db->limit(1);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return false;
    } else {
        // Insert user into user
        $data = array(
        'username' => $username,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'email' => $email,
        'facebook_id' => $facebook_id,
        'ip' => $ip
        );
        $this->db->insert('user', $data);

        // Find user id
        $this->db->select_max('id');
        $this->db->from('user');
        $this->db->limit(1);
        $query = $this->db->get()->row();
        $user_id = $query->id;
        return $user_id;
    }
 }
 // Create player account
 function create_player_account($user_key, $world_key, $color, $nation_name, $nation_flag, $leader_portrait, $government)
 {
    // Insert user into user
    $data = array(
    'world_key' => $world_key,
    'user_key' => $user_key,
    'color' => $color,
    'nation_name' => $nation_name,
    'nation_flag' => $nation_flag,
    'leader_portrait' => $leader_portrait,
    'government' => $government,
    'last_load' => date('Y-m-d H:i:s')
    );
    $this->db->insert('account', $data);

    // Find account id
    $this->db->select_max('id');
    $this->db->from('account');
    $this->db->limit(1);
    $query = $this->db->get()->row();
    $account_id = $query->id;
    return $account_id;
 }
 // Update account information
 function update_account_info($account_id, $color, $nation_name, $nation_flag, $leader_portrait)
 {
    // Update account
    $data = array(
        'color' => $color,
        'nation_name' => $nation_name,
        'nation_flag' => $nation_flag,
        'leader_portrait' => $leader_portrait
    );
    $this->db->where('id', $account_id);
    $this->db->update('account', $data);

    // Update lands
    $data = array(
        'color' => $color,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('account_key', $account_id);
    $this->db->update('land', $data);
    return true;
 }
 // Update account laws
 function update_account_laws($account_id, $government, $tax_rate, $military_budget, $entitlements_budget)
 {
    // Update account
    $data = array(
        'government' => $government,
        'tax_rate' => $tax_rate,
        'military_budget' => $military_budget,
        'entitlements_budget' => $entitlements_budget
    );
    $this->db->where('id', $account_id);
    $this->db->update('account', $data);
    return true;
}
 // Progress Tutorial
 function update_account_tutorial($account_id, $tutorial)
 {
    // Update account
    $data = array(
        'tutorial' => $tutorial
    );
    $this->db->where('id', $account_id);
    $this->db->update('account', $data);
    return true;
 }
 // Mark account as loaded
 function account_loaded($account_id)
 {
    // Update account
    $data = array(
        'last_load' => date('Y-m-d H:i:s')
    );
    $this->db->where('id', $account_id);
    $this->db->update('account', $data);
    return true;
 }
 // Create player account
 function record_ip_request($ip, $request)
 {
    // Insert user into user
    $data = array(
    'ip' => $ip,
    'request' => $request
    );
    $this->db->insert('ip_request', $data);
 }
 // Create player account
 function check_ip_request_since_timestamp($ip, $request, $timestamp)
 {
    $this->db->select('*');
    $this->db->from('ip_request');
    $this->db->where('ip', $ip);
    $this->db->where('request', $request);
    $this->db->where('timestamp >', $timestamp);
    $query = $this->db->get();
    return $query->result_array();
 }
 // Record marketing slug hits
 function record_marketing_hit($marketing_slug)
 {
    // Insert user into user
    $data = array(
    'marketing_slug' => $marketing_slug
    );
    $this->db->insert('analytics', $data);
 }
 // Update last government switch
 function update_government_switch($account_id)
 {
    $this->db->where('id', $account_id);
    $this->db->set('last_government_switch', date('Y-m-d H:i:s', time() ) );
    $this->db->update('account');
 }

}
?>