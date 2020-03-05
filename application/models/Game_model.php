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
	function update_tile_terrain($lng, $lat, $terrain_key)
	{
		$data = array(
			'terrain_key' => $terrain_key,
		);
		$this->db->where('lat', $lat);
		$this->db->where('lng', $lng);
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
		return $settlement_key === TOWN_KEY || $settlement_key === CITY_KEY || $settlement_key === METRO_KEY;
	}
	function first_claim($tile,$account) {
		$data = array(
			'account_key' => $account['id'],
			'settlement_key' => TOWN_KEY,
			'army_unit_key' => INFANTRY_COST,
			'army_unit_owner_key' => $account['id'],
			'is_capitol' => 1,
			'tile_name' => 'Capitol of ' . $account['nation_name'],
			'tile_desc' => '',
		);
		$this->db->where('lat', $tile['lat']);
		$this->db->where('lng', $tile['lng']);
		$this->db->update('tile', $data);
	}
	function increment_account_supply($account_key, $supply_key) {
		$this->db->set('amount', 'amount + 1', FALSE);
		$this->db->where('account_key', $account_key);
		$this->db->where('supply_key', $supply_key);
		$this->db->update('supply_account_lookup');
	}
	function decrement_account_supply($account_key, $supply_key) {
		$this->db->set('amount', 'amount + 1', FALSE);
		$this->db->where('account_key', $account_key);
		$this->db->where('supply_key', $supply_key);
		$this->db->update('supply_account_lookup');
	}
}