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
 // Get all lands recently updated
 function get_all_lands_in_world_recently_updated($world_key, $update_timespan)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('modified >', date('Y-m-d H:i:s', time() - $update_timespan));
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
 function update_land_data($world_key, $coord_slug, $account_key, $land_name, $content, $land_type, $color)
 {
    $data = array(
        'account_key' => $account_key,
        'land_name' => $land_name,
        'content' => $content,
        'land_type' => $land_type,
        'color' => $color,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;
 }
 // Update land capitol status
 function update_land_capitol_status($world_key, $coord_slug, $capitol)
 {
    $data = array(
        'capitol' => $capitol
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;
 }
 // Remove capitol from account
 function remove_capitol_from_account($world_key, $account_key)
 {
    $data = array(
        'capitol' => 0
    );
    $this->db->where('account_key', $account_key);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;

 }
 // Update land content
 function update_land_content($world_key, $coord_slug, $content)
 {
    $data = array(
        'content' => $content,
        'modified' => date('Y-m-d H:i:s', time())
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
 // Get accounts in world
 function get_accounts_in_world($world_key)
 {
   $this->db->select('*');
   $this->db->from('account');
   $this->db->where('world_key', $world_key);
   $query = $this->db->get();
   return $query->result_array();
 }
 // Get active accounts in world
 function get_active_accounts_in_world($world_key)
 {
   $this->db->select('*');
   $this->db->from('account');
   $this->db->where('world_key', $world_key);
   $this->db->where('active_account', 1);
   $query = $this->db->get();
   return $query->result_array();
 }
 // Upgrade land type
 function upgrade_land_type($coord_slug, $world_key, $land_type)
 {
    $data = array(
        'land_type' => $land_type,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;
 }
 // Mark account as active
 function update_account_active_state($account_id, $active_state)
 {
    // Update account
    $data = array(
        'active_account' => $active_state
    );
    $this->db->where('id', $account_id);
    $this->db->update('account', $data);
    return true;
 }

}
?>