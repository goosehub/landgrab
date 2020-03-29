	<?php
	defined('BASEPATH')
	 OR exit('No direct script access allowed');

	Class game_model extends CI_Model
	{
		function get_all($table, $sort_column = false)
		{
			$this->db->select('*');
			$this->db->from($table);
			if ($sort_column) {
				$this->db->order_by($sort_column, 'ASC');
			}
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
			if (!$this->tile_is_township($settlement_key)) {
				$data['industry_key'] = NULL;
			}
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
		function update_account_laws($account_id, $power_structure, $tax_rate, $ideology)
		{
			$data = array(
				'power_structure' => $power_structure,
				'tax_rate' => $tax_rate,
				'ideology' => $ideology,
				'last_law_change' => date('Y-m-d H:i:s', time())
			);
			$this->db->where('id', $account_id);
			$this->db->update('account', $data);
		}
		function get_account_supplies($account_key)
		{
			$this->db->select('*');
			$this->db->from('supply');
			$this->db->join('supply_account_lookup', 'supply_account_lookup.supply_key = supply.id', 'left');
			$this->db->where('supply_account_lookup.account_key', $account_key);
			$query = $this->db->get();
			return $query->result_array();
		}
		function get_input_projections($account_key)
		{
			$settlements_grouped = $this->get_settlement_input_by_account($account_key);
			$settlement_input = $this->get_supplies_of_settlement_input($settlements_grouped);
			$industry_input = $this->get_industry_input_by_account($account_key);
			return $this->restructure_data(array_merge($settlement_input, $industry_input));
		}
		function get_output_projections($account_key)
		{
			$resource_output = $this->get_resource_output_by_account($account_key);
			$settlement_output = $this->get_settlement_output_by_account($account_key);
			$industry_output = $this->get_industry_output_by_account($account_key);
			return $this->restructure_data(array_merge($resource_output, $settlement_output, $industry_output));
		}
		function restructure_data($array)
		{
			$new_array = array();
			foreach ($array as $row) {
				if (isset($new_array[$row['output_supply_key']])) {
					$new_array[$row['output_supply_key']] += (int)$row['output_supply_amount'];
				}
				else {
					$new_array[$row['output_supply_key']] = (int)$row['output_supply_amount'];
				}
			}
			return $new_array;
		}
		function get_resource_output_by_account($account_key)
		{
			$this->db->select('COUNT(tile.id) as output_supply_amount, output_supply_key');
			$this->db->from('tile');
			$this->db->join('resource', 'resource.id = tile.resource_key', 'left');
			$this->db->where('tile.account_key', $account_key);
			$this->db->where('output_supply_key IS NOT NULL', NULL, FALSE);
			$this->db->group_by('resource_key');
			$query = $this->db->get();
			return $query->result_array();
		}
		function get_settlement_input_by_account($account_key)
		{
			$this->db->select('COUNT(tile.id) as settlement_count, settlement_key');
			$this->db->from('tile');
			$this->db->where('account_key', $account_key);
			$this->db->where_in('settlement_key', array(TOWN_KEY, CITY_KEY, METRO_KEY));
			$this->db->group_by('settlement_key');
			$query = $this->db->get();
			return $query->result_array();
		}
		function get_supplies_of_settlement_input($settlements_grouped)
		{
			$food = $cash_crops = $energy = $merchandise = $steel = $pharmaceuticals = 0;
			foreach ($settlements_grouped as $settlement) {
				if ($settlement['settlement_key'] == TOWN_KEY) {
					$food = $food + (TOWN_FOOD_COST * $settlement['settlement_count']);
					$energy = $energy + (TOWN_ENERGY_COST * $settlement['settlement_count']);
					$cash_crops = $cash_crops + (TOWN_CASH_CROPS_COST * $settlement['settlement_count']);
					$merchandise = $merchandise + (TOWN_MERCHANDISE_COST * $settlement['settlement_count']);
					$steel = $steel + (TOWN_STEEL_COST * $settlement['settlement_count']);
					$pharmaceuticals = $pharmaceuticals + (TOWN_PHARMACEUTICALS_COST * $settlement['settlement_count']);
				}
				else if ($settlement['settlement_key'] == CITY_KEY) {
					$food = $food + (CITY_FOOD_COST * $settlement['settlement_count']);
					$energy = $energy + (CITY_ENERGY_COST * $settlement['settlement_count']);
					$cash_crops = $cash_crops + (CITY_CASH_CROPS_COST * $settlement['settlement_count']);
					$merchandise = $merchandise + (CITY_MERCHANDISE_COST * $settlement['settlement_count']);
					$steel = $steel + (CITY_STEEL_COST * $settlement['settlement_count']);
					$pharmaceuticals = $pharmaceuticals + (CITY_PHARMACEUTICALS_COST * $settlement['settlement_count']);
				}
				else if ($settlement['settlement_key'] == METRO_KEY) {
					$food = $food + (METRO_FOOD_COST * $settlement['settlement_count']);
					$energy = $energy + (METRO_ENERGY_COST * $settlement['settlement_count']);
					$cash_crops = $cash_crops + (METRO_CASH_CROPS_COST * $settlement['settlement_count']);
					$merchandise = $merchandise + (METRO_MERCHANDISE_COST * $settlement['settlement_count']);
					$steel = $steel + (METRO_STEEL_COST * $settlement['settlement_count']);
					$pharmaceuticals = $pharmaceuticals + (METRO_PHARMACEUTICALS_COST * $settlement['settlement_count']);
				}
			}
			return array(
				array(
					'output_supply_amount' => $food,
					'output_supply_key' => FOOD_KEY,
				),
				array(
					'output_supply_amount' => $cash_crops,
					'output_supply_key' => CASH_CROPS_KEY,
				),
				array(
					'output_supply_amount' => $energy,
					'output_supply_key' => ENERGY_KEY,
				),
				array(
					'output_supply_amount' => $merchandise,
					'output_supply_key' => MERCHANDISE_KEY,
				),
				array(
					'output_supply_amount' => $steel,
					'output_supply_key' => STEEL_KEY,
				),
				array(
					'output_supply_amount' => $pharmaceuticals,
					'output_supply_key' => PHARMACEUTICALS_KEY,
				),
			);
		}
		function get_settlement_output_by_account($account_key)
		{
			$this->db->select('(COUNT(tile.id) * output_supply_amount) as output_supply_amount, output_supply_key');
			$this->db->from('settlement');
			$this->db->join('tile', 'settlement.id = tile.settlement_key', 'left');
			$this->db->where('account_key', $account_key);
			$this->db->where('output_supply_key IS NOT NULL', NULL, FALSE);
			$this->db->group_by('output_supply_key');
			$query = $this->db->get();
			return $query->result_array();
		}
		function get_industry_input_by_account($account_key)
		{
			$this->db->select('(COUNT(tile.id) * amount) as output_supply_amount, supply_key AS output_supply_key');
			$this->db->from('supply_industry_lookup');
			$this->db->join('tile', 'supply_industry_lookup.industry_key = tile.industry_key', 'left');
			$this->db->where('account_key', $account_key);
			$this->db->where('supply_key IS NOT NULL', NULL, FALSE);
			$this->db->group_by('supply_key');
			$query = $this->db->get();
			return $query->result_array();
		}
		function get_industry_output_by_account($account_key)
		{
			$this->db->select('(COUNT(tile.id) * output_supply_amount) as output_supply_amount, output_supply_key');
			$this->db->from('industry');
			$this->db->join('tile', 'industry.id = tile.industry_key', 'left');
			$this->db->where('account_key', $account_key);
			$this->db->where('output_supply_key IS NOT NULL', NULL, FALSE);
			$this->db->group_by('output_supply_key');
			$query = $this->db->get();
			return $query->result_array();
		}
		function tile_is_township($settlement_key)
		{
			$settlement_key = (int)$settlement_key;
			return $settlement_key === TOWN_KEY || $settlement_key === CITY_KEY || $settlement_key === METRO_KEY;
		}
		function first_claim($tile, $account) {
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
		function get_account_budget($account) {
			// To more accurately make reflect settlement_income_collect and industry_income_collect, calculate it separately
			$budget['base_gdp'] = $this->settlement_gdp($account['id']) + $this->industry_gdp($account['id']);
			$budget['gdp_bonus'] = $this->gdp_bonus($account);
			$bonus_multiplier = (1 + ($budget['gdp_bonus'] / 100));
			$budget['gdp'] = $budget['base_gdp'] * $bonus_multiplier;
			$budget['tax_income'] = $budget['gdp'] * ($account['tax_rate'] / 100);
			$running_income = $budget['tax_income'];
			$budget['power_corruption'] = $running_income * (($account['power_structure'] * 10) / 100);
			$running_income = $running_income - $budget['power_corruption'];
			$budget['size_corruption'] = $running_income * (floor($account['supplies']['tiles']['amount'] / TILES_PER_CORRUPTION_PERCENT) / 100 );
			$running_income = $running_income - $budget['size_corruption'];
			$budget['federal'] = $this->get_cost_of_industry_by_account($account['id'], FEDERAL_INDUSTRY_KEY, FEDERAL_CASH_COST);
			$running_income = $running_income - $budget['federal'];
			$budget['bases'] = $this->get_cost_of_industry_by_account($account['id'], BASE_INDUSTRY_KEY, BASE_CASH_COST);
			$running_income = $running_income - $budget['bases'];
			$budget['education'] = $this->get_cost_of_industry_by_account($account['id'], EDUCATION_INDUSTRY_KEY, EDUCATION_CASH_COST);
			$running_income = $running_income - $budget['education'];
			$budget['pharmaceuticals'] = $this->get_cost_of_industry_by_account($account['id'], HEALTHCARE_INDUSTRY_KEY, PHARMACEUTICALS_CASH_COST);
			$running_income = $running_income - $budget['pharmaceuticals'];
			$budget['socialism'] = $running_income;
			$budget['earnings'] = $running_income;
			return $budget;
		}
		function gdp_bonus($account) {
			$gdp_bonus = 0;
			if ($account['supplies']['port']['amount'] > 0) {
				$gdp_bonus += PORT_BONUS;
			}
			if ($account['supplies']['machinery']['amount'] > 0) {
				$gdp_bonus += MACHINERY_BONUS;
			}
			if ($account['supplies']['automotive']['amount'] > 0) {
				$gdp_bonus += AUTOMOTIVE_BONUS;
			}
			if ($account['supplies']['aerospace']['amount'] > 0) {
				$gdp_bonus += AEROSPACE_BONUS;
			}
			if ($account['supplies']['entertainment']['amount'] > 0) {
				$gdp_bonus += ENTERTAINMENT_BONUS;
			}
			if ($account['supplies']['financial']['amount'] > 0) {
				$gdp_bonus += FINANCIAL_BONUS;
			}
			return $gdp_bonus;
		}
		function settlement_gdp($account_key) {
			$query = $this->db->query("
				SELECT SUM(settlement_tile_join.settlement_gdp) AS sum_settlement_gdp
				FROM supply_account_lookup AS sal
				INNER JOIN (
					SELECT COUNT(tile.id) * settlement.gdp AS settlement_gdp, account_key
					FROM tile
					INNER JOIN settlement ON tile.settlement_key = settlement.id
					GROUP BY account_key, settlement_key
				) AS settlement_tile_join ON settlement_tile_join.account_key = sal.account_key
				WHERE sal.supply_key = 1
				AND sal.account_key = $account_key
				GROUP BY sal.account_key
			");
			$result = $query->result_array();
			$result = isset($result[0]) ? $result[0] : false;
			return (int)$result['sum_settlement_gdp'];
		}
		function industry_gdp($account_key) {
			$query = $this->db->query("
				SELECT SUM(industry_tile_join.industry_gdp) AS sum_industry_gdp
				FROM supply_account_lookup AS sal
				INNER JOIN (
					SELECT COUNT(tile.id) * industry.gdp AS industry_gdp, account_key
					FROM tile
					INNER JOIN industry ON tile.industry_key = industry.id
					GROUP BY account_key, industry_key
				) AS industry_tile_join ON industry_tile_join.account_key = sal.account_key
				WHERE sal.supply_key = 1
				AND sal.account_key = $account_key
				GROUP BY sal.account_key
			");
			$result = $query->result_array();
			$result = isset($result[0]) ? $result[0] : false;
			return (int)$result['sum_industry_gdp'];
		}
		function get_cost_of_industry_by_account($account_key, $industry_key, $industry_cash_cost) {
			$query = $this->db->query("
				SELECT COUNT(id) AS tile_count
				FROM tile
				WHERE account_key = $account_key
				AND industry_key = $industry_key
				LIMIT 1
			");
			$result = $query->result_array();
			$result = isset($result[0]) ? $result[0] : false;
			return $result['tile_count'] * $industry_cash_cost;
		}
		// 
		// 
		// 
		function merge_settlement_and_supply($settlements, $supplies) {
			foreach ($settlements as $key => $settlement) {
				$settlements[$key]['output'] = '';
				foreach ($supplies as $supply) {
					if ($settlement['output_supply_key'] === $supply['id']) {
						$supply['amount'] = $settlement['output_supply_amount'];
						$settlements[$key]['output'] = $supply;
					}
				}
			}
			return $settlements;
		}
		function merge_industry_and_supply($industries, $supplies) {
			$supply_industry_lookup = $this->get_all('supply_industry_lookup');
			foreach ($industries as $key => $industry) {
				$industries[$key]['inputs'] = [];
				$industries[$key]['output'] = '';
				foreach ($supplies as $supply) {
					if ($industry['output_supply_key'] === $supply['id']) {
						$supply['amount'] = $industry['output_supply_amount'];
						$industries[$key]['output'] = $supply;
					}
				}
				foreach ($supply_industry_lookup as $lookup) {
					if ($industry['id'] === $lookup['industry_key']) {
						foreach ($supplies as $supply) {
							if ($supply['id'] === $lookup['supply_key']) {
								$supply['amount'] = $lookup['amount'];
								$industries[$key]['inputs'][] = $supply;
							}
						}
					}
				}
			}
			return $industries;
		}
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