<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class world_model extends CI_Model
{
	function create_new_world($account_key, $world_name, $slug, $is_private)
	{
		$world_key = $this->insert_world($account_key, $world_name, $slug, $is_private);
		$tiles_json = file_get_contents(TILES_JSON_PATH);
		if (!$tiles_json) {
			api_error_response('cant_get_world_file', 'Can not get world file.');
		}
		$tiles = json_decode($tiles_json);
		$tile_data = $this->structure_tile_data($tiles, $world_key);
		$this->db->insert_batch('tile', $tile_data);
		$this->world_reset_regenerate_resources($world_key);
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
	function insert_world($account_key, $world_name, $slug, $is_private)
	{
		$data = array(
			'name' => $world_name,
			'slug' => $slug,
			'is_private' => $is_private,
			'creator_account_key' => $account_key,
			'tile_size' => TILE_SIZE,
		);
		$this->db->insert('world', $data);
		return $this->db->insert_id();
	}
	function world_reset_regenerate_resources($world_key)
	{
		$this->reset_resources($world_key);
		$resources = $this->game_model->get_all('resource');
		foreach ($resources as $resource) {
			$this->resource_distribute($resource);
			$this->db->where('lat > ', LOWEST_LAT_RESOURCE_GEN);
			$this->db->where('resource_key', NULL);
			$this->db->where('world_key', $world_key);
			$this->db->order_by('RAND()');
			$this->db->limit($resource['frequency_per_world']);
			$data = array(
				'resource_key' => $resource['id'],
			);
			$this->db->update('tile', $data);
		}
	}
	function reset_resources($world_key)
	{
		$data = array(
			'resource_key' => null,
		);
		$this->db->where('world_key', $world_key);
		$this->db->update('tile', $data);
	}
	function resource_distribute($resource)
	{
		$this->db->group_start();
		// Light bias against barren
		if ($resource['spawns_in_barren'] && mt_rand(0,10) > BARREN_BIAS) {
			$this->db->where('terrain_key', BARREN_KEY);
		}
		if ($resource['spawns_in_mountain']) {
			$this->db->or_where('terrain_key', MOUNTAIN_KEY);
		}
		// Strong bias against tundra
		if ($resource['spawns_in_tundra'] && mt_rand(0,10) > TUNDRA_BIAS) {
			$this->db->or_where('terrain_key', TUNDRA_KEY);
		}
		if ($resource['spawns_in_coastal']) {
			$this->db->or_where('terrain_key', COASTAL_KEY);
		}
		$this->db->group_end();
	}
}
?>