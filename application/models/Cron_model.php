<?php
defined('BASEPATH')
 OR exit('No direct script access allowed');

Class cron_model extends CI_Model
{
	function cycle_system_message()
	{
		$worlds = $this->game_model->get_all('world');
		foreach ($worlds as $world) {
			$world_key = $world['id'];
	        $declare_war_message = 'Cycle Update';
	        $this->chat_model->new_chat(0, 0, '', ALERT_COLOR, $declare_war_message, $world_key);
	    }
	}
	function mark_active_accounts_as_active()
	{
		$support_key = SUPPORT_KEY;
		$cash_key = CASH_KEY;
		$this->db->query("
			UPDATE account
			LEFT JOIN tile ON tile.account_key = account.id
			LEFT JOIN supply_account_lookup AS support ON tile.account_key = support.account_key AND support.supply_key = $support_key
			SET is_active = IF (tile.id IS NOT NULL, 1, 0)
		");
	}
	function punish_negative_money()
	{
		$support_key = SUPPORT_KEY;
		$cash_key = CASH_KEY;
		$this->db->query("
			UPDATE supply_account_lookup as support
			INNER JOIN supply_account_lookup AS cash ON support.account_key = cash.account_key AND cash.supply_key = $cash_key
			SET support.amount = 0
			WHERE support.supply_key = $support_key
			AND cash.amount < 0
		");
	}
	function zero_negative_account_supply()
	{
		$this->db->query("
			UPDATE supply_account_lookup
			SET amount = 0
			WHERE amount < 0;
		");
	}
	function increase_support()
	{
		$cash_key = CASH_KEY;
		$support_key = SUPPORT_KEY;
		$democracy_key = DEMOCRACY_KEY;
		$oligarchy_key = OLIGARCHY_KEY;
		$autocracy_key = AUTOCRACY_KEY;
		$free_market_key = FREE_MARKET_KEY;
		$socialism_key = SOCIALISM_KEY;
		$democracy_support_regen = DEMOCRACY_SUPPORT_REGEN;
		$oligarchy_support_regen = OLIGARCHY_SUPPORT_REGEN;
		$autocracy_support_regen = AUTOCRACY_SUPPORT_REGEN;
		$democracy_socialism_max_support = DEMOCRACY_SOCIALISM_MAX_SUPPORT;
		$oligarchy_socialism_max_support = OLIGARCHY_SOCIALISM_MAX_SUPPORT;
		$autocracy_socialism_max_support = AUTOCRACY_SOCIALISM_MAX_SUPPORT;
		// Free Market Democracy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			INNER JOIN supply_account_lookup AS cash ON support.account_key = cash.account_key AND cash.supply_key = $cash_key
			SET support.amount = support.amount + $democracy_support_regen
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND cash.amount >= 0
			AND power_structure = $democracy_key
			AND ideology = $free_market_key
			AND support.amount < 100 - tax_rate
		");
		// Free Market Oligarchy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			INNER JOIN supply_account_lookup AS cash ON support.account_key = cash.account_key AND cash.supply_key = $cash_key
			SET support.amount = support.amount + $oligarchy_support_regen
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND cash.amount >= 0
			AND power_structure = $oligarchy_key
			AND ideology = $free_market_key
			AND support.amount < 100 - tax_rate
		");
		// Free Market Autocracy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			INNER JOIN supply_account_lookup AS cash ON support.account_key = cash.account_key AND cash.supply_key = $cash_key
			SET support.amount = support.amount + $autocracy_support_regen
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND cash.amount >= 0
			AND power_structure = $autocracy_key
			AND ideology = $free_market_key
			AND support.amount < 100 - tax_rate
		");
		// Socialism Democracy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			INNER JOIN supply_account_lookup AS cash ON support.account_key = cash.account_key AND cash.supply_key = $cash_key
			SET support.amount = support.amount + $democracy_support_regen
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND cash.amount >= 0
			AND power_structure = $democracy_key
			AND ideology = $socialism_key
			AND support.amount < $democracy_socialism_max_support
		");
		// Socialism Oligarchy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			INNER JOIN supply_account_lookup AS cash ON support.account_key = cash.account_key AND cash.supply_key = $cash_key
			SET support.amount = support.amount + $oligarchy_support_regen
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND cash.amount >= 0
			AND power_structure = $oligarchy_key
			AND ideology = $socialism_key
			AND support.amount < $oligarchy_socialism_max_support
		");
		// Socialism Autocracy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			INNER JOIN supply_account_lookup AS cash ON support.account_key = cash.account_key AND cash.supply_key = $cash_key
			SET support.amount = support.amount + $autocracy_support_regen
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND cash.amount >= 0
			AND power_structure = $autocracy_key
			AND ideology = $socialism_key
			AND support.amount < $autocracy_socialism_max_support
		");
	}
	function enforce_max_support()
	{
		$support_key = SUPPORT_KEY;
		$democracy_key = DEMOCRACY_KEY;
		$oligarchy_key = OLIGARCHY_KEY;
		$autocracy_key = AUTOCRACY_KEY;
		$free_market_key = FREE_MARKET_KEY;
		$socialism_key = SOCIALISM_KEY;
		$democracy_socialism_max_support = DEMOCRACY_SOCIALISM_MAX_SUPPORT;
		$oligarchy_socialism_max_support = OLIGARCHY_SOCIALISM_MAX_SUPPORT;
		$autocracy_socialism_max_support = AUTOCRACY_SOCIALISM_MAX_SUPPORT;
		// Free Market
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			SET support.amount = 100 - tax_rate
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND ideology = $free_market_key
			AND support.amount > 100 - tax_rate
		");
		// Socialism Democracy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			SET support.amount = $democracy_socialism_max_support
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND power_structure = $democracy_key
			AND ideology = $socialism_key
			AND support.amount > $democracy_socialism_max_support
		");
		// Socialism Oligarchy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			SET support.amount = $oligarchy_socialism_max_support
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND power_structure = $oligarchy_key
			AND ideology = $socialism_key
			AND support.amount > $oligarchy_socialism_max_support
		");
		// Socialism Autocracy
		$this->db->query("
			UPDATE supply_account_lookup AS support
			INNER JOIN account ON account_key = account.id
			SET support.amount = $autocracy_socialism_max_support
			WHERE support.supply_key = $support_key
			AND is_active = 1
			AND power_structure = $autocracy_key
			AND ideology = $socialism_key
			AND support.amount > $autocracy_socialism_max_support
		");
	}
	function mark_accounts_as_active()
	{
		// If account tiles > 0, set is_active to true, else set is_active to false
	}
	function grow_population()
	{
		// If all increments are the same, these 3 queries could be merged
		$town_population_increment = TOWN_POPULATION_INCREMENT;
		$city_population_increment = CITY_POPULATION_INCREMENT;
		$metro_population_increment = METRO_POPULATION_INCREMENT;
		$town_key = TOWN_KEY;
		$city_key = CITY_KEY;
		$metro_key = METRO_KEY;
		$grain_key = GRAIN_KEY;
		$fruit_key = FRUIT_KEY;
		$vegetables_key = VEGETABLES_KEY;
		$livestock_key = LIVESTOCK_KEY;
		$fish_key = FISH_KEY;
		$this->db->query("
			UPDATE tile
			LEFT JOIN supply_account_lookup AS grain ON tile.account_key = grain.account_key AND grain.amount > 0 AND grain.supply_key = $grain_key
			LEFT JOIN supply_account_lookup AS fruit ON tile.account_key = fruit.account_key AND fruit.amount > 0 AND fruit.supply_key = $fruit_key
			LEFT JOIN supply_account_lookup AS vegetables ON tile.account_key = vegetables.account_key AND vegetables.amount > 0 AND vegetables.supply_key = $vegetables_key
			LEFT JOIN supply_account_lookup AS livestock ON tile.account_key = livestock.account_key AND livestock.amount > 0 AND livestock.supply_key = $livestock_key
			LEFT JOIN supply_account_lookup AS fish ON tile.account_key = fish.account_key AND fish.amount > 0 AND fish.supply_key = $fish_key
			SET population = population + ($town_population_increment *
				(
					IF(grain.id, 1, 0) +
					IF(fruit.id, 1, 0) +
					IF(vegetables.id, 1, 0) +
					IF(livestock.id, 1, 0) +
					IF(fish.id, 1, 0)
				)
			)
			WHERE settlement_key = $town_key
			AND population <= (SELECT base_population FROM settlement WHERE id = $city_key)
		");
		$this->db->query("
			UPDATE tile
			LEFT JOIN supply_account_lookup AS grain ON tile.account_key = grain.account_key AND grain.amount > 0 AND grain.supply_key = $grain_key
			LEFT JOIN supply_account_lookup AS fruit ON tile.account_key = fruit.account_key AND fruit.amount > 0 AND fruit.supply_key = $fruit_key
			LEFT JOIN supply_account_lookup AS vegetables ON tile.account_key = vegetables.account_key AND vegetables.amount > 0 AND vegetables.supply_key = $vegetables_key
			LEFT JOIN supply_account_lookup AS livestock ON tile.account_key = livestock.account_key AND livestock.amount > 0 AND livestock.supply_key = $livestock_key
			LEFT JOIN supply_account_lookup AS fish ON tile.account_key = fish.account_key AND fish.amount > 0 AND fish.supply_key = $fish_key
			SET population = population + ($city_population_increment *
				(
					IF(grain.id, 1, 0) +
					IF(fruit.id, 1, 0) +
					IF(vegetables.id, 1, 0) +
					IF(livestock.id, 1, 0) +
					IF(fish.id, 1, 0)
				)
			)
			WHERE settlement_key = $city_key
			AND population >= (SELECT base_population FROM settlement WHERE id = $city_key)
			AND population <= (SELECT base_population FROM settlement WHERE id = $metro_key)
		");
		$this->db->query("
			UPDATE tile
			LEFT JOIN supply_account_lookup AS grain ON tile.account_key = grain.account_key AND grain.amount > 0 AND grain.supply_key = $grain_key
			LEFT JOIN supply_account_lookup AS fruit ON tile.account_key = fruit.account_key AND fruit.amount > 0 AND fruit.supply_key = $fruit_key
			LEFT JOIN supply_account_lookup AS vegetables ON tile.account_key = vegetables.account_key AND vegetables.amount > 0 AND vegetables.supply_key = $vegetables_key
			LEFT JOIN supply_account_lookup AS livestock ON tile.account_key = livestock.account_key AND livestock.amount > 0 AND livestock.supply_key = $livestock_key
			LEFT JOIN supply_account_lookup AS fish ON tile.account_key = fish.account_key AND fish.amount > 0 AND fish.supply_key = $fish_key
			SET population = population + ($metro_population_increment *
				(
					IF(grain.id, 1, 0) +
					IF(fruit.id, 1, 0) +
					IF(vegetables.id, 1, 0) +
					IF(livestock.id, 1, 0) +
					IF(fish.id, 1, 0)
				)
			)
			WHERE settlement_key = $metro_key
			AND population >= (SELECT base_population FROM settlement WHERE id = $metro_key)
		");
	}
	function shrink_population()
	{
		$town_population_increment = TOWN_POPULATION_INCREMENT;
		$city_population_increment = CITY_POPULATION_INCREMENT;
		$metro_population_increment = METRO_POPULATION_INCREMENT;
		$shrink_population_multiplier = SHRINK_POPULATION_MULTIPLIER;
		$uninhabited_key = UNINHABITED_KEY;
		$town_key = TOWN_KEY;
		$city_key = CITY_KEY;
		$metro_key = METRO_KEY;
		$grain_key = GRAIN_KEY;
		$fruit_key = FRUIT_KEY;
		$vegetables_key = VEGETABLES_KEY;
		$livestock_key = LIVESTOCK_KEY;
		$fish_key = FISH_KEY;
		$coffee_key = COFFEE_KEY;
		$tea_key = TEA_KEY;
		$cannabis_key = CANNABIS_KEY;
		$wine_key = WINE_KEY;
		$tobacco_key = TOBACCO_KEY;
		$energy_key = ENERGY_KEY;
		$merchandise_key = MERCHANDISE_KEY;
		$steel_key = STEEL_KEY;
		$pharmaceuticals_key = PHARMACEUTICALS_KEY;

		$this->db->query("
			UPDATE tile
			LEFT JOIN supply_account_lookup AS grain ON tile.account_key = grain.account_key AND grain.supply_key = $grain_key AND grain.amount < 0
			LEFT JOIN supply_account_lookup AS fruit ON tile.account_key = fruit.account_key AND fruit.supply_key = $fruit_key AND fruit.amount < 0
			LEFT JOIN supply_account_lookup AS vegetables ON tile.account_key = vegetables.account_key AND vegetables.supply_key = $vegetables_key AND vegetables.amount < 0
			LEFT JOIN supply_account_lookup AS livestock ON tile.account_key = livestock.account_key AND livestock.supply_key = $livestock_key AND livestock.amount < 0
			LEFT JOIN supply_account_lookup AS fish ON tile.account_key = fish.account_key AND fish.supply_key = $fish_key AND fish.amount < 0
			LEFT JOIN supply_account_lookup AS energy ON tile.account_key = energy.account_key AND energy.supply_key = $energy_key AND energy.amount < 0
			SET population = population - If(population > ($town_population_increment * $shrink_population_multiplier), ($town_population_increment * $shrink_population_multiplier), population)
			WHERE settlement_key = $town_key
			AND is_capitol = 0
			AND (
				energy.amount IS NOT NULL
				OR grain.amount IS NOT NULL
				OR fruit.amount IS NOT NULL
				OR vegetables.amount IS NOT NULL
				OR livestock.amount IS NOT NULL
				OR fish.amount IS NOT NULL
			)
		");
		$this->db->query("
			UPDATE tile
			LEFT JOIN supply_account_lookup AS grain ON tile.account_key = grain.account_key AND grain.supply_key = $grain_key AND grain.amount < 0
			LEFT JOIN supply_account_lookup AS fruit ON tile.account_key = fruit.account_key AND fruit.supply_key = $fruit_key AND fruit.amount < 0
			LEFT JOIN supply_account_lookup AS vegetables ON tile.account_key = vegetables.account_key AND vegetables.supply_key = $vegetables_key AND vegetables.amount < 0
			LEFT JOIN supply_account_lookup AS livestock ON tile.account_key = livestock.account_key AND livestock.supply_key = $livestock_key AND livestock.amount < 0
			LEFT JOIN supply_account_lookup AS fish ON tile.account_key = fish.account_key AND fish.supply_key = $fish_key AND fish.amount < 0
			LEFT JOIN supply_account_lookup AS coffee ON tile.account_key = coffee.account_key AND coffee.supply_key = $coffee_key AND coffee.amount < 0
			LEFT JOIN supply_account_lookup AS tea ON tile.account_key = tea.account_key AND tea.supply_key = $tea_key AND tea.amount < 0
			LEFT JOIN supply_account_lookup AS cannabis ON tile.account_key = cannabis.account_key AND cannabis.supply_key = $cannabis_key AND cannabis.amount < 0
			LEFT JOIN supply_account_lookup AS wine ON tile.account_key = wine.account_key AND wine.supply_key = $wine_key AND wine.amount < 0
			LEFT JOIN supply_account_lookup AS tobacco ON tile.account_key = tobacco.account_key AND tobacco.supply_key = $tobacco_key AND tobacco.amount < 0
			LEFT JOIN supply_account_lookup AS energy ON tile.account_key = energy.account_key AND energy.supply_key = $energy_key AND energy.amount < 0
			LEFT JOIN supply_account_lookup AS merchandise ON tile.account_key = merchandise.account_key AND merchandise.supply_key = $merchandise_key AND merchandise.amount < 0
			SET population = population - $city_population_increment - ($city_population_increment * $shrink_population_multiplier)
			WHERE settlement_key = $city_key
			AND (
				energy.amount IS NOT NULL
				OR grain.amount IS NOT NULL
				OR fruit.amount IS NOT NULL
				OR vegetables.amount IS NOT NULL
				OR livestock.amount IS NOT NULL
				OR fish.amount IS NOT NULL
				OR coffee.amount IS NOT NULL
				OR tea.amount IS NOT NULL
				OR cannabis.amount IS NOT NULL
				OR wine.amount IS NOT NULL
				OR tobacco.amount IS NOT NULL
				OR merchandise.amount IS NOT NULL
			)
		");
		$this->db->query("
			UPDATE tile
			LEFT JOIN supply_account_lookup AS grain ON tile.account_key = grain.account_key AND grain.supply_key = $grain_key AND grain.amount < 0
			LEFT JOIN supply_account_lookup AS fruit ON tile.account_key = fruit.account_key AND fruit.supply_key = $fruit_key AND fruit.amount < 0
			LEFT JOIN supply_account_lookup AS vegetables ON tile.account_key = vegetables.account_key AND vegetables.supply_key = $vegetables_key AND vegetables.amount < 0
			LEFT JOIN supply_account_lookup AS livestock ON tile.account_key = livestock.account_key AND livestock.supply_key = $livestock_key AND livestock.amount < 0
			LEFT JOIN supply_account_lookup AS fish ON tile.account_key = fish.account_key AND fish.supply_key = $fish_key AND fish.amount < 0
			LEFT JOIN supply_account_lookup AS coffee ON tile.account_key = coffee.account_key AND coffee.supply_key = $coffee_key AND coffee.amount < 0
			LEFT JOIN supply_account_lookup AS tea ON tile.account_key = tea.account_key AND tea.supply_key = $tea_key AND tea.amount < 0
			LEFT JOIN supply_account_lookup AS cannabis ON tile.account_key = cannabis.account_key AND cannabis.supply_key = $cannabis_key AND cannabis.amount < 0
			LEFT JOIN supply_account_lookup AS wine ON tile.account_key = wine.account_key AND wine.supply_key = $wine_key AND wine.amount < 0
			LEFT JOIN supply_account_lookup AS tobacco ON tile.account_key = tobacco.account_key AND tobacco.supply_key = $tobacco_key AND tobacco.amount < 0
			LEFT JOIN supply_account_lookup AS energy ON tile.account_key = energy.account_key AND energy.supply_key = $energy_key AND energy.amount < 0
			LEFT JOIN supply_account_lookup AS merchandise ON tile.account_key = merchandise.account_key AND merchandise.supply_key = $merchandise_key AND merchandise.amount < 0
			LEFT JOIN supply_account_lookup AS steel ON tile.account_key = steel.account_key AND steel.supply_key = $steel_key AND steel.amount < 0
			LEFT JOIN supply_account_lookup AS pharmaceuticals ON tile.account_key = pharmaceuticals.account_key AND pharmaceuticals.supply_key = $pharmaceuticals_key AND pharmaceuticals.amount < 0
			SET population = population - $metro_population_increment - ($metro_population_increment * $shrink_population_multiplier)
			WHERE settlement_key = $metro_key
			AND (
				energy.amount IS NOT NULL
				OR grain.amount IS NOT NULL
				OR fruit.amount IS NOT NULL
				OR vegetables.amount IS NOT NULL
				OR livestock.amount IS NOT NULL
				OR fish.amount IS NOT NULL
				OR coffee.amount IS NOT NULL
				OR tea.amount IS NOT NULL
				OR cannabis.amount IS NOT NULL
				OR wine.amount IS NOT NULL
				OR tobacco.amount IS NOT NULL
				OR merchandise.amount IS NOT NULL
				OR steel.amount IS NOT NULL
				OR pharmaceuticals.amount IS NOT NULL
			)
		");
		// Keep towns at their base
		$this->db->query("
			UPDATE tile
			INNER JOIN settlement ON settlement.id = settlement_key
			SET population = settlement.base_population
			WHERE settlement_key = $town_key
			AND population < settlement.base_population
		");
	}
	function downgrade_townships()
	{
		$town_key = TOWN_KEY;
		$city_key = CITY_KEY;
		$metro_key = METRO_KEY;
		$this->db->query("
			UPDATE tile
			INNER JOIN settlement ON settlement.id = settlement_key
			SET
				settlement_key = $town_key,
				industry_key = NULL
			WHERE settlement_key = $city_key
			AND population < settlement.base_population
		");
		$this->db->query("
			UPDATE tile
			INNER JOIN settlement ON settlement.id = settlement_key
			SET
				settlement_key = $city_key,
				industry_key = NULL
			WHERE settlement_key = $metro_key
			AND population < settlement.base_population
		");
	}
	function luxury_bonus()
	{
		$support_key = SUPPORT_KEY;
		$coffee_key = COFFEE_KEY;
		$tea_key = TEA_KEY;
		$cannabis_key = CANNABIS_KEY;
		$wine_key = WINE_KEY;
		$tobacco_key = TOBACCO_KEY;
		$this->db->query("
			UPDATE supply_account_lookup AS osal
			LEFT JOIN supply_account_lookup AS coffee ON osal.account_key = coffee.account_key AND coffee.amount > 0 AND coffee.supply_key = $coffee_key
			LEFT JOIN supply_account_lookup AS tea ON osal.account_key = tea.account_key AND tea.amount > 0 AND tea.supply_key = $tea_key
			LEFT JOIN supply_account_lookup AS cannabis ON osal.account_key = cannabis.account_key AND cannabis.amount > 0 AND cannabis.supply_key = $cannabis_key
			LEFT JOIN supply_account_lookup AS wine ON osal.account_key = wine.account_key AND wine.amount > 0 AND wine.supply_key = $wine_key
			LEFT JOIN supply_account_lookup AS tobacco ON osal.account_key = tobacco.account_key AND tobacco.amount > 0 AND tobacco.supply_key = $tobacco_key
			SET osal.amount = osal.amount +
			(
				IF(coffee.id, 1, 0) +
				IF(tea.id, 1, 0) +
				IF(cannabis.id, 1, 0) +
				IF(wine.id, 1, 0) +
				IF(tobacco.id, 1, 0)
			)
			WHERE osal.supply_key = $support_key
		");
	}
	function census_population()
	{
		$population_key = POPULATION_KEY;
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			SET sal.amount = (SELECT SUM(population) FROM tile WHERE tile.account_key = sal.account_key)
			WHERE sal.supply_key = $population_key
		");
	}
	function resource_output()
	{
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			INNER JOIN resource AS resource_by_supply ON sal.supply_key = resource_by_supply.output_supply_key
			INNER JOIN (
				SELECT resource_key, account_key, COUNT(*) as tile_count
				FROM tile
				GROUP BY resource_key, account_key
			) AS tile_by_resource_and_account ON resource_by_supply.id = tile_by_resource_and_account.resource_key AND sal.account_key = tile_by_resource_and_account.account_key
			SET sal.amount = sal.amount + (IFNULL(tile_by_resource_and_account.tile_count, 0) * resource_by_supply.output_supply_amount)
		");
	}
	function settlement_output()
	{
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT SUM(tile_count * settlement.output_supply_amount) AS new_amount, account_key, output_supply_key
				FROM settlement
				INNER JOIN (
					SELECT COUNT(*) as tile_count, settlement_key, account_key
					FROM tile
					GROUP BY settlement_key, account_key
				) AS tile_by_settlement_and_account ON settlement.id = tile_by_settlement_and_account.settlement_key
				GROUP BY settlement.output_supply_key, tile_by_settlement_and_account.account_key
			) AS settlement ON settlement.output_supply_key = sal.supply_key AND sal.account_key = settlement.account_key
			INNER JOIN account ON sal.account_key = account.id
			SET sal.amount = sal.amount + new_amount
		");
	}
	function township_input()
	{
		$accounts = $this->get_all_accounts_for_townships();
		foreach ($accounts as $account) {
			// Calculate new grouped supply values
			$food_needed = $this->get_account_food_needed($account);
			$cash_crops_needed = $this->get_account_cash_crops_needed($account);
			$food_randomized = [
				'fish' => 0,
				'livestock' => 0,
				'fruit' => 0,
				'vegetables' => 0,
				'grain' => 0,
			];
			$cash_crops_randomized = [
				'coffee' => 0,
				'tea' => 0,
				'tobacco' => 0,
				'wine' => 0,
				'cannabis' => 0,
			];
			// Shuffle with perserved keys
			shuffle_assoc($food_randomized);
			shuffle_assoc($cash_crops_randomized);
			// update_grouped_supply receives reference
			$this->update_grouped_supply($account, $food_randomized, $food_needed);
			$this->update_grouped_supply($account, $cash_crops_randomized, $cash_crops_needed);
			// Calculate new direct supply values
			$energy = $merchandise = $steel = $pharmaceuticals = 0;
			$energy = ($account['town_count'] * TOWN_ENERGY_COST) + ($account['city_count'] * CITY_ENERGY_COST) + ($account['metro_count'] * METRO_ENERGY_COST);
			$merchandise = ($account['town_count'] * TOWN_MERCHANDISE_COST) + ($account['city_count'] * CITY_MERCHANDISE_COST) + ($account['metro_count'] * METRO_MERCHANDISE_COST);
			$steel = ($account['town_count'] * TOWN_STEEL_COST) + ($account['city_count'] * CITY_STEEL_COST) + ($account['metro_count'] * METRO_STEEL_COST);
			$pharmaceuticals = ($account['town_count'] * TOWN_PHARMACEUTICALS_COST) + ($account['city_count'] * CITY_PHARMACEUTICALS_COST) + ($account['metro_count'] * METRO_PHARMACEUTICALS_COST);
			// Apply new values
			$data = $this->township_input_update_array($account, $food_randomized, $food_needed, $cash_crops_randomized, $cash_crops_needed, $energy, $merchandise, $steel, $pharmaceuticals);
			// Run update
			$this->db->where('account_key', $account['id']);
			$this->db->update_batch('supply_account_lookup', $data, 'supply_key');
		}
	}
	function update_grouped_supply($account_supplies, &$new_supplies, &$supply_needed)
	{
		foreach ($new_supplies as $key => $value) {
			if ($account_supplies[$key] >= $supply_needed) {
				$new_supplies[$key] = $supply_needed;
				$supply_needed = 0;
			}
			else {
				$new_supplies[$key] = $account_supplies[$key];
				$supply_needed = $supply_needed - $account_supplies[$key];
			}
		}
	}
	function township_input_update_array($account, $food_randomized, $food_needed, $cash_crops_randomized, $cash_crops_needed, $energy, $merchandise, $steel, $pharmaceuticals)
	{
		return [
			[
				'account_key' => $account['id'],
				'supply_key' => GRAIN_KEY,
				'amount' => $account['grain'] - $food_randomized['grain'] - $food_needed,
			],
			[
				'account_key' => $account['id'],
				'supply_key' => FRUIT_KEY,
				'amount' => $account['fruit'] - $food_randomized['fruit'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => VEGETABLES_KEY,
				'amount' => $account['vegetables'] - $food_randomized['vegetables'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => LIVESTOCK_KEY,
				'amount' => $account['livestock'] - $food_randomized['livestock'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => FISH_KEY,
				'amount' => $account['fish'] - $food_randomized['fish'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => COFFEE_KEY,
				'amount' => $account['coffee'] - $cash_crops_randomized['coffee'] - $cash_crops_needed,
			],
			[
				'account_key' => $account['id'],
				'supply_key' => TEA_KEY,
				'amount' => $account['tea'] - $cash_crops_randomized['tea'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => CANNABIS_KEY,
				'amount' => $account['cannabis'] - $cash_crops_randomized['cannabis'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => WINE_KEY,
				'amount' => $account['wine'] - $cash_crops_randomized['wine'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => TOBACCO_KEY,
				'amount' => $account['tobacco'] - $cash_crops_randomized['tobacco'],
			],
			[
				'account_key' => $account['id'],
				'supply_key' => ENERGY_KEY,
				'amount' => $account['energy'] - $energy,
			],
			[
				'account_key' => $account['id'],
				'supply_key' => MERCHANDISE_KEY,
				'amount' => $account['merchandise'] - $merchandise,
			],
			[
				'account_key' => $account['id'],
				'supply_key' => STEEL_KEY,
				'amount' => $account['steel'] - $steel,
			],
			[
				'account_key' => $account['id'],
				'supply_key' => PHARMACEUTICALS_KEY,
				'amount' => $account['pharmaceuticals'] - $pharmaceuticals,
			],
		];
	}
	function get_account_food_needed($account)
	{
		$food_needed = 0;
		$food_needed += $account['town_count'] * TOWN_FOOD_COST;
		$food_needed += $account['city_count'] * CITY_FOOD_COST;
		$food_needed += $account['metro_count'] * METRO_FOOD_COST;
		return $food_needed;
	}
	function get_account_cash_crops_needed($account)
	{
		$cash_crops_needed = 0;
		$cash_crops_needed += $account['town_count'] * TOWN_CASH_CROPS_COST;
		$cash_crops_needed += $account['city_count'] * CITY_CASH_CROPS_COST;
		$cash_crops_needed += $account['metro_count'] * METRO_CASH_CROPS_COST;
		return $cash_crops_needed;
	}
	function get_all_accounts_for_townships()
	{
		$this->db->select("
			*,
			(SELECT COUNT(tile.id) FROM tile WHERE account_key = account.id AND settlement_key = " . TOWN_KEY . ") AS town_count,
			(SELECT COUNT(tile.id) FROM tile WHERE account_key = account.id AND settlement_key = " . CITY_KEY . ") AS city_count,
			(SELECT COUNT(tile.id) FROM tile WHERE account_key = account.id AND settlement_key = " . METRO_KEY . ") AS metro_count,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . GRAIN_KEY . ") AS grain,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . FRUIT_KEY . ") AS fruit,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . VEGETABLES_KEY . ") AS vegetables,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . LIVESTOCK_KEY . ") AS livestock,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . FISH_KEY . ") AS fish,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . COFFEE_KEY . ") coffee,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . TEA_KEY . ") AS tea,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . CANNABIS_KEY . ") AS cannabis,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . WINE_KEY . ") AS wine,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . TOBACCO_KEY . ") AS tobacco,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . ENERGY_KEY . ") AS energy,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . MERCHANDISE_KEY . ") AS merchandise,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . STEEL_KEY . ") AS steel,
			(SELECT amount FROM supply_account_lookup WHERE account_key = account.id AND supply_key = " . PHARMACEUTICALS_KEY . ") AS pharmaceuticals,
		");
		$this->db->from('account');
		$this->db->where('account.is_active', true);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	function industry_input()
	{
		$support_key = SUPPORT_KEY;
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT SUM(tile_count * supply_industry_lookup.amount) as new_amount, account_key, supply_key
				FROM supply_industry_lookup
				INNER JOIN (
					SELECT COUNT(*) as tile_count, industry_key, account_key
					FROM tile
					GROUP BY industry_key, account_key
				) AS tile_by_industry_and_account ON supply_industry_lookup.industry_key = tile_by_industry_and_account.industry_key
				GROUP BY supply_industry_lookup.supply_key, tile_by_industry_and_account.account_key
			) AS sil ON sil.supply_key = sal.supply_key AND sal.account_key = sil.account_key
			INNER JOIN account ON sal.account_key = account.id
			INNER JOIN (
				SELECT account_key, amount
				FROM supply_account_lookup
				WHERE supply_key = $support_key
				AND amount >= 0
			) AS support ON support.account_key = account.id
			SET sal.amount = sal.amount - new_amount
			WHERE support.amount IS NOT NULL
		");
	}
	// This pattern can be switched to settlement_income_collect style if ever slow
	function industry_output()
	{
		$support_key = SUPPORT_KEY;
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			-- Get update data
			INNER JOIN (
				SELECT SUM(tile_count * industry.output_supply_amount) AS new_amount, account_key, output_supply_key, industry.id
				FROM industry
				INNER JOIN (
					SELECT COUNT(*) as tile_count, industry_key, account_key
					FROM tile
					GROUP BY industry_key, account_key
				) AS tile_by_industry_and_account ON industry.id = tile_by_industry_and_account.industry_key
				GROUP BY industry.output_supply_key, tile_by_industry_and_account.account_key
			) AS industry ON industry.output_supply_key = sal.supply_key AND sal.account_key = industry.account_key
			-- Ensure support exists
			INNER JOIN account ON sal.account_key = account.id
			INNER JOIN (
				SELECT account_key, amount
				FROM supply_account_lookup
				WHERE supply_key = $support_key
				AND amount >= 0
			) AS support ON support.account_key = account.id
			-- Set
			SET sal.amount = sal.amount + new_amount
			WHERE support.amount IS NOT NULL
			-- Ensure supply exists
			AND NOT EXISTS (
				SELECT industry_key, account_key
				FROM supply_industry_lookup
				INNER JOIN (
					SELECT supply_key, account_key, id, amount
					FROM supply_account_lookup
					WHERE amount < 0
				) AS sal_by_sil ON supply_industry_lookup.supply_key = sal_by_sil.supply_key
				WHERE sal_by_sil.amount < 0
				AND sal_by_sil.account_key = account.id
				AND supply_industry_lookup.industry_key = industry.id
			)
		");
	}
	function settlement_income_collect()
	{
		$support_key = SUPPORT_KEY;
		$tiles_per_corruption_percent = TILES_PER_CORRUPTION_PERCENT;
		$free_market_key = FREE_MARKET_KEY;
		$cash_key = CASH_KEY;
		$port_key = PORT_KEY;
		$machinery_key = MACHINERY_KEY;
		$automotive_key = AUTOMOTIVE_KEY;
		$aerospace_key = AEROSPACE_KEY;
		$entertainment_key = ENTERTAINMENT_KEY;
		$financial_key = FINANCIAL_KEY;
		$port_bonus = PORT_BONUS;
		$machinery_bonus = MACHINERY_BONUS;
		$automotive_bonus = AUTOMOTIVE_BONUS;
		$aerospace_bonus = AEROSPACE_BONUS;
		$entertainment_bonus = ENTERTAINMENT_BONUS;
		$financial_bonus = FINANCIAL_BONUS;
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT sal.account_key, sal.amount, (SUM(settlement_tile_join.settlement_gdp) * IFNULL(sum_gdp_bonus,1) ) AS sum_settlement_gdp
				FROM supply_account_lookup AS sal
				-- GDP Bonus
				INNER JOIN (
					SELECT supply_account_lookup.id, supply_account_lookup.account_key,
					(
						1 +
						(IF(port.amount, $port_bonus, 0) / 100) +
						(IF(machinery.amount, $machinery_bonus, 0) / 100) +
						(IF(automotive.amount, $automotive_bonus, 0) / 100) +
						(IF(aerospace.amount, $aerospace_bonus, 0) / 100) +
						(IF(entertainment.amount, $entertainment_bonus, 0) / 100) +
						(IF(financial.amount, $financial_bonus, 0) / 100)
					) AS sum_gdp_bonus
					FROM supply_account_lookup
					LEFT JOIN supply_account_lookup AS port ON port.account_key = supply_account_lookup.supply_key AND port.supply_key = $port_key && port.amount > 0
					LEFT JOIN supply_account_lookup AS machinery ON machinery.account_key = supply_account_lookup.supply_key AND machinery.supply_key = $machinery_key && machinery.amount > 0
					LEFT JOIN supply_account_lookup AS automotive ON automotive.account_key = supply_account_lookup.supply_key AND automotive.supply_key = $automotive_key && automotive.amount > 0
					LEFT JOIN supply_account_lookup AS aerospace ON aerospace.account_key = supply_account_lookup.supply_key AND aerospace.supply_key = $aerospace_key && aerospace.amount > 0
					LEFT JOIN supply_account_lookup AS entertainment ON entertainment.account_key = supply_account_lookup.supply_key AND entertainment.supply_key = $entertainment_key && entertainment.amount > 0
					LEFT JOIN supply_account_lookup AS financial ON financial.account_key = supply_account_lookup.supply_key AND financial.supply_key = $financial_key && financial.amount > 0
				) AS sal_gdp_bonus ON sal_gdp_bonus.id = sal.id
				INNER JOIN (
					SELECT COUNT(tile.id) * settlement.gdp AS settlement_gdp, account_key
					FROM tile
					INNER JOIN settlement ON tile.settlement_key = settlement.id
					GROUP BY account_key, settlement_key
				) AS settlement_tile_join ON settlement_tile_join.account_key = sal.account_key
				WHERE sal.supply_key = 1
				GROUP BY sal.account_key
			) as gdp ON sal.account_key = gdp.account_key
			INNER JOIN account ON sal.account_key = account.id
			INNER JOIN world ON account.world_key = world.id
			INNER JOIN (
				SELECT account_key, COUNT(tile.id) AS tile_count
				FROM tile
				GROUP BY account_key
			) AS all_tile ON all_tile.account_key = sal.account_key

			-- amount equals current amount plus grp times tax rate * power structure corruption rate * the tile corruption rate
			-- Tile corruption is every N tiles another 1 percent of corruption
			SET sal.amount = sal.amount + (
				sum_settlement_gdp *
				( account.tax_rate / 100 ) *
				( ( 100 - ( account.power_structure * 10 ) ) / 100 ) *
				( ( 100 - ( FLOOR(all_tile.tile_count / $tiles_per_corruption_percent) ) ) / 100 )
			)

			WHERE account.is_active = 1
			AND world.deleted = 0
			AND account.ideology = $free_market_key
			AND sal.supply_key = $cash_key
		");
	}
	function industry_income_collect()
	{
		$tiles_per_corruption_percent = TILES_PER_CORRUPTION_PERCENT;
		$free_market_key = FREE_MARKET_KEY;
		$cash_key = CASH_KEY;
		$port_key = PORT_KEY;
		$machinery_key = MACHINERY_KEY;
		$automotive_key = AUTOMOTIVE_KEY;
		$aerospace_key = AEROSPACE_KEY;
		$entertainment_key = ENTERTAINMENT_KEY;
		$financial_key = FINANCIAL_KEY;
		$port_bonus = PORT_BONUS;
		$machinery_bonus = MACHINERY_BONUS;
		$automotive_bonus = AUTOMOTIVE_BONUS;
		$aerospace_bonus = AEROSPACE_BONUS;
		$entertainment_bonus = ENTERTAINMENT_BONUS;
		$financial_bonus = FINANCIAL_BONUS;
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT sal.account_key, sal.amount, (SUM(industry_tile_join.industry_gdp) * IFNULL(sum_gdp_bonus,1) ) AS sum_industry_gdp
				FROM supply_account_lookup AS sal
				-- GDP Bonus
				INNER JOIN (
					SELECT supply_account_lookup.id, supply_account_lookup.account_key,
					(
						1 +
						(IF(port.amount, $port_bonus, 0) / 100) +
						(IF(machinery.amount, $machinery_bonus, 0) / 100) +
						(IF(automotive.amount, $automotive_bonus, 0) / 100) +
						(IF(aerospace.amount, $aerospace_bonus, 0) / 100) +
						(IF(entertainment.amount, $entertainment_bonus, 0) / 100) +
						(IF(financial.amount, $financial_bonus, 0) / 100)
					) AS sum_gdp_bonus
					FROM supply_account_lookup
					LEFT JOIN supply_account_lookup AS port ON port.account_key = supply_account_lookup.supply_key AND port.supply_key = $port_key && port.amount > 0
					LEFT JOIN supply_account_lookup AS machinery ON machinery.account_key = supply_account_lookup.supply_key AND machinery.supply_key = $machinery_key && machinery.amount > 0
					LEFT JOIN supply_account_lookup AS automotive ON automotive.account_key = supply_account_lookup.supply_key AND automotive.supply_key = $automotive_key && automotive.amount > 0
					LEFT JOIN supply_account_lookup AS aerospace ON aerospace.account_key = supply_account_lookup.supply_key AND aerospace.supply_key = $aerospace_key && aerospace.amount > 0
					LEFT JOIN supply_account_lookup AS entertainment ON entertainment.account_key = supply_account_lookup.supply_key AND entertainment.supply_key = $entertainment_key && entertainment.amount > 0
					LEFT JOIN supply_account_lookup AS financial ON financial.account_key = supply_account_lookup.supply_key AND financial.supply_key = $financial_key && financial.amount > 0
				) AS sal_gdp_bonus ON sal_gdp_bonus.id = sal.id
				-- GDP of tiles grouped by industry
				INNER JOIN (
					SELECT COUNT(tile.id) * industry.gdp AS industry_gdp, account_key
					FROM tile
					INNER JOIN industry ON tile.industry_key = industry.id
					-- Ensure supply exists
					WHERE NOT EXISTS (
						SELECT industry_key, account_key
						FROM supply_industry_lookup
						INNER JOIN (
							SELECT supply_key, account_key, id, amount
							FROM supply_account_lookup
							WHERE amount < 0
						) AS sal_by_sil ON supply_industry_lookup.supply_key = sal_by_sil.supply_key
						WHERE sal_by_sil.amount < 0
						AND sal_by_sil.account_key = tile.account_key
						AND supply_industry_lookup.industry_key = tile.industry_key
					)
					GROUP BY account_key, industry_key
				) AS industry_tile_join ON industry_tile_join.account_key = sal.account_key
				WHERE sal.supply_key = 1
				GROUP BY sal.account_key
			) as gdp ON sal.account_key = gdp.account_key
			INNER JOIN account ON sal.account_key = account.id
			INNER JOIN (
				SELECT account_key, COUNT(tile.id) AS tile_count
				FROM tile
				GROUP BY account_key
			) AS all_tile ON all_tile.account_key = sal.account_key

			-- amount equals current amount plus grp times tax rate * power structure corruption rate * the tile corruption rate
			-- Tile corruption is every N tiles another 1 percent of corruption
			SET sal.amount = sal.amount + (
				sum_industry_gdp *
				( account.tax_rate / 100 ) *
				( ( 100 - ( account.power_structure * 10 ) ) / 100 ) *
				( ( 100 - ( FLOOR(all_tile.tile_count / $tiles_per_corruption_percent) ) ) / 100 )
			)
			WHERE account.is_active = 1
			AND account.ideology = $free_market_key
			AND sal.supply_key = $cash_key
		");
	}
	function consume_gdp_bonus_supplies()
	{
		// Minimum of 1 so industry has input to use before outputting
		$this->db->query("
			UPDATE supply_account_lookup AS sal
			INNER JOIN supply ON supply.id = sal.supply_key
			SET amount = amount - 1
			WHERE amount > 0
			AND supply.gdp_bonus IS NOT NULL
		");
	}
	function world_resets()
	{
		$force_reset_world_id = isset($_GET['world_id']) ? $_GET['world_id'] : false;
		$now = date('Y-m-d H:i:s');
		$worlds = $this->game_model->get_all_won_worlds();
		foreach ($worlds as $world) {
			// Check if it's time to run
			if ($force_reset_world_id) {
				if ($force_reset_world_id != $world['id']) {
					continue;
				}
			}
			echo 'Resetting world ' . $world['id'] . ' - ' . $world['slug'] . ' - ';
			$this->world_reset_row_deletes($world['id']);
			$this->world_reset_row_updates($world['id']);
			$this->world_model->world_reset_regenerate_resources($world['id']);
			$this->world_reset_world_state($world['id']);
		}
	}
	function world_reset_row_deletes($world_key)
	{
		$this->db->query("
			DELETE trade_request
			FROM trade_request
			LEFT JOIN account
				ON trade_request.request_account_key = account.id
			WHERE world_key = account.world_key
		");

		$this->db->query("
			DELETE trade_request
			FROM trade_request
			LEFT JOIN account
				ON trade_request.receive_account_key = account.id
			WHERE world_key = account.world_key
		");

		$this->db->query("
			DELETE treaty_lookup
			FROM treaty_lookup
			LEFT JOIN account
				ON treaty_lookup.a_account_key = account.id
			WHERE world_key = account.world_key
		");

		$this->db->query("
			DELETE treaty_lookup
			FROM treaty_lookup
			LEFT JOIN account
				ON treaty_lookup.b_account_key = account.id
			WHERE world_key = account.world_key
		");

		$this->db->query("
			DELETE supply_account_trade_lookup
			FROM supply_account_trade_lookup
			LEFT JOIN account
				ON supply_account_trade_lookup.account_key = account.id
			WHERE world_key = account.world_key
		");
	}
	function world_reset_row_updates($world_key)
	{
		$cash_key = CASH_KEY;
		$support_key = SUPPORT_KEY;
		$grain_key = GRAIN_KEY;
		$energy_key = ENERGY_KEY;
		$default_cash = DEFAULT_CASH;
		$default_support = DEFAULT_SUPPORT;
		$default_grain = DEFAULT_GRAIN;
		$default_energy = DEFAULT_ENERGY;
		$default_power_structure = DEFAULT_POWER_STRUCTURE;
		$default_tax_rate = DEFAULT_TAX_RATE;
		$default_ideology = DEFAULT_IDEOLOGY;

		$this->db->query("
			UPDATE `world`
			SET
			`winner_account_key` = NULL,
			`winner_industry_key` = NULL
			WHERE id = $world_key;
		");

		$this->db->query("
			UPDATE `tile`
			SET
			`account_key` = NULL,
			`resource_key` = NULL,
			`settlement_key` = NULL,
			`industry_key` = NULL,
			`unit_key` = NULL,
			`unit_owner_key` = NULL,
			`unit_owner_color` = NULL,
			`is_capitol` = 0,
			`is_base` = 0,
			`population` = NULL,
			`tile_name` = NULL,
			`tile_desc` = NULL,
			`color` = NULL
			WHERE world_key = $world_key;
		");

		$this->db->query("
			UPDATE `account`
			SET
			`power_structure` = $default_power_structure,
			`tax_rate` = $default_tax_rate,
			`ideology` = $default_ideology,
			`last_law_change` = NULL
			WHERE world_key = $world_key;
		");

		$this->db->query("
			UPDATE `supply_account_lookup`
			LEFT JOIN `account`
				ON `account`.id = `supply_account_lookup`.account_key
			SET
			`amount` = 0
			WHERE world_key = $world_key;
		");

		$this->db->query("
			UPDATE `supply_account_lookup`
			LEFT JOIN `account`
				ON `account`.id = `supply_account_lookup`.account_key
			SET
			`amount` = $default_cash
			WHERE world_key = $world_key
			AND supply_key = $cash_key;
		");

		$this->db->query("
			UPDATE `supply_account_lookup`
			LEFT JOIN `account`
				ON `account`.id = `supply_account_lookup`.account_key
			SET
			`amount` = $default_support
			WHERE world_key = $world_key
			AND supply_key = $support_key;
		");

		$this->db->query("
			UPDATE `supply_account_lookup`
			LEFT JOIN `account`
				ON `account`.id = `supply_account_lookup`.account_key
			SET
			`amount` = $default_grain
			WHERE world_key = $world_key
			AND supply_key = $grain_key;
		");

		$this->db->query("
			UPDATE `supply_account_lookup`
			LEFT JOIN `account`
				ON `account`.id = `supply_account_lookup`.account_key
			SET
			`amount` = $default_energy
			WHERE world_key = $world_key
			AND supply_key = $energy_key;
		");
	}
	function world_reset_world_state($world_key)
	{
		$data = array(
			'winner_account_key' => null,
			'winner_industry_key' => null,
		);
		$this->db->where('id', $world_key);
		$this->db->update('world', $data);
	}
	function market_prices_debug()
	{
		$market_prices = $this->game_model->get_all('market_price');
		foreach ($market_prices as $market_price) {
			$rounds = 24 * 15;
			echo '<hr>' . $market_price['supply_key'] . '<hr>';
			for ($i = 0; $i < $rounds; $i++) {
				$market_price['amount'] = $this->generate_new_market_price($market_price);
				echo $market_price['amount'] . '<br>';
			}
		}
	}
	function generate_new_market_price($market_price)
	{
		// To get gradual gains in the long term, either set percent_chance_of_increase to slightly more than 50, or set max_increase slighter higher than max_decrease.
		// Leaving it even leads to the price occasionally hitting bottom, which might be ideal depending on your use case.
		// Setting max_increase and max_decrease gives a very volatile market.
		$price = (int)$market_price['amount'];
		$percent_chance_of_increase = (int)$market_price['percent_chance_of_increase'];
		$max_increase = (int)$market_price['max_increase'];
		$max_decrease = (int)$market_price['max_decrease'];
		$min_increase = (int)$market_price['min_increase'];
		$min_decrease = (int)$market_price['min_decrease'];
		$min_price = (int)$market_price['min_price'];
		$max_price = (int)$market_price['max_price'];

		if ($percent_chance_of_increase > mt_rand(0,100)) {
			$price = $price + mt_rand($min_increase, $max_increase);
		}
		else {
			$price = $price - mt_rand($min_decrease, $max_decrease);
		}
		if ($price < $min_price) {
			$price = $min_price;
		}
		return $price;
	}
	function update_market_prices()
	{
		$market_prices = $this->game_model->get_all('market_price');
		foreach ($market_prices as $market_price) {
			$new_price = $this->generate_new_market_price($market_price);
			$data = array(
				'amount' => $new_price,
			);
			$this->db->where('id', $market_price['id']);
			$this->db->update('market_price', $data);
		}
	}
	function reject_expired_trade_requests()
	{
		$expired_trade_requests = $this->get_expired_trade_requests();
		foreach ($expired_trade_requests as $trade_request) {
			if ($trade_request['treaty_key'] != WAR_KEY) {
				$this->game_model->reject_trade_request($trade_request['id'], $trade_request['request_account_key'], TRADE_EXPIRED_MESSAGE);
			}
		}
	}
	function get_expired_trade_requests() {
		$this->db->select('*');
		$this->db->from('trade_request');
		$this->db->where('is_accepted', false);
		$this->db->where('is_rejected', false);
		$this->db->where('trade_request.created <', date('Y-m-d H:i:s', time() - (TRADE_EXPIRE_HOURS * 60 * 60) ));
		$query = $this->db->get();
		return $query->result_array();
	}

}