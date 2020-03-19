<?php
defined('BASEPATH')
 OR exit('No direct script access allowed');

Class cron_model extends CI_Model
{
	function increase_support()
	{
		// increment supply by power structure type for all accounts
		// set to 100 when more than 100
	}
	function mark_accounts_as_active()
	{
		// If account tiles > 0, set is_active to true, else set is_active to false
	}
	function census_population()
	{
		// foreach world
		// foreach account
		// set population supply to population of all territories
	}
	function settlement_output()
	{
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			INNER JOIN settlement AS settlement_by_supply ON sal.supply_key = settlement_by_supply.output_supply_key
			INNER JOIN (
				SELECT settlement_key, account_key, COUNT(*) as tile_count
				FROM tile
				GROUP BY settlement_key, account_key
			) AS tile_by_settlement_and_account ON settlement_by_supply.id = tile_by_settlement_and_account.settlement_key AND sal.account_key = tile_by_settlement_and_account.account_key
			SET sal.amount = sal.amount + (tile_by_settlement_and_account.tile_count * settlement_by_supply.output_supply_amount)
		");
	}
	function township_input()
	{
		// town
		// city
		// metro
	}
	function industry_output_input()
	{
		// foreach world
		// foreach account
		// foreach settlement that has a township ordered by population desc
		// if does not have all inputs, mark is_insufficient_industry_input
		// subtract from supply the inputs
		// create outputs
	}
	function income_collect()
	{
		// foreach world
		// foreach account
		// get sum settlement GDP where not insufficient_input
		// get sum industry GDP where not insufficient_input
		// get taxed income
		// get subtract corruption
		// apply earnings
	}
	function update_cache_leaderboards()
	{
		// foreach supply
		// get top 100 accounts by that supply
		// generate datetime plus world id identified json
	}
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