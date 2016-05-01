<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class leaderboard_model extends CI_Model
{
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

}
?>