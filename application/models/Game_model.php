<?php
defined('BASEPATH')
 OR exit('No direct script access allowed');

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
	function get_world_by_slug($slug)
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
	function get_world_by_id($world_id)
	{
		$this->db->select('*');
		$this->db->from('world');
		$this->db->or_where('id', $world_id);
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
	function get_single_tile($lat, $lng, $world_key)
	{
		$this->db->select('*');
		$this->db->from('tile');
		$this->db->where('lat', $lat);
		$this->db->where('lng', $lng);
		$this->db->where('world_key', $world_key);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
	}
	function get_count_of_account_tile($account_key)
	{
		$this->db->select('COUNT(id) as count');
		$this->db->from('tile');
		$this->db->where('account_key', $account_key);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]['count']) ? $result[0]['count'] : 0;
	}
	function tile_range_check($world_key, $account_key, $coords)
	{
		$this->db->select('id');
		$this->db->from('tile');
		$where = "(
			(lat=".$coords[0]['lat']." AND lng=".$coords[0]['lng'].")
		 OR (lat=".$coords[1]['lat']." AND lng=".$coords[1]['lng'].")
		 OR (lat=".$coords[2]['lat']." AND lng=".$coords[2]['lng'].")
		 OR (lat=".$coords[3]['lat']." AND lng=".$coords[3]['lng'].")
		)";
		$this->db->where($where);
		$this->db->where('account_key', $account_key);
		$this->db->where(function(){

		});
		$this->db->where_in('coord_slug', $coord_array);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
	}
	function update_tile_terrain($lng, $lat, $terrain_key)
	{
		$data = array(
			'terrain_key' => $terrain_key,
		);
		$this->db->where('lat', $lat);
		$this->db->where('lng', $lng);
		$this->db->update('tile', $data);
		return true;
	}
	function update_account_laws($account_id, $government, $tax_rate)
	{
		$data = array(
			'government' => $government,
			'tax_rate' => $tax_rate,
		);
		$this->db->where('id', $account_id);
		$this->db->update('account', $data);
		return true;
	}
}