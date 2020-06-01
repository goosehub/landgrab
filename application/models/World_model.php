<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class world_model extends CI_Model
{
	function create_new_world($account_key, $slug, $is_private)
	{
		$create_world_sql = file_get_contents(WORLD_SQL_PATH);
		$create_tiles_sql = file_get_contents(TILES_SQL_PATH);
		if (!$create_world_sql) {
			api_error_response('cant_get_world_file', 'Can not get world file.');
		}
		if (!$create_tiles_sql) {
			api_error_response('cant_get_tiles_file', 'Can not get tiles file.');
		}
		$create_world_sql = mb_convert_encoding($create_world_sql, "UTF-8");
		$create_tiles_sql = mb_convert_encoding($create_tiles_sql, "UTF-8");
		$next_world_key = $this->next_world_key();
		$create_world_sql = str_replace('world_name', $slug, $create_world_sql);
		$create_world_sql = str_replace(DEFAULT_WORLD_KEY, $next_world_key, $create_world_sql);
		$create_tiles_sql = str_replace(DEFAULT_WORLD_KEY, $next_world_key, $create_tiles_sql);
		// dd($create_world_sql);
		$this->db->query($create_world_sql);
		// echo $create_tiles_sql;
		// die();
		$this->db->query($create_tiles_sql);
		return $next_world_key;
	}
	function next_world_key()
	{
		$this->db->select('id');
		$this->db->from('world');
		$this->db->limit(1);
		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
		$result = $query->result_array();
		if (!isset($result[0])) {
			api_error_response('no_worlds_found', 'There is no world in the database');
		}
		return (int)$result[0]['id'] + 1;
	}
	function create_new_world___legacy()
	{
		$data = array(
			'slug' => $slug,
			'is_private' => $is_private,
			'creator_account_key' => $account_key,
			'tile_size' => TILE_SIZE,
		);
		$this->db->insert('world', $data);
		return $this->db->insert_id();
	}
}
?>