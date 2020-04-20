<?php
defined('BASEPATH')
 OR exit('No direct script access allowed');

Class combat_model extends CI_Model
{
	function combat($defender_unit_key, $attacker_unit_key, $defender_terrain_key, $attacker_terrain_key, $settlement_key)
	{
		$attacker_unit_key = $this->navy_translate($attacker_unit_key, $attacker_terrain_key);
		$defender_unit_key = $this->navy_translate($defender_unit_key, $defender_terrain_key);

		$attack_power = $this->calculate_attack_power($defender_unit_key, $attacker_unit_key, $defender_terrain_key);
		$defend_power = $this->calculate_defend_power($defender_unit_key, $attacker_unit_key, $defender_terrain_key, $settlement_key);
		$total_power = $attack_power + $defend_power;

		$combat_result = rand(0, $total_power * COMBAT_ACCURACY) / COMBAT_ACCURACY;

		return array(
			'defender_unit_key' => $defender_unit_key,
			'attacker_unit_key' => $attacker_unit_key,
			'attack_power' => $attack_power,
			'defend_power' => $defend_power,
			'total_power' => $attack_power + $defend_power,
			'combat_result' => (float)$combat_result,
			'victory' => $combat_result > $defend_power,
		);
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