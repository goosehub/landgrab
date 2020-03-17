<?php
defined('BASEPATH')
 OR exit('No direct script access allowed');

Class cron_model extends CI_Model
{
    function world_resets()
    {
        $force_reset_world_id = isset($_GET['world_id']) ? $_GET['world_id'] : false;
        $now = date('Y-m-d H:i:s');
        $worlds = $this->game_model->get_all('world');
        foreach ($worlds as $world) {
            // Check if it's time to run
            $time_to_reset = parse_crontab($now, $world['crontab']);
            if (!$time_to_reset && !$force_reset_world_id) {
                continue;
            }
            if ($force_reset_world_id) {
                if ($force_reset_world_id != $world['id']) {
                    continue;
                }
            }
            echo 'Resetting world ' . $world['id'] . ' - ' . $world['slug'] . ' - ';
            // $this->backup();
            // $this->reset_tiles();
            // $this->reset_trades();
            // $this->reset_accounts();
            $this->regenerate_resources($world['id']);
        }
    }
	function regenerate_resources($world_key)
	{
		$this->reset_resources($world_key);
		$resources = $this->game_model->get_all('resource');
		foreach ($resources as $resource) {
			$this->resource_distribute($resource);
			$this->db->where('lat > ', LOWEST_LAT_RESOURCE_GEN);
			$this->db->where('resource_key', NULL);
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