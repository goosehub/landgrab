<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
    // echo '<br>' . $this->db->last_query() . '<br>';

Class cron_model extends CI_Model
{
 // Get accounts in world
 function get_accounts_in_world($world_key)
 {
   $this->db->select('*');
   $this->db->from('account');
   $this->db->where('world_key', $world_key);
   $query = $this->db->get();
   return $query->result_array();
 }
}
?>