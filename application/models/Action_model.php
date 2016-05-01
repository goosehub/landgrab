<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
    // echo '<br>' . $this->db->last_query() . '<br>';

Class action_model extends CI_Model
{
// Insert new action log
 function new_action_record($active_account_key, $passive_account_key, $action, $amount, $world_key, $coord_slug, $name_at_action, $details)
 {
    $data = array(
    'active_account_key' => $active_account_key,
    'passive_account_key' => $passive_account_key,
    'action' => $action,
    'amount' => $amount,
    'world_key' => $world_key,
    'coord_slug' => $coord_slug,
    'name_at_action' => $name_at_action,
    'details' => $details
    );
    $this->db->insert('action_log', $data);
 }
 // Get trade purcheses
 function get_action_purchases($active_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum, COUNT(*) as total');
    $this->db->from('action_log');
    $this->db->where('active_account_key', $active_account_key);
    $this->db->where_in('action', ['buy', 'claim']);
    $this->db->where('created >= DATE(NOW()) - INTERVAL ' . $day_timeframe . ' DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get trade sales
 function get_action_sales($passive_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum, COUNT(*) as total');
    $this->db->from('action_log');
    $this->db->where('passive_account_key', $passive_account_key);
    $this->db->where('action', 'buy');
    $this->db->where('created >= DATE(NOW()) - INTERVAL ' . $day_timeframe . ' DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get action losses
 function get_action_losses($active_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum');
    $this->db->from('action_log');
    $this->db->where('active_account_key', $active_account_key);
    $this->db->where('created >= DATE(NOW()) - INTERVAL ' . $day_timeframe . ' DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get action profit
 function get_action_gains($passive_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum');
    $this->db->from('action_log');
    $this->db->where('passive_account_key', $passive_account_key);
    $this->db->where('created >= DATE(NOW()) - INTERVAL ' . $day_timeframe . ' DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get recent sold lands
 function sold_lands_by_account_over_period($account_key, $last_load)
 {
    $this->db->select('*');
    $this->db->from('action_log');
    $this->db->where('passive_account_key', $account_key);
    $this->db->where('action', 'buy');
    $this->db->where('created >= ', $last_load);
    $this->db->order_by('id', 'DESC');
    $query = $this->db->get();
    $result = $query->result_array();
    return $result;
 }
 // Check for bankruptcy since last page load
 function check_for_bankruptcy($account_key, $last_load)
 {
    $this->db->select('*');
    $this->db->from('action_log');
    $this->db->where('passive_account_key', $account_key);
    $this->db->where('action', 'bankruptcy');
    $this->db->where('created >= ', $last_load);
    $query = $this->db->get();
    $result = $query->result_array();
    return $result;
 }

}