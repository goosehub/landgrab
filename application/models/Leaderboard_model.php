<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class leaderboard_model extends CI_Model
{
 // leaderboard_land_owned
 function leaderboard_land_owned($world_key)
 {
    $this->db->select('*, COUNT(*) as total');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('land_type !=', 0);
    $this->db->limit(10);
    $this->db->group_by('account_key');
    $this->db->order_by('COUNT(*)', 'desc');
    $query = $this->db->get();
    return $query->result_array();
 }
 // leaderboard_cities
 function leaderboard_cities($world_key)
 {
    $this->db->select('*, COUNT(*) as total');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('land_type !=', 0);
    $this->db->where('land_type', 'city');
    $this->db->limit(10);
    $this->db->group_by('account_key');
    $this->db->order_by('COUNT(*)', 'desc');
    $query = $this->db->get();
    return $query->result_array();
 }
 // leaderboard_strongholds
 function leaderboard_strongholds($world_key)
 {
    $this->db->select('*, COUNT(*) as total');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('land_type !=', 0);
    $this->db->where('land_type', 'stronghold');
    $this->db->limit(10);
    $this->db->group_by('account_key');
    $this->db->order_by('COUNT(*)', 'desc');
    $query = $this->db->get();
    return $query->result_array();
 }
 // leaderboard_army
 function leaderboard_army($world_key)
 {
    $this->db->select('*');
    $this->db->from('account');
    $this->db->where('world_key', $world_key);
    $this->db->limit(10);
    $query = $this->db->get();
    return $query->result_array();
 }
 // leaderboard_population
 function leaderboard_population($world_key)
 {
    $this->db->select('*');
    $this->db->from('account');
    $this->db->where('world_key', $world_key);
    $this->db->limit(10);
    $query = $this->db->get();
    return $query->result_array();
 }

}
?>