<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

}
?>