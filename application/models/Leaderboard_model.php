<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class leaderboard_model extends CI_Model
{
	function get_leaders_of_supply($supply_key, $world_key)
	{
		$this->db->select('
			supply_account_lookup.amount,
			user.username,
			supply.label,
			supply.suffix,
			nation_name,
			nation_flag,
			leader_name,
			leader_portrait,
			color
		');
		$this->db->from('supply_account_lookup');
		$this->db->join('supply', ' supply.id = supply_account_lookup.supply_key', 'left');
		$this->db->join('account', ' account.id = supply_account_lookup.account_key', 'left');
		$this->db->join('user', ' user.id = account.user_key', 'left');
		$this->db->where('account.world_key', $world_key);
		$this->db->where('supply_account_lookup.supply_key', $supply_key);
		$this->db->order_by('supply_account_lookup.amount', 'desc');
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
}
?>