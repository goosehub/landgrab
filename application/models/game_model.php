<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
// echo '<br>' . $this->db->last_query() . '<br>';

Class game_model extends CI_Model
{
 // Get all lands
 function get_all_lands()
 {
    $this->db->select('*');
    $this->db->from('land');
    $query = $this->db->get();
    return $query->result_array();
 }
 // Get single land
 function get_single_land($coord_key)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('coord_key', $coord_key);
    $query = $this->db->get();
    return $query->result_array();
 }
 // Update land data
 function update_land_data($claimed, $coord_key, $lat, $lng, $user_key, $land_name, $price, $content, $primary_color)
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
    $this->db->update('land', $data);
    return true;
 }
 // Do land sale if possible
 function land_sale($selling_owner_id, $buying_owner_id, $new_selling_owner_cash, $new_buying_owner_cash)
 {
    // Seller add cash
    $data = array(
        'cash' => $new_selling_owner_cash
    );
    $this->db->where('id', $selling_owner_id);
    $this->db->update('user', $data);
    echo '<br>' . $this->db->last_query() . '<br>';

    // Buyer detuct cash
    $data = array(
        'cash' => $new_buying_owner_cash
    );
    $this->db->where('id', $buying_owner_id);
    $this->db->update('user', $data);
    echo '<br>' . $this->db->last_query() . '<br>';

    return true;
 }

}
?>