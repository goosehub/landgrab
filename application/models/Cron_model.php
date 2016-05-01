<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
    // echo '<br>' . $this->db->last_query() . '<br>';

Class cron_model extends CI_Model
{
  // Get world tax info
  function get_world_tax_info($world_key)
  {
    $this->db->select('SUM(price) as price_tally, COUNT(*) as land_tally');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('claimed', 1);
    $query = $this->db->get();
    return $query->result_array();
  }
 // Get accounts in world
 function get_accounts_in_world($world_key)
 {
   $this->db->select('*');
   $this->db->from('account');
   $this->db->where('world_key', $world_key);
   $query = $this->db->get();
   return $query->result_array();
 }
 // Get tax info from account
 function get_account_for_taxes($account_key)
 {
    $this->db->select('SUM(price) as price_tally, COUNT(*) as land_tally');
    $this->db->from('land');
    $this->db->where('account_key', $account_key);
    $query = $this->db->get();
    return $query->result_array();
 }
 // Get unique sales by account
 function get_account_unique_sales_tally($account_key)
 {
    $this->db->select('COUNT(*) as unique_sales');
    $this->db->from('transaction_log');
    $this->db->where('recipient_account_key', $account_key);
    $this->db->where('transaction', 'buy');
    $this->db->where('created >= DATE(NOW()) - INTERVAL 1 DAY');
    $this->db->group_by('coord_slug');
    $query = $this->db->get();
    return $query->result_array();
 }
 // Forfeit all land of account
 function forfeit_all_land_of_account($account_id, $price)
 {
    $data = array(
        'claimed' => 0,
        'account_key' => 0,
        'price' => $price
    );
    $this->db->where('account_key', $account_id);
    $this->db->update('land', $data);
    return true;
 }
 // Get owned cities
 function get_owned_cities($account_key)
 {
    $this->db->select('COUNT(*) as owned_cities');
    $this->db->from('land');
    $this->db->where('account_key', $account_key);
    $this->db->where('city', 1);
    $query = $this->db->get();
    return $query->result_array();
  }
}
?>