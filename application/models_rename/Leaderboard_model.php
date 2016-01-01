<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class leaderboard_model extends CI_Model
{
 // leaderboard_net_value
 function leaderboard_net_value($world_key)
 {
 }
 // leaderboard_land_owned
 function leaderboard_land_owned($world_key)
 {
    $this->db->select('*, COUNT(*)');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('claimed', 1);
    $this->db->limit(10);
    $this->db->group_by('account_key');
    $this->db->order_by('COUNT(*)', 'desc');
    $query = $this->db->get();
    return $query->result_array();
 }
 // leaderboard_cash_owned
 function leaderboard_cash_owned($world_key)
 {
    $this->db->select('id, user_key, primary_color, cash');
    $this->db->from('account');
    $this->db->where('world_key', $world_key);
    $this->db->order_by('cash', 'desc');
    $this->db->limit(10);
    $query = $this->db->get();
    return $query->result_array();
 }
 // leaderboard_highest_valued_land
 function leaderboard_highest_valued_land($world_key)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('claimed', 1);
    $this->db->order_by('price', 'desc');
    $this->db->limit(10);
    $query = $this->db->get();
    return $query->result_array();
 }
 // leaderboard_cheapest_land
 function leaderboard_cheapest_land($world_key)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('claimed', 1);
    $this->db->order_by('price', 'asc');
    $this->db->limit(10);
    $query = $this->db->get();
    return $query->result_array();
 }

}
?>