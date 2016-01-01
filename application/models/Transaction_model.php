<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class transaction_model extends CI_Model
{
// Insert new transaction log
 function new_transaction_record($paying_account_key, $recipient_account_key, $transaction, $amount, $world_key, $coord_slug, $details)
 {
    $data = array(
    'paying_account_key' => $paying_account_key,
    'recipient_account_key' => $recipient_account_key,
    'transaction' => $transaction,
    'amount' => $amount,
    'world_key' => $world_key,
    'coord_slug' => $coord_slug,
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
    $this->db->where('created >= DATE(NOW()) - INTERVAL 7 DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // Get purcheses
 function get_transaction_sales($recipient_account_key, $day_timeframe)
 {
    $this->db->select('SUM(amount) as sum, COUNT(*) as total');
    $this->db->from('transaction_log');
    $this->db->where('recipient_account_key', $recipient_account_key);
    $this->db->where('transaction', 'buy');
    $this->db->where('created >= DATE(NOW()) - INTERVAL 7 DAY');
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
    $this->db->where('created >= DATE(NOW()) - INTERVAL 7 DAY');
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
    $this->db->where('created >= DATE(NOW()) - INTERVAL 7 DAY');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
}