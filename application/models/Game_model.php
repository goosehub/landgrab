<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class game_model extends CI_Model
{
	function get_world($world_id)
	{
		$this->db->select('*');
		$this->db->from('world');
		$this->db->where('id', $world_id);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
	}
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
    function get_all_worlds()
    {
        $this->db->select('*');
        $this->db->from('world');
        $query = $this->db->get();
        return $query->result_array();
    }

	function get_all_tiles_in_world($world_key)
	{
		$this->db->select('*');
		$this->db->from('tile');
		$this->db->where('world_key', $world_key);
		$query = $this->db->get();
		return $query->result_array();
	}
	function get_all_tiles_in_world_recently_updated($world_key, $update_timespan)
	{
		$this->db->select('*');
		$this->db->from('tile');
		$this->db->where('world_key', $world_key);
		$this->db->where('modified >', date('Y-m-d H:i:s', time() - $update_timespan));
		$query = $this->db->get();
		return $query->result_array();
	}
}