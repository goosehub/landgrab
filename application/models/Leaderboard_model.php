<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class leaderboard_model extends CI_Model
{
    function leaderboard_land_owned($world_key, $limit)
    {
       $this->db->select('account_key, COUNT(*) as total');
       $this->db->from('land');
       $this->db->where('world_key', $world_key);
       $this->db->where('land_type !=', 1);
       $this->db->limit($limit);
       $this->db->group_by('account_key');
       $this->db->order_by('COUNT(*)', 'desc');
       $query = $this->db->get();
       return $query->result_array();
    }
}
?>