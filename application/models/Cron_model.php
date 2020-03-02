<?php
defined('BASEPATH')
 OR exit('No direct script access allowed');

Class cron_model extends CI_Model
{
	function regenerate_resources($world_key)
	{
		$resources = $this->game_model->get_all_resources();
		foreach ($resources as $resource) {
			if ($resource['spawns_in_barren']) {
				$this->db->where('terrain_key', BARREN_KEY);
			}
			if ($resource['spawns_in_mountain']) {
				$this->db->or_where('terrain_key', MOUNTAIN_KEY);
			}
			if ($resource['spawns_in_tundra']) {
				$this->db->or_where('terrain_key', TUNDRA_KEY);
			}
			if ($resource['spawns_in_coastal']) {
				$this->db->or_where('terrain_key', COASTAL_KEY);
			}
			$this->db->order_by('RAND()');
			$this->db->limit($resource['frequency_per_world']);
			// var_dump($resource['id']);
			// die();
			$data = array(
				'resource_key' => $resource['id'],
			);
			$this->db->update('tile', $data);
		}
	}
}