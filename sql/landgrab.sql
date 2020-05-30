-- Database sql
DROP TABLE IF EXISTS world;
DROP TABLE IF EXISTS tile;
DROP TABLE IF EXISTS unit_type;
DROP TABLE IF EXISTS account;
DROP TABLE IF EXISTS trade_request;
DROP TABLE IF EXISTS treaty_lookup;
DROP TABLE IF EXISTS supply_account_lookup;
DROP TABLE IF EXISTS supply_account_trade_lookup;
DROP TABLE IF EXISTS supply_industry_lookup;
DROP TABLE IF EXISTS supply;
DROP TABLE IF EXISTS terrain;
DROP TABLE IF EXISTS resource;
DROP TABLE IF EXISTS settlement;
DROP TABLE IF EXISTS industry;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS chat;
DROP TABLE IF EXISTS analytics;
DROP TABLE IF EXISTS ip_request;

DROP TABLE IF EXISTS `world`;
CREATE TABLE IF NOT EXISTS `world` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(256) NOT NULL,
  `tile_size` int(4) NOT NULL,
  `winner_account_key` int(10) UNSIGNED NULL,
  `winner_industry_key` int(10) UNSIGNED NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `world` ADD PRIMARY KEY (`id`);
ALTER TABLE `world` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `tile`;
CREATE TABLE IF NOT EXISTS `tile` (
  `id` int(10) UNSIGNED NOT NULL,
  `lat` int(4) NOT NULL,
  `lng` int(4) NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NULL,
  `terrain_key` int(10) UNSIGNED NOT NULL,
  `resource_key` int(10) UNSIGNED NULL,
  `settlement_key` int(10) UNSIGNED NULL,
  `industry_key` int(10) UNSIGNED NULL,
  `unit_key` int(10) UNSIGNED NULL, -- Infantry, Tanks, Airforce, none as null
  `unit_owner_key` int(10) UNSIGNED NULL,
  `unit_owner_color` varchar(8) NULL,
  `is_capitol` int(1) NOT NULL,
  `is_base` int(1) NOT NULL,
  `population` int(10) UNSIGNED NULL,
  `tile_name` varchar(512) NULL,
  `tile_desc` text NULL,
  `color` varchar(8) NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `tile` ADD PRIMARY KEY (`id`);
ALTER TABLE `tile` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tile` ADD INDEX `world_key` (`world_key`);
ALTER TABLE `tile` ADD INDEX `account_key` (`account_key`);
ALTER TABLE `tile` ADD INDEX `terrain_key` (`terrain_key`);
ALTER TABLE `tile` ADD INDEX `resource_key` (`resource_key`);
ALTER TABLE `tile` ADD INDEX `settlement_key` (`settlement_key`);
ALTER TABLE `tile` ADD INDEX `industry_key` (`industry_key`);
ALTER TABLE `tile` ADD INDEX `unit_key` (`unit_key`);

DROP TABLE IF EXISTS `unit_type`;
CREATE TABLE IF NOT EXISTS `unit_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(126) NOT NULL,
  `strength_against_key` int(10) UNSIGNED NOT NULL,
  `cash_cost` int(4) NOT NULL,
  `support_cost` int(4) NOT NULL,
  `can_take_tiles` int(1) NOT NULL,
  `can_take_towns` int(1) NOT NULL,
  `can_take_cities` int(1) NOT NULL,
  `can_take_metros` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `unit_type` ADD PRIMARY KEY (`id`);
ALTER TABLE `unit_type` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

TRUNCATE TABLE `unit_type`;
INSERT INTO `unit_type` (`id`, `slug`, `strength_against_key`, `cash_cost`, `support_cost`,
  `can_take_tiles`, `can_take_towns`, `can_take_cities`, `can_take_metros`) VALUES
(1, 'Infantry', 3, 20, 10,
  TRUE, TRUE, TRUE, FALSE),
(2, 'Tanks', 1, 30, 20,
  TRUE, TRUE, TRUE, TRUE),
(3, 'Airforce', 2, 40, 40,
  TRUE, FALSE, FALSE, FALSE);

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_key` int(10) UNSIGNED NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `is_active` int(1) UNSIGNED NOT NULL,
  `tutorial` int(10) UNSIGNED NOT NULL,
  -- Custom Info
  `nation_name` varchar(256) NOT NULL,
  `nation_flag` varchar(256) NOT NULL,
  `leader_name` varchar(256) NOT NULL,
  `leader_portrait` varchar(256) NOT NULL,
  `color` varchar(8) NOT NULL,
  -- Government Settings
  `cash_crop_key` int(10) UNSIGNED NULL,
  `power_structure` int(10) UNSIGNED NULL, -- Democracy, Oligarchy, Autocracy, Anarchy
  `tax_rate` int(10) UNSIGNED NOT NULL,
  `ideology` int(10) UNSIGNED NULL, -- Socialism, Free Market
  `last_law_change` timestamp NULL,
  -- meta
  `last_load` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `account` ADD PRIMARY KEY (`id`);
ALTER TABLE `account` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `account` ADD INDEX `user_key` (`user_key`);
ALTER TABLE `account` ADD INDEX `world_key` (`world_key`);

DROP TABLE IF EXISTS `trade_request`;
CREATE TABLE IF NOT EXISTS `trade_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_account_key` int(10) UNSIGNED NOT NULL,
  `receive_account_key` int(10) UNSIGNED NOT NULL,
  `treaty_key` int(10) UNSIGNED NOT NULL, -- War, Peace, Passage
  `request_message` text NOT NULL,
  `response_message` text NOT NULL,
  `request_seen` int(1) UNSIGNED NOT NULL,
  `response_seen` int(1) UNSIGNED NOT NULL,
  `is_accepted` int(1) UNSIGNED NOT NULL,
  `is_rejected` int(1) UNSIGNED NOT NULL,
  `is_declared` int(1) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `trade_request` ADD PRIMARY KEY (`id`);
ALTER TABLE `trade_request` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `trade_request` ADD INDEX `request_account_key` (`request_account_key`);
ALTER TABLE `trade_request` ADD INDEX `receive_account_key` (`receive_account_key`);
ALTER TABLE `trade_request` ADD INDEX `treaty_key` (`treaty_key`);

DROP TABLE IF EXISTS `treaty_lookup`;
CREATE TABLE IF NOT EXISTS `treaty_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `a_account_key` int(10) UNSIGNED NOT NULL,
  `b_account_key` int(10) UNSIGNED NOT NULL,
  `treaty_key` int(10) UNSIGNED NOT NULL, -- War, Peace, Passage
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `treaty_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `treaty_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `treaty_lookup` ADD INDEX `a_account_key` (`a_account_key`);
ALTER TABLE `treaty_lookup` ADD INDEX `b_account_key` (`b_account_key`);
ALTER TABLE `treaty_lookup` ADD INDEX `treaty_key` (`treaty_key`);

DROP TABLE IF EXISTS `supply_industry_lookup`;
CREATE TABLE IF NOT EXISTS `supply_industry_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `industry_key` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_industry_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_industry_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `supply_industry_lookup` ADD INDEX `industry_key` (`industry_key`);
ALTER TABLE `supply_industry_lookup` ADD INDEX `supply_key` (`supply_key`);

TRUNCATE TABLE `supply_industry_lookup`;
INSERT INTO `supply_industry_lookup` (`industry_key`, `supply_key`, `amount`) VALUES
(2, 1, 10), -- Federal
(3, 1, 10), -- Base
(4, 14, 3), -- Biofuel
(5, 15, 1), -- Coal
(6, 16, 1), -- Gas
(7, 17, 1), -- Petroleum
(8, 18, 1), -- Nuclear
(9, 5, 1), -- Manufacturing
(9, 6, 1), -- Manufacturing
(9, 7, 2), -- Manufacturing
(10, 13, 10), -- Chemicals
(11, 28, 1), -- Steel
(11, 13, 1), -- Steel
(12, 29, 1), -- Electronics
(12, 7, 1), -- Electronics
(13, 39, 5), -- Port
(14, 30, 1), -- Machinery
(14, 39, 1), -- Machinery
(14, 40, 1), -- Machinery
(14, 36, 1), -- Machinery
(14, 38, 1), -- Machinery
(14, 34, 1), -- Machinery
(15, 31, 1), -- Automotive
(15, 39, 1), -- Automotive
(15, 40, 1), -- Automotive
(15, 38, 1), -- Automotive
(15, 36, 2), -- Automotive
(15, 17, 2), -- Automotive
(16, 31, 1), -- Aerospace
(16, 32, 1), -- Aerospace
(16, 39, 1), -- Aerospace
(16, 40, 1), -- Aerospace
(16, 38, 2), -- Aerospace
(16, 34, 2), -- Aerospace
(16, 36, 3), -- Aerospace
(16, 17, 3), -- Aerospace
(19, 2, 20), -- Gambling
(20, 1, 10), -- University
(21, 33, 1), -- Software
(22, 1, 10), -- Pharmaceuticals
(22, 33, 1), -- Pharmaceuticals
(26, 3, 1000000), -- World_Government
(26, 4, 2000), -- World_Government
(26, 45, 300), -- World_Government
(27, 1, 50000), -- World_Currency
(27, 41, 300), -- World_Currency
(27, 46, 100), -- World_Currency
(28, 13, 10000), -- Space_Colonization
(28, 44, 500), -- Space_Colonization
(28, 36, 200); -- Space_Colonization

DROP TABLE IF EXISTS `supply_account_lookup`;
CREATE TABLE IF NOT EXISTS `supply_account_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_account_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_account_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `supply_account_lookup` ADD INDEX `account_key` (`account_key`);
ALTER TABLE `supply_account_lookup` ADD INDEX `supply_key` (`supply_key`);

DROP TABLE IF EXISTS `supply_account_trade_lookup`;
CREATE TABLE IF NOT EXISTS `supply_account_trade_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `trade_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_account_trade_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_account_trade_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `supply_account_trade_lookup` ADD INDEX `account_key` (`account_key`);
ALTER TABLE `supply_account_trade_lookup` ADD INDEX `supply_key` (`supply_key`);
ALTER TABLE `supply_account_trade_lookup` ADD INDEX `trade_key` (`trade_key`);

DROP TABLE IF EXISTS `supply`;
CREATE TABLE IF NOT EXISTS `supply` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL, -- core,food,cash_crops,materials,energy,valuables,metals,light,heavy,knowledge
  `suffix` varchar(256) NOT NULL,
  `can_trade` int(1) UNSIGNED NOT NULL,
  `market_price_key` int(10) UNSIGNED NULL,
  `gdp_bonus` int(1) UNSIGNED NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `supply` ADD INDEX `category_id` (`category_id`);
ALTER TABLE `supply` ADD INDEX `market_price_key` (`market_price_key`);

TRUNCATE TABLE `supply`;
INSERT INTO `supply` (`id`, `category_id`, `label`, `slug`, `suffix`, `can_trade`, `market_price_key`, `gdp_bonus`, `meta`) VALUES
(1, 1, 'Cash', 'cash', 'Billion', TRUE, NULL, NULL, 'Cash is consumed each hour for certain industries that require government cash and produced by taxing GDP'),
(2, 1, 'Support', 'support', '%', FALSE, NULL, NULL, 'Support is used for controlling units. It increases each minute depending on power structure and each hour depending on cash crops. If you go into debt, support will go to zero.'),
(3, 1, 'Population', 'population', 'K', FALSE, NULL, NULL, 'Population for each townships grows each hour. Townships without the needed supplies will shrink. Having a diverse diet available gives you a growth bonus'),
(4, 1, 'Territories', 'tiles', '', FALSE, NULL, NULL, 'Territories is the primary leaderboard stat and determines the offical winner of each round.'),
(5, 6, 'Timber', 'timber', 'Mt', TRUE, NULL, NULL, ''),
(6, 6, 'Fiber', 'fiber', 'Mt', TRUE, NULL, NULL, ''),
(7, 6, 'Ore', 'ore', 'Mt', TRUE, NULL, NULL, ''),
(8, 2, 'Grain', 'grain', 'Gt', TRUE, NULL, NULL, ''),
(9, 2, 'Fruit', 'fruit', 'Gt', TRUE, NULL, NULL, ''),
(10, 2, 'Vegetables', 'vegetables', 'Gt', TRUE, NULL, NULL, ''),
(11, 2, 'Livestock', 'livestock', 'Gt', TRUE, NULL, NULL, ''),
(12, 2, 'Fish', 'fish', 'Gt', TRUE, NULL, NULL, ''),
(13, 4, 'Energy', 'energy', 'MW', FALSE, NULL, NULL, ''),
(14, 4, 'Biofuel', 'biofuel', 'Gt', TRUE, NULL, NULL, ''),
(15, 4, 'Coal', 'coal', 'Gt', TRUE, NULL, NULL, ''),
(16, 4, 'Gas', 'gas', 'Mcf', TRUE, NULL, NULL, ''),
(17, 4, 'Oil', 'oil', 'MMbbl', TRUE, NULL, NULL, ''),
(18, 4, 'Uranium', 'uranium', 'Ton', TRUE, NULL, NULL, ''),
(19, 10, 'Silver', 'silver', 'kg', TRUE, 1, NULL, 'Silver Prices tend to move downward with occasional volatility'),
(20, 10, 'Gold', 'gold', 'kg', TRUE, 2, NULL, 'Gold Prices tend to move slowly with rare volatility'),
(21, 10, 'Platinum', 'platinum', 'kg', TRUE, 3, NULL, 'Platinum Prices tend to move quickly with extreme volatility'),
(22, 10, 'Gemstones', 'gemstones', 'Mct', TRUE, 4, NULL, 'Gemstones Prices tend to move up and down with strong volatility'),
(23, 3, 'Coffee', 'coffee', 'Mt', TRUE, NULL, NULL, ''),
(24, 3, 'Tea', 'tea', 'Mt', TRUE, NULL, NULL, ''),
(25, 3, 'Cannabis', 'cannabis', 'Mt', TRUE, NULL, NULL, ''),
(26, 3, 'Wine', 'wine', 'Mt', TRUE, NULL, NULL, ''),
(27, 3, 'Tobacco', 'tobacco', 'Mt', TRUE, NULL, NULL, ''),
(28, 5, 'Iron', 'iron', 'Gt', TRUE, NULL, NULL, ''),
(29, 5, 'Copper', 'copper', 'Mt', TRUE, NULL, NULL, ''),
(30, 5, 'Zinc', 'zinc', 'Mt', TRUE, NULL, NULL, ''),
(31, 5, 'Aluminum', 'aluminum', 'Mt', TRUE, NULL, NULL, ''),
(32, 5, 'Nickle', 'nickle', 'Mt', TRUE, NULL, NULL, ''),
(33, 7, 'Professors', 'professors', 'K', FALSE, NULL, NULL, ''),
(34, 7, 'Programmers', 'programmers', 'K', TRUE, NULL, NULL, ''),
(35, 8, 'Pharmaceuticals', 'pharmaceuticals', 'Ton', TRUE, NULL, NULL, ''),
(36, 7, 'Engineers', 'engineers', 'K', TRUE, NULL, NULL, ''),
(37, 6, 'Merchandise', 'merchandise', 'Mt', TRUE, NULL, NULL, ''),
(38, 8, 'Chemicals', 'chemicals', 'M gal', TRUE, NULL, NULL, ''),
(39, 8, 'Steel', 'steel', 'Gt', TRUE, NULL, NULL, ''),
(40, 8, 'Electronics', 'electronics', 'M', TRUE, NULL, NULL, ''),
(41, 9, 'Cargo Ships', 'port', 'K', TRUE, NULL, 10, 'Increases National GDP by 10% and is reduced by 1 each hour.'),
(42, 9, 'Machines', 'machinery', 'K', TRUE, NULL, 10, 'Increases National GDP by 10% and is reduced by 1 each hour.'),
(43, 9, 'Automobiles', 'automotive', 'M', TRUE, NULL, 10, 'Increases National GDP by 10% and is reduced by 1 each hour.'),
(44, 9, 'Aircraft', 'aircraft', '', TRUE, NULL, 10, 'Increases National GDP by 10% and is reduced by 1 each hour.'),
(45, 9, 'Culture', 'culture', '', TRUE, NULL, 30, 'Increases National GDP by 30% and is reduced by 1 each hour.'),
(46, 9, 'Influence', 'influence', '', FALSE, NULL, 30, 'Increases National GDP by 30% and is reduced by 1 each hour.');

DROP TABLE IF EXISTS `market_price`;
CREATE TABLE IF NOT EXISTS `market_price` (
  `id` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `starting_price` int(10) UNSIGNED NOT NULL,
  `percent_chance_of_increase` int(10) UNSIGNED NOT NULL,
  `max_increase` int(10) UNSIGNED NOT NULL,
  `max_decrease` int(10) UNSIGNED NOT NULL,
  `min_increase` int(10) UNSIGNED NOT NULL,
  `min_decrease` int(10) UNSIGNED NOT NULL,
  `min_price` int(10) UNSIGNED NOT NULL,
  `max_price` int(10) UNSIGNED NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `market_price` ADD PRIMARY KEY (`id`);
ALTER TABLE `market_price` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `market_price` ADD INDEX `supply_key` (`supply_key`);

TRUNCATE TABLE `market_price`;
INSERT INTO `market_price` (`id`, `supply_key`, `amount`, `starting_price`,
  `percent_chance_of_increase`, `max_increase`, `max_decrease`,
  `min_increase`, `min_decrease`, `min_price`, `max_price`) VALUES
-- Silver
(1, 19, 1, 1, 
  45, 3, 5,
  1, 1, 1, 1000
),
-- Gold
(2, 20, 1, 1, 
  45, 2, 2,
  1, 1, 1, 1000
),
-- Platinum
(3, 21, 1, 1, 
  45, 10, 25,
  1, 1, 1, 1000
),
-- Gemstones
(4, 22, 1, 1, 
  45, 4, 5,
  1, 1, 1, 1000
);

DROP TABLE IF EXISTS `terrain`;
CREATE TABLE IF NOT EXISTS `terrain` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `terrain` ADD PRIMARY KEY (`id`);
ALTER TABLE `terrain` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

TRUNCATE TABLE `terrain`;
INSERT INTO `terrain` (`id`, `label`, `slug`, `meta`) VALUES
(1, 'Fertile', 'fertile', ''),
(2, 'Barren', 'barren', ''),
(3, 'Mountain', 'mountain', ''),
(4, 'Tundra', 'tundra', ''),
(5, 'Coastal', 'coastal', ''),
(6, 'Ocean', 'ocean', '');

DROP TABLE IF EXISTS `resource`;
CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `output_supply_key` int(10) UNSIGNED NOT NULL,
  `output_supply_amount` int(10) UNSIGNED NOT NULL,
  `is_value_resource` int(1) UNSIGNED NOT NULL,
  `is_energy_resource` int(1) UNSIGNED NOT NULL,
  `is_metal_resource` int(1) UNSIGNED NOT NULL,
  `frequency_per_world` int(10) UNSIGNED NOT NULL,
  `spawns_in_barren` int(1) UNSIGNED NOT NULL,
  `spawns_in_mountain` int(1) UNSIGNED NOT NULL,
  `spawns_in_tundra` int(1) UNSIGNED NOT NULL,
  `spawns_in_coastal` int(1) UNSIGNED NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `resource` ADD PRIMARY KEY (`id`);
ALTER TABLE `resource` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `resource` ADD INDEX `output_supply_key` (`output_supply_key`);

TRUNCATE TABLE `resource`;
INSERT INTO `resource` (`id`, `label`, `slug`, `output_supply_key`,
  `is_value_resource`, `is_energy_resource`, `is_metal_resource`, `output_supply_amount`,
  `frequency_per_world`, `spawns_in_barren`, `spawns_in_mountain`, `spawns_in_tundra`, `spawns_in_coastal`
) VALUES
-- value
(
  1, 'Silver', 'silver', 19,
  TRUE, FALSE, FALSE, 1,
  10, TRUE, TRUE, TRUE, FALSE
),
(
  2, 'Gold', 'gold', 20,
  TRUE, FALSE, FALSE, 1,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  3, 'Platinum', 'platinum', 21,
  TRUE, FALSE, FALSE, 1,
  3, TRUE, TRUE, TRUE, FALSE
),
(
  4, 'Gemstones', 'gemstones', 22,
  TRUE, FALSE, FALSE, 1,
  2, TRUE, TRUE, TRUE, FALSE
),
-- Energy
(
  5, 'Coal', 'coal', 15,
  FALSE, TRUE, FALSE, 3,
  10, TRUE, TRUE, TRUE, FALSE
),
(
  6, 'Gas', 'gas', 16,
  FALSE, TRUE, FALSE, 3,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  7, 'Oil', 'oil', 17,
  FALSE, TRUE, FALSE, 3,
  10, TRUE, FALSE, TRUE, TRUE
),
(
  8, 'Uranium', 'uranium', 18,
  FALSE, TRUE, FALSE, 3,
  5, TRUE, TRUE, TRUE, FALSE
),
-- Metals
(
  9, 'Iron', 'iron', 28,
  FALSE, FALSE, TRUE, 2,
  30, TRUE, TRUE, TRUE, FALSE
),
(
  10, 'Copper', 'copper', 29,
  FALSE, FALSE, TRUE, 2,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  11, 'Zinc', 'zinc', 30,
  FALSE, FALSE, TRUE, 2,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  12, 'Aluminum', 'aluminum', 31,
  FALSE, FALSE, TRUE, 2,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  13, 'Nickle', 'nickle', 32,
  FALSE, FALSE, TRUE, 2,
  5, TRUE, TRUE, TRUE, FALSE
);

DROP TABLE IF EXISTS `settlement`;
CREATE TABLE IF NOT EXISTS `settlement` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL, -- township, food, materials, energy, cash_crops,
  `is_township` int(1) NOT NULL,
  `is_food` int(1) NOT NULL,
  `is_material` int(1) NOT NULL,
  `is_energy` int(1) NOT NULL,
  `is_cash_crop` int(1) NOT NULL,
  `is_allowed_on_fertile` int(1) NOT NULL,
  `is_allowed_on_coastal` int(1) NOT NULL,
  `is_allowed_on_barren` int(1) NOT NULL,
  `is_allowed_on_mountain` int(1) NOT NULL,
  `is_allowed_on_tundra` int(1) NOT NULL,
  `base_population` int(10) UNSIGNED NOT NULL,
  `input_desc` varchar(256) NOT NULL,
  `gdp` int(10) UNSIGNED NULL,
  `output_supply_key` int(10) UNSIGNED NULL,
  `output_supply_amount` int(10) NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `settlement` ADD PRIMARY KEY (`id`);
ALTER TABLE `settlement` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `settlement` ADD INDEX `category_id` (`category_id`);
ALTER TABLE `settlement` ADD INDEX `output_supply_key` (`output_supply_key`);

TRUNCATE TABLE `settlement`;
INSERT INTO `settlement` (
  `id`, `label`, `slug`, `category_id`,
  `is_township`, `is_food`, `is_material`, `is_energy`, `is_cash_crop`,
  `is_allowed_on_fertile`, `is_allowed_on_coastal`, `is_allowed_on_barren`, `is_allowed_on_mountain`, `is_allowed_on_tundra`,
  `base_population`, `input_desc`, `output_supply_key`, `output_supply_amount`, `gdp`) VALUES
(1, 'Unclaimed', 'unclaimed', 1,
  FALSE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  0, '', NULL, NULL, 0
),
(2, 'Uninhabited', 'uninhabited', 1,
  FALSE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  0, '', NULL, NULL, 0
),
(3, 'Town', 'town', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  100, '', NULL, NULL, 5
),
(4, 'City', 'city', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  1000, '', NULL, NULL, 20
),
(5, 'Metro', 'metro', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10000, '', NULL, NULL, 50
),
(6, 'Grain', 'grain', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 8, 2, 1
),
(7, 'Fruit', 'fruit', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 9, 1, 2
),
(8, 'Vegetables', 'vegetables', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 10, 1, 1
),
(9, 'Livestock', 'livestock', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 11, 1, 3
),
(10, 'Fish', 'fish', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  100, '', 12, 1, 5
),
(11, 'Timber', 'timber', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 5, 3, 1
),
(12, 'Fiber', 'fiber', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 6, 2, 3
),
(13, 'Ore', 'ore', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  FALSE, FALSE, TRUE, TRUE, FALSE,
  100, '', 7, 1, 2
),
(14, 'Biofuel', 'biofuel', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 14, 1, 2
),
(15, 'Solar', 'solar', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, TRUE, FALSE, FALSE,
  100, '', 13, 1, 1
),
(16, 'Wind', 'wind', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, FALSE, TRUE, FALSE,
  100, '', 13, 1, 1
),
(17, 'Coffee', 'coffee', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 23, 1, 3
),
(18, 'Tea', 'tea', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 24, 1, 3
),
(19, 'Cannabis', 'cannabis', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 25, 1, 3
),
(20, 'Wine', 'wine', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 26, 1, 3
),
(21, 'Tobacco', 'tobacco', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  100, '', 27, 1, 3
);

DROP TABLE IF EXISTS `industry`;
CREATE TABLE IF NOT EXISTS `industry` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `output_supply_key` int(10) UNSIGNED NULL,
  `output_supply_amount` int(10) NULL,
  `upfront_cost` int(10) NULL,
  `minimum_settlement_size` int(10) UNSIGNED NULL, -- town, city, metro
  `required_terrain_key` int(10) UNSIGNED NULL,
  `gdp` int(10) UNSIGNED NULL,
  `is_victory` int(10) UNSIGNED NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `industry` ADD PRIMARY KEY (`id`);
ALTER TABLE `industry` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `industry` ADD INDEX `category_id` (`category_id`);
ALTER TABLE `industry` ADD INDEX `output_supply_key` (`output_supply_key`);
ALTER TABLE `industry` ADD INDEX `required_terrain_key` (`required_terrain_key`);

TRUNCATE TABLE `industry`;
INSERT INTO `industry` (
  `id`, `category_id`, `label`, `slug`, `minimum_settlement_size`, `required_terrain_key`, `is_victory`,
  `output_supply_key`, `output_supply_amount`, `upfront_cost`, `gdp`, `meta`) VALUES
-- government
(1, 1, 'Capitol', 'capitol', NULL, NULL, FALSE,
  null, 1, 10, 50, 'Spawns units'
),
(2, 1, 'Federal', 'federal', 4, NULL, FALSE,
  2, 10, 10, 5, 'Support is per hour'
),
(3, 1, 'Base', 'base', NULL, NULL, FALSE,
  null, 1, 10, 3, 'Spawns units'
),
-- energy
(4, 2, 'Biofuel', 'biofuel', NULL, NULL, FALSE,
  13, 10, NULL, 1, ''
),
(5, 2, 'Coal', 'coal', NULL, NULL, FALSE,
  13, 10, NULL, 2, ''
),
(6, 2, 'Gas', 'gas', NULL, NULL, FALSE,
  13, 15, NULL, 3, ''
),
(7, 2, 'Petroleum', 'petroleum', NULL, NULL, FALSE,
  13, 20, NULL, 10, ''
),
(8, 2, 'Nuclear', 'nuclear', NULL, NULL, FALSE,
  13, 25, NULL, 10, ''
),
-- light industry
(9, 5, 'Manufacturing', 'manufacturing', NULL, NULL, FALSE,
  37, 10, NULL, 5, ''
),
(10, 5, 'Chemicals', 'chemicals', NULL, NULL, FALSE,
  38, 5, NULL, 5, ''
),
(11, 5, 'Steel', 'steel', NULL, NULL, FALSE,
  39, 5, NULL, 5, ''
),
(12, 5, 'Electronics', 'electronics', 4, NULL, FALSE,
  40, 10, NULL, 10, ''
),
-- hevvy industry
(13, 6, 'Shipping Port', 'port', 4, 5, FALSE,
  41, 3, NULL, 50, 'Increases National GDP by 10%'
),
(14, 6, 'Machinery', 'machinery', 4, NULL, FALSE,
  42, 15, NULL, 30, 'Increases National GDP by 10%'
),
(15, 6, 'Automotive', 'automotive', 4, NULL, FALSE,
  43, 20, NULL, 40, 'Increases National GDP by 10%'
),
(16, 6, 'Aerospace', 'aerospace', 4, NULL, FALSE,
  44, 10, NULL, 50, 'Increases National GDP by 10%'
),
-- tourism
(17, 3, 'Leisure', 'leisure', NULL, 5, FALSE,
  null, 1, NULL, 20, ''
),
(18, 3, 'Resort', 'resort', NULL, 3, FALSE,
  null, 1, NULL, 10, ''
),
(19, 3, 'Gambling', 'gambling', NULL, NULL, FALSE,
  null, 1, NULL, 15, ''
),
-- knowledge/quaternary
(20, 4, 'University', 'university', NULL, NULL, FALSE,
  33, 1, NULL, 3, ''
),
(21, 4, 'Software', 'software', 4, NULL, FALSE,
  34, 5, NULL, 8, ''
),
(22, 4, 'Healthcare', 'healthcare', 4, NULL, FALSE,
  35, 3, NULL, 6, ''
),
-- metro
(23, 7, 'Financial', 'financial_banking', 5, NULL, FALSE,
  46, 1, NULL, 200, 'Increases National GDP by 30%'
),
(24, 7, 'Entertainment', 'entertainment_media', 5, NULL, FALSE,
  45, 10, NULL, 50, 'Increases National GDP by 30%'
),
(25, 7, 'Engineering', 'engineering_design', 5, NULL, FALSE,
  36, 5, NULL, 100, ''
),
-- victory
(26, 8, 'World Government', 'world_government', 5, NULL, TRUE,
  NULL, 1, NULL, 1000, 'Wins the game'
),
(27, 8, 'World Currency', 'World_currency', 5, NULL, TRUE,
  NULL, 1, NULL, 1000, 'Wins the game'
),
(28, 8, 'Space Colonization', 'space_colonization', 5, NULL, TRUE,
  NULL, 1, NULL, 1000, 'Wins the game'
);

-- 
-- 
-- 

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(256) NOT NULL,
  `facebook_id` int(16) NOT NULL,
  `email` varchar(256) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `ab_test` varchar(256) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `user` ADD PRIMARY KEY (`id`);
ALTER TABLE `user` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `chat`;
CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_key` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `color` varchar(8) NOT NULL,
  `message` text NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `chat` ADD PRIMARY KEY (`id`);
ALTER TABLE `chat` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `chat` ADD INDEX `user_key` (`user_key`);
ALTER TABLE `chat` ADD INDEX `account_key` (`account_key`);
ALTER TABLE `chat` ADD INDEX `world_key` (`world_key`);

DROP TABLE IF EXISTS `analytics`;
CREATE TABLE IF NOT EXISTS `analytics` (
  `id` int(10) UNSIGNED NOT NULL,
  `marketing_slug` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `analytics` ADD PRIMARY KEY (`id`);
ALTER TABLE `analytics` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `ip_request`;
CREATE TABLE IF NOT EXISTS `ip_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(64) NOT NULL,
  `request` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `ip_request` ADD PRIMARY KEY (`id`);
ALTER TABLE `ip_request` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
