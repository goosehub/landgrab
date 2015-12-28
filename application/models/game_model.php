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
    return $query->result_array();
 }
 // Get world by slug
 function get_world_by_slug($slug)
 {
    $this->db->select('*');
    $this->db->from('world');
    $this->db->where('slug', $slug);
    $query = $this->db->get();
    return $query->result_array();
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
 function get_single_land($world_key, $coord_key)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('coord_key', $coord_key);
    $this->db->where('world_key', $world_key);
    $query = $this->db->get();
    return $query->result_array();
 }
 // Update land data
 function update_land_data($world_key, $claimed, $coord_key, $lat, $lng, $user_key, $land_name, $price, $content, $primary_color)
 {
    $data = array(
        'claimed' => $claimed,
        'coord_key' => $coord_key,
        'lat' => $lat,
        'lng' => $lng,
        'user_key' => $user_key,
        'land_name' => $land_name,
        'price' => $price,
        'content' => $content,
        'primary_color' => $primary_color
    );
    $this->db->where('coord_key', $coord_key);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;
 }
 // Do land sale if possible
 function land_sale($world_key, $selling_owner_id, $buying_owner_id, $new_selling_owner_cash, $new_buying_owner_cash)
 {
    // Seller add cash
    $data = array(
        'cash' => $new_selling_owner_cash
    );
    $this->db->where('id', $selling_owner_id);
    $this->db->update('user', $data);

    // Buyer detuct cash
    $data = array(
        'cash' => $new_buying_owner_cash
    );
    $this->db->where('id', $buying_owner_id);
    $this->db->update('user', $data);

    return true;
 }

}
?>