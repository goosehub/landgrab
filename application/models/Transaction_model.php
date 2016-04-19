<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class transaction_model extends CI_Model
{
// Insert new transaction log
 function new_transaction_record($paying_account_key, $recipient_account_key, $transaction, $amount, $world_key, $coord_slug, $name_at_sale, $details)
 {
    $data = array(
    'paying_account_key' => $paying_account_key,
    'recipient_account_key' => $recipient_account_key,
    'transaction' => $transaction,
    'amount' => $amount,
    'world_key' => $world_key,
    'coord_slug' => $coord_slug,
    'name_at_sale' => $name_at_sale,
    'details' => $details
    );
    $this->db->insert('transaction_log', $data);
 }
 // Get purcheses
 function get_transaction_purchases($paying_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum, COUNT(*) as total');
    $this->db->from('transaction_log');
    $this->db->where('paying_account_key', $paying_account_key);
    $this->db->where('transaction', 'buy');
    $this->db->where('created >= DATE(NOW()) - INTERVAL ' . $day_timeframe . ' DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get sales
 function get_transaction_sales($recipient_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum, COUNT(*) as total');
    $this->db->from('transaction_log');
    $this->db->where('recipient_account_key', $recipient_account_key);
    $this->db->where('transaction', 'buy');
    $this->db->where('created >= DATE(NOW()) - INTERVAL ' . $day_timeframe . ' DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get transaction losses
 function get_transaction_losses($paying_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum');
    $this->db->from('transaction_log');
    $this->db->where('paying_account_key', $paying_account_key);
    $this->db->where('created >= DATE(NOW()) - INTERVAL ' . $day_timeframe . ' DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get transaction profit
 function get_transaction_gains($recipient_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum');
    $this->db->from('transaction_log');
    $this->db->where('recipient_account_key', $recipient_account_key);
    $this->db->where('created >= DATE(NOW()) - INTERVAL ' . $day_timeframe . ' DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get recent sold lands
 function sold_lands_by_account_over_period($account_key, $last_load)
 {
    $this->db->select('*');
    $this->db->from('transaction_log');
    $this->db->where('recipient_account_key', $account_key);
    $this->db->where('transaction', 'buy');
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
    $this->db->from('transaction_log');
    $this->db->where('recipient_account_key', $account_key);
    $this->db->where('transaction', 'bankruptcy');
    $this->db->where('created >= ', $last_load);
    $query = $this->db->get();
    $result = $query->result_array();
    return $result;
 }

}