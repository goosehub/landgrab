<?php

Class game_model extends CI_Model
{
 function get_all_grids()
 {
    $this->db->select('*');
    $this->db->from('grid');
    $query = $this->db->get();
    return $query->result_array();
 }
 function get_single_grid($coord_key)
 {
    $this->db->select('*');
    $this->db->from('grid');
    $this->db->where('coord_key', $coord_key);
    $query = $this->db->get();
    return $query->result_array();
 }

}
?>