<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
// echo '<br>' . $this->db->last_query() . '<br>';

Class game_model extends CI_Model
{
 // Get world by id
 function get_world($world_id)
 {
    $this->db->select('*');
    $this->db->from('world');
    $this->db->where('id', $world_id);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Get world by slug
 function get_world_by_slug_or_id($slug)
 {
    $this->db->select('*');
    $this->db->from('world');
    $this->db->where('slug', $slug);
    $this->db->or_where('id', $slug);
    $this->db->limit(1);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Get all lands
 function get_all_lands_in_world($world_key)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $query = $this->db->get();
    return $query->result_array();
 }
 // Get single land
 function get_single_land($world_key, $coord_slug)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Update land data
 function update_land_data($world_key, $claimed, $coord_slug, $account_key, $land_name, $content, $land_type, $color)
 {
    $data = array(
        'claimed' => $claimed,
        'account_key' => $account_key,
        'land_name' => $land_name,
        'content' => $content,
        'land_type' => $land_type,
        'color' => $color
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;
 }
 // Update land content
 function update_land_content($world_key, $coord_slug, $content)
 {
    $data = array(
        'content' => $content
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;    
 }
 // Check if any immediate squares belong to current account
 function land_range_check($world_key, $account_key, $coord_array)
 {
    $this->db->select('id');
    $this->db->from('land');
    $this->db->where_in('coord_slug', $coord_array);
    $this->db->where('account_key', $account_key);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Update account active army
 function update_account_active_army($account_key, $active_army)
 {
    $data = array(
        'active_army' => $active_army
    );
    $this->db->where('id', $account_key);
    $this->db->update('account', $data);
    return true;    
 }
 // Update account passive army
 function update_account_passive_army($account_key, $passive_army)
 {
    $data = array(
        'passive_army' => $passive_army
    );
    $this->db->where('id', $account_key);
    $this->db->update('account', $data);
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
 // Upgrade land type
 function upgrade_land_type($coord_slug, $world_key, $land_type)
 {
    $data = array(
        'land_type' => $land_type
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;
 }

}
?>