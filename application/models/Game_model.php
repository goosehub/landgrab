	<?php
	defined('BASEPATH')
	 OR exit('No direct script access allowed');

	Class game_model extends CI_Model
	{
		function get_all($table)
		{
			$this->db->select('*');
			$this->db->from($table);
			$query = $this->db->get();
			return $query->result_array();
		}
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
		function get_tile($lat, $lng, $world_key)
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
		function get_tile_by_id($tile_id)
		{
			$this->db->select('*');
			$this->db->from('tile');
			$this->db->where('id', $tile_id);
			$query = $this->db->get();
			$result = $query->result_array();
			return isset($result[0]) ? $result[0] : false;
		}
		function get_capitol_tile_by_account($account_key)
		{
			$this->db->select('*');
			$this->db->from('tile');
			$this->db->where('account_key', $account_key);
			$this->db->where('is_capitol', 1);
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
		function update_tile_terrain($world_key, $lng, $lat, $terrain_key)
		{
			$data = array(
				'terrain_key' => $terrain_key,
			);
			$this->db->where('world_key', $world_key);
			$this->db->where('lat', $lat);
			$this->db->where('lng', $lng);
			$this->db->update('tile', $data);
		}
		function update_tile_name($tile_id, $tile_name)
		{
			$data = array(
				'tile_name' => $tile_name,
			);
			$this->db->where('id', $tile_id);
			$this->db->update('tile', $data);
		}
		function update_tile_desc($tile_id, $tile_desc)
		{
			$data = array(
				'tile_desc' => $tile_desc,
			);
			$this->db->where('id', $tile_id);
			$this->db->update('tile', $data);
		}
		function update_tile_settlement($tile_id, $settlement_key)
		{
			$data = array(
				'settlement_key' => $settlement_key,
				'population' => $this->settlements[$settlement_key - 1]['base_population'],
			);
			$this->db->where('id', $tile_id);
			$this->db->update('tile', $data);
		}
		function update_tile_industry($tile_id, $industry_key = null)
		{
			$data = array(
				'industry_key' => $industry_key,
				'is_capitol' => (int)$industry_key === CAPITOL_INDUSTRY_KEY,
				'is_base' => (int)$industry_key === BASE_INDUSTRY_KEY,
			);
			$this->db->where('id', $tile_id);
			$this->db->update('tile', $data);
		}
		function update_account_laws($account_id, $government, $tax_rate, $ideology)
		{
			$data = array(
				'government' => $government,
				'tax_rate' => $tax_rate,
				'ideology' => $ideology,
				'last_law_change' => date('Y-m-d H:i:s', time())
			);
			$this->db->where('id', $account_id);
			$this->db->update('account', $data);
		}
		function get_account_supplies($account)
		{
			$this->db->select('*');
			$this->db->from('supply');
			$this->db->join('supply_account_lookup', 'supply_account_lookup.supply_key = supply.id', 'left');
			$this->db->where('supply_account_lookup.account_key', $account);
			$query = $this->db->get();
			return $query->result_array();
		}
		function tile_is_incorporated($settlement_key)
		{
			$settlement_key = (int)$settlement_key;
			return $settlement_key === TOWN_KEY || $settlement_key === CITY_KEY || $settlement_key === METRO_KEY;
		}
		function first_claim($tile,$account) {
			$data = array(
				'account_key' => $account['id'],
				'settlement_key' => TOWN_KEY,
				'industry_key' => CAPITOL_INDUSTRY_KEY,
				'population' => $this->settlements[TOWN_KEY - 1]['base_population'],
				'unit_key' => INFANTRY_KEY,
				'unit_owner_key' => $account['id'],
				'unit_owner_color' => $account['color'],
				'is_capitol' => 1,
				'tile_name' => 'Capitol of ' . $account['nation_name'],
				'tile_desc' => 'Founded on ' . date('l jS \of F Y h:i A T'),
				'color' => $account['color'],
			);
			$this->db->where('lat', $tile['lat']);
			$this->db->where('lng', $tile['lng']);
			$this->db->update('tile', $data);
		}
		function claim($tile, $account, $unit_key) {
			$data = array(
				'account_key' => $account['id'],
				'settlement_key' => UNINHABITED_KEY,
				'industry_key' => NULL,
				'unit_key' => $unit_key,
				'unit_owner_key' => $account['id'],
				'unit_owner_color' => $account['color'],
				'is_capitol' => 0,
				'color' => $account['color'],
			);
			$this->db->where('lat', $tile['lat']);
			$this->db->where('lng', $tile['lng']);
			$this->db->update('tile', $data);
		}
		function put_unit_on_tile($tile, $account, $unit_key) {
			$data = array(
				'unit_key' => $unit_key,
				'unit_owner_key' => $account['id'],
				'unit_owner_color' => $account['color'],
			);
			$this->db->where('lat', $tile['lat']);
			$this->db->where('lng', $tile['lng']);
			$this->db->update('tile', $data);
		}
		function remove_unit_from_previous_tile($world_key, $lat, $lng) {
			$data = array(
				'unit_key' => NULL,
				'unit_owner_key' => NULL,
				'unit_owner_color' => NULL,
			);
			$this->db->where('lat', $lat);
			$this->db->where('lng', $lng);
			$this->db->update('tile', $data);
		}
		function increment_account_supply($account_key, $supply_key, $amount = 1) {
			$this->db->set('amount', 'amount + ' . $amount, FALSE);
			$this->db->where('account_key', $account_key);
			$this->db->where('supply_key', $supply_key);
			$this->db->update('supply_account_lookup');
		}
		function decrement_account_supply($account_key, $supply_key, $amount = 1) {
			$this->db->set('amount', 'amount - ' . $amount, FALSE);
			$this->db->where('account_key', $account_key);
			$this->db->where('supply_key', $supply_key);
			$this->db->update('supply_account_lookup');
		}
		// 
		// 
		// 
	    function get_tile_border_color($tile)
	    {
	        $fill_color = "#FFFFFF";
	        if ($tile['account_key']) {
	        	$fill_color = $tile['color'];
	        }
	        return $fill_color;
	    }
	    function get_tile_terrain_color($tile)
	    {
	        $fill_color = "#FFFFFF";
	        if ($tile['terrain_key'] == FERTILE_KEY) {
	            $fill_color = FERTILE_COLOR;
	        }
	        if ($tile['terrain_key'] == BARREN_KEY) {
	            $fill_color = BARREN_COLOR;
	        }
	        if ($tile['terrain_key'] == MOUNTAIN_KEY) {
	            $fill_color = MOUNTAIN_COLOR;
	        }
	        if ($tile['terrain_key'] == TUNDRA_KEY) {
	            $fill_color = TUNDRA_COLOR;
	        }
	        if ($tile['terrain_key'] == COASTAL_KEY) {
	            $fill_color = COASTAL_COLOR;
	        }
	        if ($tile['terrain_key'] == OCEAN_KEY) {
	            $fill_color = OCEAN_COLOR;
	        }
	        return $fill_color;
	    }
	    function tiles_are_adjacent($start_lat, $start_lng, $end_lat, $end_lng) {
		    // Ignore if ending same place we started
		    if ($start_lat === $end_lat && $start_lng === $end_lng) {
		      return false;
		    }
		    // Check if one is changed by 1, and other is the same
		    $allowed_lats = [$start_lat, $start_lat + TILE_SIZE, $start_lat - TILE_SIZE];
		    $allowed_lngs = [$start_lng, $this->correct_lng($start_lng + TILE_SIZE), $this->correct_lng($start_lng - TILE_SIZE)];
		    if (
		      (in_array($end_lat, $allowed_lats) && $start_lng === $end_lng) || 
		      (in_array($end_lng, $allowed_lngs) && $start_lat === $end_lat)
		      ) {
		      return true;
		    }
		    return false;
	    }
	    function correct_lng($lng) {
	    	if ($lng === 182) {
	    	  $lng = -178;
	    	}
	    	if ($lng === -180) {
	    	  $lng = 180;
	    	}
	    	return $lng;
	    }
		function remove_capitol($account_id) {
			$tile = $this->get_capitol_tile_by_account($account_id);
			$data = array(
				'is_capitol' => false,
				'industry_key' => NULL,
			);
			$this->db->where('id', $tile['id']);
			$this->db->update('tile', $data);
	        $this->game_model->update_tile_industry($tile['id']);
		}
	}