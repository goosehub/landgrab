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
	function get_single($table, $id)
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where('id', $id);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
	}
	function get_supply_by_account($account_key, $supply_key)
	{
		$this->db->select('*');
		$this->db->from('supply_account_lookup');
		$this->db->where('account_key', $account_key);
		$this->db->where('supply_key', $supply_key);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
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
	function supply_is_cash_crop($supply_key)
	{
		$cash_crops = [
			COFFEE_KEY,
			TEA_KEY,
			CANNABIS_KEY,
			WINE_KEY,
			TOBACCO_KEY,
		];
		return in_array($supply_key, $cash_crops);
	}
	function tile_select()
	{
		$resource_string = "IF(account_key, resource_key, NULL) AS resource_key,";
		if (DEBUG_SHOW_RESOURCES) {
			$resource_string = "resource_key,";
		}
		return "
		id,
		lat,
		lng,
		world_key,
		account_key,
		terrain_key,
		$resource_string
		settlement_key,
		industry_key,
		unit_key,
		unit_owner_key,
		unit_owner_color,
		is_capitol,
		is_base,
		population,
		tile_name,
		tile_desc,
		color,
		modified
		";
	}
	function get_all_tiles_in_world($world_key)
	{
		$this->db->select($this->tile_select(), FALSE);
		$this->db->from('tile');
		$this->db->where('world_key', $world_key);
		$query = $this->db->get();
		return $query->result_array();
	}
	function get_all_tiles_in_world_recently_updated($world_key, $update_timespan)
	{
		$this->db->select($this->tile_select(), FALSE);
		$this->db->from('tile');
		$this->db->where('world_key', $world_key);
		$this->db->where('modified >', date('Y-m-d H:i:s', time() - $update_timespan));
		$query = $this->db->get();
		return $query->result_array();
	}
	function get_all_tiles_in_world_with_units($world_key)
	{
		$this->db->select($this->tile_select(), FALSE);
		$this->db->from('tile');
		$this->db->where('world_key', $world_key);
		$this->db->where('unit_key IS NOT NULL', NULL, FALSE);
		$query = $this->db->get();
		return $query->result_array();
	}
	function get_tile($lat, $lng, $world_key)
	{
		$this->db->select($this->tile_select(), FALSE);
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
		$this->db->select($this->tile_select(), FALSE);
		$this->db->from('tile');
		$this->db->where('id', $tile_id);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
	}
	function get_capitol_tile_by_account($account_key)
	{
		$this->db->select($this->tile_select(), FALSE);
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
	function update_tile_timestamp($previous_tile)
	{
		$data = array(
			'modified' => date('Y-m-d H:i:s'),
		);
		$this->db->where('id', $previous_tile['id']);
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
	function update_tile_settlement($tile_id, $settlement_key, $tile_name)
	{
		// dd($tile_name);
		$data = array(
			'settlement_key' => $settlement_key,
			'tile_name' => $tile_name,
		);
		if ($settlement_key == TOWN_KEY) {
			$data['population'] = $this->settlements[$settlement_key - 1]['base_population'];
		}
		if (!$this->tile_is_township($settlement_key)) {
			$data['industry_key'] = NULL;
			$data['population'] = $this->settlements[$settlement_key - 1]['base_population'];
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
		$this->db->select('COUNT(tile.id) * output_supply_amount AS output_supply_amount, output_supply_key');
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
		if ($account['supplies']['aircraft']['amount'] > 0) {
			$gdp_bonus += AEROSPACE_BONUS;
		}
		if ($account['supplies']['culture']['amount'] > 0) {
			$gdp_bonus += ENTERTAINMENT_BONUS;
		}
		if ($account['supplies']['influence']['amount'] > 0) {
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
	function get_trade_request($trade_request_key) {
		$this->db->select('trade_request.*, request_user.username AS request_username, receive_user.username AS receive_username');
		$this->db->from('trade_request');
		$this->db->join('account AS request_account', 'trade_request.request_account_key = request_account.id', 'left');
		$this->db->join('user AS request_user', 'request_user.id = request_account.user_key', 'left');
		$this->db->join('account AS receive_account', 'trade_request.receive_account_key = receive_account.id', 'left');
		$this->db->join('user AS receive_user', 'receive_user.id = receive_account.user_key', 'left');
		$this->db->where('trade_request.id', $trade_request_key);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
	}
	function mark_trade_request_request_seen($trade_request_key) {
		$this->db->set('request_seen', true);
		$this->db->where('id', $trade_request_key);
		$this->db->update('trade_request');
	}
	function mark_trade_request_response_seen($trade_request_key) {
		$this->db->set('response_seen', true);
		$this->db->where('id', $trade_request_key);
		$this->db->update('trade_request');
	}
	function get_supply_account_trade_lookup($trade_request_key, $account_key) {
		$this->db->select('*');
		$this->db->from('supply_account_trade_lookup');
		$this->db->where('trade_key', $trade_request_key);
		$this->db->where('account_key', $account_key);
		$query = $this->db->get();
		return $query->result_array();
	}
	function sent_trades($account_key) {
		$this->db->select('trade_request.*, user.username');
		$this->db->from('trade_request');
		$this->db->join('account', 'trade_request.receive_account_key = account.id', 'left');
		$this->db->join('user', 'user.id = account.user_key', 'left');
		$this->db->where('request_account_key', $account_key);
		$this->db->where('trade_request.created >', date('Y-m-d H:i:s', time() - (TRADE_SHOW_HOURS * 60 * 60) ));
		$this->db->order_by('trade_request.created', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}
	function received_trades($account_key) {
		$this->db->select('trade_request.*, user.username');
		$this->db->from('trade_request');
		$this->db->join('account', 'trade_request.request_account_key = account.id', 'left');
		$this->db->join('user', 'user.id = account.user_key', 'left');
		$this->db->where('receive_account_key', $account_key);
		$this->db->where('trade_request.created >', date('Y-m-d H:i:s', time() - (TRADE_SHOW_HOURS * 60 * 60) ));
		$this->db->order_by('trade_request.created', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}
	function create_trade_main($request_account_key, $receive_account_key, $message, $treaty_key, $supplies_offered, $supplies_demanded) {
		$trade_key = $this->create_trade_request($request_account_key, $receive_account_key, $message, $treaty_key);
		foreach ($supplies_offered as $supply) {
			$this->create_supply_account_trade_lookups($supply->supply_key, $request_account_key, $trade_key, (int)$supply->amount);
			// We hold the money pal
			$this->decrement_account_supply($request_account_key, $supply->supply_key, (int)$supply->amount);
		}
		foreach ($supplies_demanded as $supply) {
			$this->create_supply_account_trade_lookups($supply->supply_key, $receive_account_key, $trade_key, (int)$supply->amount);
		}
		return $trade_key;
	}
	function create_trade_request($request_account_key, $receive_account_key, $message, $treaty_key) {
		$data = array(
			'request_account_key' => $request_account_key,
			'receive_account_key' => $receive_account_key,
			'request_message' => $message,
			'is_declared' => $treaty_key == WAR_KEY,
			'treaty_key' => $treaty_key,
		);
		$this->db->insert('trade_request', $data);
		return $this->db->insert_id();
	}
	function create_supply_account_trade_lookups($supply_key, $account_key, $trade_key, $amount) {
		$data = array(
			'supply_key' => $supply_key,
			'account_key' => $account_key,
			'trade_key' => $trade_key,
			'amount' => $amount,
		);
		$this->db->insert('supply_account_trade_lookup', $data);
	}
	function open_trade_request_between_accounts_exist($account_key, $trade_partner_key) {
		$this->db->select('*');
		$this->db->from('trade_request');
		$this->db->where('request_account_key', $account_key);
		$this->db->where('receive_account_key', $trade_partner_key);
		$this->db->where('is_accepted', 0);
		$this->db->where('is_rejected', 0);
		$this->db->where('is_declared', 0);
		$query = $this->db->get();
		return $query->result_array();
	}
	function sufficient_supplies_to_send_trade_request($supplies_offered, $account_key) {
		foreach ($supplies_offered as $supply) {
			if (!$this->account_has_amount_of_supply($account_key, $supply->supply_key, $supply->amount)) {
				return false;
			}
		}
		return true;
	}
	function sufficient_supplies_to_accept_trade_request($trade_request_key, $account_key) {
		$data['needed_supplies'] = $this->get_supply_account_trade_lookup($trade_request_key, $account_key);
		foreach ($data['needed_supplies'] as $supply) {
			if (!$this->account_has_amount_of_supply($account_key, $supply['supply_key'], $supply['amount'])) {
				return false;
			}
		}
		return true;
	}
	function pay_upfront_food_cost($account, $settlement_key) {
		$food_needed = 0;
		if ($settlement_key == TOWN_KEY) {
			$food_needed = TOWN_FOOD_COST;
		}
		if ($settlement_key == CITY_KEY) {
			$food_needed = CITY_FOOD_COST;
		}
		if ($settlement_key == METRO_KEY) {
			$food_needed = METRO_FOOD_COST;
		}
		$food_randomized = [
			'fish' => 0,
			'livestock' => 0,
			'fruit' => 0,
			'vegetables' => 0,
			'grain' => 0,
		];
		shuffle_assoc($food_randomized);
		$this->update_grouped_supply_for_account($account['supplies'], $food_randomized, $food_needed);
		$data = $this->township_upfront_food_update_array($account, $food_randomized, $food_needed);
		// Run update
		$this->db->where('account_key', $account['id']);
		$this->db->update_batch('supply_account_lookup', $data, 'supply_key');
	}
	function township_upfront_food_update_array($account, $food_randomized, $food_needed)
	{
		return [
			[
				'account_key' => $account['id'],
				'supply_key' => GRAIN_KEY,
				'amount' => $account['supplies']['grain']['amount'] - $food_randomized['grain'] - $food_needed,
			],
			[
				'account_key' => $account['id'],
				'supply_key' => FRUIT_KEY,
				'amount' => $account['supplies']['fruit']['amount'] - $food_randomized['fruit'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => VEGETABLES_KEY,
				'amount' => $account['supplies']['vegetables']['amount'] - $food_randomized['vegetables'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => LIVESTOCK_KEY,
				'amount' => $account['supplies']['livestock']['amount'] - $food_randomized['livestock'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => FISH_KEY,
				'amount' => $account['supplies']['fish']['amount'] - $food_randomized['fish'],
			],
		];
	}
	function update_grouped_supply_for_account($account_supplies, &$new_supplies, &$supply_needed)
	{
		foreach ($new_supplies as $key => $value) {
			if ((int)$account_supplies[$key]['amount'] >= $supply_needed) {
				$new_supplies[$key] = $supply_needed;
				$supply_needed = 0;
			}
			else {
				$new_supplies[$key] = (int)$account_supplies[$key]['amount'];
				$supply_needed = $supply_needed - (int)$account_supplies[$key]['amount'];
			}
		}
	}
	function account_has_amount_of_supply($account_key, $supply_key, $amount) {
		$this->db->select('*');
		$this->db->from('supply_account_lookup');
		$this->db->where('account_key', $account_key);
		$this->db->where('supply_key', $supply_key);
		$this->db->where('amount >=', $amount);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? true : false;
	}
	function accept_trade_request($trade_request_key, $receive_account_key, $request_account_key, $response_message) {
		$data = array(
			'is_accepted' => true,
			'response_message' => $response_message,
		);
		$this->db->where('id', $trade_request_key);
		$this->db->update('trade_request', $data);
		$this->pay_on_accept_trade_request($trade_request_key, $receive_account_key);
		$this->receiver_receive_on_accept_trade_request($trade_request_key, $receive_account_key, $request_account_key);
		$this->requester_receive_on_accept_trade_request($trade_request_key, $request_account_key, $receive_account_key);
	}
	function pay_on_accept_trade_request($trade_request_key, $receive_account_key) {
		$supplies_demanded = $this->get_supply_account_trade_lookup($trade_request_key, $receive_account_key);
		foreach ($supplies_demanded as $supply) {
			$this->decrement_account_supply($receive_account_key, $supply['supply_key'], (int)$supply['amount']);
		}
	}
	function receiver_receive_on_accept_trade_request($trade_request_key, $receive_account_key, $request_account_key) {
		$supplies_offered = $this->get_supply_account_trade_lookup($trade_request_key, $request_account_key);
		foreach ($supplies_offered as $supply) {
			$this->increment_account_supply($receive_account_key, $supply['supply_key'], (int)$supply['amount']);
		}
	}
	function requester_receive_on_accept_trade_request($trade_request_key, $receive_account_key, $request_account_key) {
		$supplies_offered = $this->get_supply_account_trade_lookup($trade_request_key, $request_account_key);
		foreach ($supplies_offered as $supply) {
			$this->increment_account_supply($receive_account_key, $supply['supply_key'], (int)$supply['amount']);
		}
	}
	function reject_trade_request($trade_request_key, $request_account_key, $response_message) {
		$data = array(
			'is_rejected' => true,
			'request_seen' => true,
			'response_message' => $response_message,
		);
		$this->db->where('id', $trade_request_key);
		$this->db->update('trade_request', $data);
		$this->refund_on_reject_trade_request($trade_request_key, $request_account_key);
	}
	function refund_on_reject_trade_request($trade_request_key, $request_account_key) {
		$supplies_offered = $this->get_supply_account_trade_lookup($trade_request_key, $request_account_key);
		foreach ($supplies_offered as $supply) {
			$this->increment_account_supply($request_account_key, $supply['supply_key'], (int)$supply['amount']);
		}
	}
	function find_existing_treaty($account_key, $trade_partner_key) {
		$account_key = (int)$account_key;
		$trade_partner_key = (int)$trade_partner_key;
		$this->db->select('*');
		$this->db->from('treaty_lookup');
		$this->db->where('
			(a_account_key = ' . $account_key . ' AND b_account_key = ' . $trade_partner_key . ')
			OR
			(b_account_key = ' . $account_key . ' AND a_account_key = ' . $trade_partner_key . ')
		', NULL, FALSE);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
	}
	function update_treaty($id, $treaty_key) {
		$this->db->set('treaty_key', $treaty_key);
		$this->db->where('id', $id);
		$this->db->update('treaty_lookup');
	}
	function create_treaty($account_a, $account_b, $treaty_key) {
		$data = array(
			'a_account_key' => $account_a,
			'b_account_key' => $account_b,
			'treaty_key' => $treaty_key,
		);
		$this->db->insert('treaty_lookup', $data);
	}
	function treaties_by_account($account_key) {
		$this->db->select('treaty_lookup.*, a_user.username AS a_username, b_user.username AS b_username');
		$this->db->from('treaty_lookup');
		$this->db->join('account AS a_account', 'treaty_lookup.a_account_key = a_account.id', 'left');
		$this->db->join('user AS a_user', 'a_user.id = a_account.user_key', 'left');
		$this->db->join('account AS b_account', 'treaty_lookup.b_account_key = b_account.id', 'left');
		$this->db->join('user AS b_user', 'b_user.id = b_account.user_key', 'left');
		$this->db->where('a_account_key', $account_key);
		$this->db->or_where('b_account_key', $account_key);
		$query = $this->db->get();
		return $query->result_array();
	}
	function player_has_supplies_for_industry($account, $industry, $supplies) {
		$supply_industry_lookup = $this->get_all('supply_industry_lookup');
		$industry = $this->merge_single_industry_and_supplies($industry, $supplies, $supply_industry_lookup);
		foreach ($industry['inputs'] as $input) {
			if ($account['supplies'][$input['slug']]['amount'] < $input['amount']) {
				api_error_response('not_enough_to_win', 'This victory requires ' . $input['amount'] . ' ' . $input['label']);
				return false;
			}
		}
		return true;
	}
	function win_the_game($account, $industry_key) {
		$data = array(
			'winner_account_key' => $account['id'],
			'winner_industry_key' => $industry_key,
		);
		$this->db->where('id', $account['world_key']);
		$this->db->update('world', $data);
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
			$industries[$key] = $this->merge_single_industry_and_supplies($industries[$key], $supplies, $supply_industry_lookup);
		}
		return $industries;
	}
	function merge_single_industry_and_supplies($industry, $supplies, $supply_industry_lookup) {
		$industry['inputs'] = [];
		$industry['output'] = '';
		foreach ($supplies as $supply) {
			if ($industry['output_supply_key'] === $supply['id']) {
				$supply['amount'] = $industry['output_supply_amount'];
				$industry['output'] = $supply;
			}
		}
		foreach ($supply_industry_lookup as $lookup) {
			if ($industry['id'] === $lookup['industry_key']) {
				foreach ($supplies as $supply) {
					if ($supply['id'] === $lookup['supply_key']) {
						$supply['amount'] = $lookup['amount'];
						$industry['inputs'][] = $supply;
					}
				}
			}
		}
		return $industry;
	}
	function settlement_allowed_on_terrain($terrain_key, $settlement) {
		if ($terrain_key == FERTILE_KEY && $settlement['is_allowed_on_fertile']) {
			return true;
		}
		if ($terrain_key == COASTAL_KEY && $settlement['is_allowed_on_coastal']) {
			return true;
		}
		if ($terrain_key == BARREN_KEY && $settlement['is_allowed_on_barren']) {
			return true;
		}
		if ($terrain_key == MOUNTAIN_KEY && $settlement['is_allowed_on_mountain']) {
			return true;
		}
		if ($terrain_key == TUNDRA_KEY && $settlement['is_allowed_on_tundra']) {
			return true;
		}
		return false;
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
		// Hardcoded for tile size 2 worlds
		if ($lng === 182) {
			$lng = -178;
		}
		if ($lng === -180) {
			$lng = 180;
		}
		return $lng;
	}
	function get_industry_from_state($industry_key) {
		foreach ($this->industries as $industry) {
			if ($industry['id'] == $industry_key) {
				return $industry;
			}
		}
	}
	function get_settlement_from_state($settlement_key) {
		foreach ($this->settlements as $settlement) {
			if ($settlement['id'] == $settlement_key) {
				return $settlement;
			}
		}
	}
	function can_claim($account, $tile, $previous_tile) {
		if (!$account) {
			return false;
		}
		if ((int)$tile['terrain_key'] === OCEAN_KEY) {
			return false;
		}
		if ($tile['account_key'] == $account['id']) {
			return false;
		}
		if ($tile['account_key']) {
			$treaty = $this->game_model->find_existing_treaty($account['id'], $tile['account_key']);
			if (!$treaty || $treaty['treaty_key'] != WAR_KEY) {
				api_error_response('attack_requires_war', 'You must declare war before attacking this nation');
			}
		}
		if ($tile['account_key'] != $account['id'] && !$this->unit_can_take_settlement($tile['settlement_key'], $previous_tile['unit_key'])) {
			api_error_response('unit_not_able_to_take_square', 'This unit is not able to take this size of township');
		}
		return true;
	}
	function can_move_to($account, $tile, $previous_tile) {
		if ((int)$tile['terrain_key'] === OCEAN_KEY) {
			return true;
		}

		if ($tile['unit_key']) {
			$treaty = $this->game_model->find_existing_treaty($account['id'], $tile['account_key']);
			if (!$treaty || $treaty['treaty_key'] != WAR_KEY) {
				api_error_response('tile_is_occupied', 'There is already a friendly unit on this tile');
			}
		}
		if ($tile['account_key'] != $account['id'] && !$this->unit_can_take_settlement($tile['settlement_key'], $previous_tile['unit_key'])) {
			api_error_response('unit_not_able_to_take_square', 'This unit is not able to take this size of township');
		}
		return true;
	}
	function unit_can_take_settlement($settlement_key, $unit_key) {
		if ($settlement_key == TOWN_KEY) {
			if ($unit_key == AIRFORCE_KEY) {
				return false;
			}
		}
		if ($settlement_key == CITY_KEY) {
			if ($unit_key == AIRFORCE_KEY) {
				return false;
			}
		}
		if ($settlement_key == METRO_KEY) {
			if ($unit_key == AIRFORCE_KEY) {
				return false;
			}
			if ($unit_key == INFANTRY_KEY) {
				return false;
			}
		}
		return true;
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
	function get_market_price_by_supply_key($supply_key) {
		$this->db->select('market_price.*');
		$this->db->from('supply');
		$this->db->join('market_price', 'market_price.id = supply.market_price_key', 'left');
		$this->db->where('supply.id', $supply_key);
		$query = $this->db->get();
		$result = $query->result_array();
		return isset($result[0]) ? $result[0] : false;
	}
	function sell_supply_remove_supply($account_key, $supply_key) {
		$this->db->set('amount', 'amount - 1', FALSE);
		$this->db->where('supply_key', $supply_key);
		$this->db->where('account_key', $account_key);
		$this->db->update('supply_account_lookup');
	}
	function sell_supply_add_cash($account_key, $market_price_amount) {
		$this->db->set('amount', 'amount + ' . $market_price_amount, FALSE);
		$this->db->where('supply_key', CASH_KEY);
		$this->db->where('account_key', $account_key);
		$this->db->update('supply_account_lookup');
	}
}