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
 function update_land_data($world_key, $claimed, $coord_slug, $account_key, $land_name, $content, $color)
 {
    $data = array(
        'claimed' => $claimed,
        'account_key' => $account_key,
        'land_name' => $land_name,
        'content' => $content,
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

}
?>