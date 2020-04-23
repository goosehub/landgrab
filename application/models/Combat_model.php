<?php
defined('BASEPATH')
 OR exit('No direct script access allowed');

Class combat_model extends CI_Model
{
	function combat($account, $tile, $previous_tile)
	{
		$defender_account_key = (int)$tile['account_key'];
		$defender_unit_key = (int)$tile['unit_key'];
		$defender_terrain_key = (int)$tile['terrain_key'];
		$attacker_terrain_key = (int)$previous_tile['terrain_key'];
		$attacker_unit_key = (int)$previous_tile['unit_key'];
		$settlement_key = (int)$tile['settlement_key'];

		$attacker_unit_key = $this->navy_translate($attacker_unit_key, $attacker_terrain_key);
		$defender_unit_key = $this->navy_translate($defender_unit_key, $defender_terrain_key);
		$defender_unit_key = $this->militia_translate($defender_unit_key, $settlement_key);

		if ($account['id'] == $defender_account_key) {
			return false;
		}
		if (!$defender_unit_key) {
			return false;
		}

		$attack_power = $this->calculate_attack_power($defender_unit_key, $attacker_unit_key, $defender_terrain_key);
		$defend_power = $this->calculate_defend_power($defender_unit_key, $attacker_unit_key, $defender_terrain_key, $settlement_key);
		$total_power = $attack_power + $defend_power;

		$combat_result = $this->get_rekt($total_power);

		return array(
			'defender_unit_key' => $defender_unit_key,
			'attacker_unit_key' => $attacker_unit_key,
			'attack_power' => $attack_power,
			'defend_power' => $defend_power,
			'total_power' => $attack_power + $defend_power,
			'combat_result' => (float)$combat_result,
			'victory' => $combat_result >= $defend_power,
			'matchup_offensive_bonus' => $this->find_matchup_offensive_bonus($defender_unit_key, $attacker_unit_key),
			'terrain_offensive_bonus' => $this->find_terrain_offensive_bonus($defender_terrain_key),
			'matchup_defensive_bonus' => $this->find_matchup_defensive_bonus($defender_unit_key, $attacker_unit_key),
			'terrain_defensive_bonus' => $this->find_terrain_defensive_bonus($defender_terrain_key),
			'township_defensive_bonus' => $this->find_township_defensive_bonus($settlement_key),
		);
	}

	// This one line function determines the outcome of every battle
	// Is it random enough? This SO answer says sure
	// https://stackoverflow.com/a/12729689/3774582
	function get_rekt($total_power)
	{
		return rand(0, $total_power * COMBAT_ACCURACY) / COMBAT_ACCURACY;
	}

	function calculate_attack_power($defender_unit_key, $attacker_unit_key, $defender_terrain_key)
	{
		$attack_power = 1;
		$offensive_bonus = 1;
		$offensive_bonus += $this->find_matchup_offensive_bonus($defender_unit_key, $attacker_unit_key);
		$offensive_bonus += $this->find_terrain_offensive_bonus($defender_terrain_key);
		$attack_power = $attack_power * $offensive_bonus;
		return $attack_power;
	}

	function calculate_defend_power($defender_unit_key, $attacker_unit_key, $defender_terrain_key, $settlement_key)
	{
		$defend_power = 1;
		$defensive_bonus = 1;
		$defensive_bonus += $this->find_matchup_defensive_bonus($defender_unit_key, $attacker_unit_key);
		$defensive_bonus += $this->find_terrain_defensive_bonus($defender_terrain_key);
		$defensive_bonus += $this->find_township_defensive_bonus($settlement_key);
		$defend_power = $defend_power * $defensive_bonus;
		return $defend_power;
	}

	function militia_translate($defender_unit_key, $settlement_key)
	{
		if ($defender_unit_key) {
			return $defender_unit_key;
		}
		if ($this->game_model->tile_is_township($settlement_key)) {
			return MILITIA_KEY;
		}
		return $defender_unit_key;
	}

	function navy_translate($unit_key, $terrain_key)
	{
		if ($terrain_key === OCEAN_KEY) {
			return NAVY_KEY;
		}
		return (int)$unit_key;
	}

	function find_matchup_offensive_bonus($defender_unit_key, $attacker_unit_key)
	{
		if ($defender_unit_key === INFANTRY_KEY && $attacker_unit_key === TANKS_KEY) {
			return 1;
		}
		if ($defender_unit_key === TANKS_KEY && $attacker_unit_key === AIRFORCE_KEY) {
			return 1;
		}
		if ($defender_unit_key === AIRFORCE_KEY && $attacker_unit_key === INFANTRY_KEY) {
			return 1;
		}
		if ($defender_unit_key === NAVY_KEY && $attacker_unit_key === INFANTRY_KEY) {
			return 1;
		}
		if ($defender_unit_key === NAVY_KEY && $attacker_unit_key === TANKS_KEY) {
			return 1;
		}
		if ($defender_unit_key === NAVY_KEY && $attacker_unit_key === AIRFORCE_KEY) {
			return 1;
		}
		if ($defender_unit_key === MILITIA_KEY && $attacker_unit_key === INFANTRY_KEY) {
			return 1;
		}
		if ($defender_unit_key === MILITIA_KEY && $attacker_unit_key === TANKS_KEY) {
			return 1;
		}
		if ($defender_unit_key === MILITIA_KEY && $attacker_unit_key === AIRFORCE_KEY) {
			return 1;
		}
		return 0;
	}

	function find_matchup_defensive_bonus($defender_unit_key, $attacker_unit_key)
	{
		if ($defender_unit_key === INFANTRY_KEY && $attacker_unit_key === AIRFORCE_KEY) {
			return 1;
		}
		if ($defender_unit_key === INFANTRY_KEY && $attacker_unit_key === NAVY_KEY) {
			return 1;
		}
		if ($defender_unit_key === TANKS_KEY && $attacker_unit_key === INFANTRY_KEY) {
			return 1;
		}
		if ($defender_unit_key === TANKS_KEY && $attacker_unit_key === NAVY_KEY) {
			return 1;
		}
		if ($defender_unit_key === AIRFORCE_KEY && $attacker_unit_key === TANKS_KEY) {
			return 1;
		}
		if ($defender_unit_key === AIRFORCE_KEY && $attacker_unit_key === NAVY_KEY) {
			return 1;
		}
		return 0;
	}

	function find_terrain_offensive_bonus($defender_terrain_key)
	{
		if ($defender_terrain_key === BARREN_KEY) {
			return BARREN_OFFENSIVE_BONUS;
		}
		return 0;
	}

	function find_terrain_defensive_bonus($defender_terrain_key)
	{
		if ($defender_terrain_key === TUNDRA_KEY) {
			return TUNDRA_DEFENSIVE_BONUS;
		}
		if ($defender_terrain_key === MOUNTAIN_KEY) {
			return MOUNTAIN_DEFENSIVE_BONUS;
		}
		return 0;
	}

	function find_township_defensive_bonus($settlement_key)
	{
		if ($settlement_key === TOWN_KEY) {
			return TOWN_DEFENSIVE_BONUS;
		}
		if ($settlement_key === CITY_KEY) {
			return CITY_DEFENSIVE_BONUS;
		}
		if ($settlement_key === METRO_KEY) {
			return METRO_DEFENSIVE_BONUS;
		}
		return 0;
	}
}