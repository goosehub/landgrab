<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class world_model extends CI_Model
{
	function create_new_world($account_key, $slug, $is_private)
	{
		$world_key = $this->insert_world($account_key, $slug, $is_private);
		$tiles_json = file_get_contents(TILES_JSON_PATH);
		if (!$tiles_json) {
			api_error_response('cant_get_world_file', 'Can not get world file.');
		}
		$tiles = json_decode($tiles_json);
		$tile_data = $this->structure_tile_data($tiles, $world_key);
		$this->db->insert_batch('tile', $tile_data);
		return $world_key;
	}
	function structure_tile_data($tiles, $world_key)
	{
		$structured_data = array();
		foreach ($tiles as $tile) {
			$structured_data[] = array(
				'lat' => $tile[0],
				'lng' => $tile[1],
				'terrain_key' => $tile[2],
				'world_key' => $world_key,
				'is_capitol' => 0,
				'is_base' => 0,
			);
		}
		return $structured_data;
	}
	function insert_world($account_key, $slug, $is_private)
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