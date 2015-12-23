<?php

Class game_model extends CI_Model
{
 function get_all_grids()
 {
    $this->db->select('*');
    $this->db->from('grid');
    $query = $this->db->get();
    return $query->result();
 }

}
?>