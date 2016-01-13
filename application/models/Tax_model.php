<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
    // echo '<br>' . $this->db->last_query() . '<br>';

Class tax_model extends CI_Model
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
 // Record most recent rebate
 function record_latest_rebate_into_world($world_key, $latest_rebate)
 {
    $data = array(
        'latest_rebate' => $latest_rebate
    );
    $this->db->where('id', $world_key);
    $this->db->update('world', $data);
    return true;
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
}
?>