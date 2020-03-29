-- Database sql
DROP TABLE IF EXISTS world;
DROP TABLE IF EXISTS tile;
DROP TABLE IF EXISTS unit_type;
DROP TABLE IF EXISTS account;
DROP TABLE IF EXISTS trade_request;
DROP TABLE IF EXISTS agreement_lookup;
DROP TABLE IF EXISTS supply_account_lookup;
DROP TABLE IF EXISTS supply_account_trade_lookup;
DROP TABLE IF EXISTS supply_trade_lookup;
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
  `crontab` varchar(256) NOT NULL,
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
  `unit_key` int(10) UNSIGNED NULL, -- Infantry, Tanks, Commandos, none as null
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

DROP TABLE IF EXISTS `unit_type`;
CREATE TABLE IF NOT EXISTS `unit_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(126) NOT NULL,
  `strength_against_key` int(10) UNSIGNED NOT NULL,
  `cost_base` int(4) NOT NULL,
  `color` varchar(8) NULL,
  `character` varchar(8) NULL,
  `can_take_tiles` int(1) NOT NULL,
  `can_take_towns` int(1) NOT NULL,
  `can_take_cities` int(1) NOT NULL,
  `can_take_metros` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `unit_type` ADD PRIMARY KEY (`id`);
ALTER TABLE `unit_type` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

TRUNCATE TABLE `unit_type`;
INSERT INTO `unit_type` (`id`, `slug`, `strength_against_key`, `cost_base`, `color`, `character`,
  `can_take_tiles`, `can_take_towns`, `can_take_cities`, `can_take_metros`) VALUES
(1, 'Infantry', 3, 50, 'FF0000', 'I',
  TRUE, TRUE, FALSE, FALSE),
(2, 'Tanks', 1, 150, '00FF00', 'T',
  TRUE, TRUE, TRUE, TRUE),
(3, 'Commandos', 2, 100, 'BC13FE', 'C',
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
  `power_structure` int(10) UNSIGNED NULL, -- Democracy, Oligarchy, Autocracy, Anarchy
  `tax_rate` int(10) UNSIGNED NOT NULL,
  `ideology` int(10) UNSIGNED NULL, -- Socialism, Free Market
  `last_law_change` timestamp NOT NULL,
  -- meta
  `last_load` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `account` ADD PRIMARY KEY (`id`);
ALTER TABLE `account` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `trade_request`;
CREATE TABLE IF NOT EXISTS `trade_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_account_key` int(10) UNSIGNED NOT NULL,
  `receive_account_key` int(10) UNSIGNED NOT NULL,
  `request_message` text NOT NULL,
  `response_message` text NOT NULL,
  `request_seen` int(1) UNSIGNED NOT NULL,
  `response_seen` int(1) UNSIGNED NOT NULL,
  `is_accepted` int(1) UNSIGNED NOT NULL,
  `is_rejected` int(1) UNSIGNED NOT NULL,
  `is_declared` int(1) UNSIGNED NOT NULL,
  `agreement_key` int(10) UNSIGNED NOT NULL, -- War, Peace, Passage
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `trade_request` ADD PRIMARY KEY (`id`);
ALTER TABLE `trade_request` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `agreement_lookup`;
CREATE TABLE IF NOT EXISTS `agreement_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `a_account_key` int(10) UNSIGNED NOT NULL,
  `b_account_key` int(10) UNSIGNED NOT NULL,
  `agreement_key` int(10) UNSIGNED NOT NULL, -- War, Peace, Passage
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `agreement_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `agreement_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `supply_trade_lookup`;
CREATE TABLE IF NOT EXISTS `supply_trade_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `trade_key` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_trade_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_trade_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `supply_industry_lookup`;
CREATE TABLE IF NOT EXISTS `supply_industry_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `industry_key` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_industry_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_industry_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

TRUNCATE TABLE `supply_industry_lookup`;
INSERT INTO `supply_industry_lookup` (`industry_key`, `supply_key`, `amount`) VALUES
(2, 1, 10), -- Federal
(3, 1, 10), -- Base
(4, 14, 1), -- Biofuel
(5, 15, 1), -- Coal
(6, 16, 1), -- Gas
(7, 17, 1), -- Petroleum
(8, 18, 1), -- Nuclear
(9, 5, 1), -- Manufacturing
(9, 6, 1), -- Manufacturing
(9, 7, 1), -- Manufacturing
(9, 13, 1), -- Manufacturing
(10, 13, 1), -- Chemicals
(11, 28, 1), -- Steel
(11, 13, 1), -- Steel
(12, 29, 1), -- Electronics
(12, 7, 1), -- Electronics
(13, 39, 2), -- Port
(14, 30, 1), -- Machinery
(14, 39, 1), -- Machinery
(14, 40, 1), -- Machinery
(14, 36, 1), -- Machinery
(14, 38, 1), -- Machinery
(15, 31, 1), -- Automotive
(15, 39, 1), -- Automotive
(15, 40, 1), -- Automotive
(15, 38, 1), -- Automotive
(15, 36, 1), -- Automotive
(15, 17, 1), -- Automotive
(16, 31, 1), -- Aerospace
(16, 32, 1), -- Aerospace
(16, 39, 1), -- Aerospace
(16, 40, 1), -- Aerospace
(16, 38, 1), -- Aerospace
(16, 34, 1), -- Aerospace
(16, 36, 1), -- Aerospace
(16, 17, 1), -- Aerospace
(19, 2, 1), -- Gambling
(20, 1, 10), -- University
(21, 33, 1), -- Software
(22, 1, 10); -- Healthcare

DROP TABLE IF EXISTS `supply_account_lookup`;
CREATE TABLE IF NOT EXISTS `supply_account_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_account_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_account_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `supply_account_trade_lookup`;
CREATE TABLE IF NOT EXISTS `supply_account_trade_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `supply_account_lookup_key` int(10) UNSIGNED NOT NULL,
  `trade_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_account_trade_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_account_trade_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `supply`;
CREATE TABLE IF NOT EXISTS `supply` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL, -- core,food,cash_crops,materials,energy,valuables,metals,light,heavy,knowledge
  `suffix` varchar(256) NOT NULL,
  `can_trade` int(1) UNSIGNED NOT NULL,
  `market_price_key` int(10) UNSIGNED NULL,
  `gdp_increase` int(1) UNSIGNED NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

TRUNCATE TABLE `supply`;
INSERT INTO `supply` (`id`, `category_id`, `label`, `slug`, `suffix`, `can_trade`, `market_price_key`, `gdp_increase`, `meta`) VALUES
(1, 1, 'Cash', 'cash', 'M', TRUE, NULL, NULL, 'This rules everything'),
(2, 1, 'Support', 'support', '%', FALSE, NULL, NULL, 'Increases every minute depending on your power structure and each hour depending on how many types of cash crops you have'),
(3, 1, 'Population', 'population', 'K', FALSE, NULL, NULL, 'Each hour the population will grow depending on how many types of food you have'),
(4, 1, 'Territories', 'tiles', '', FALSE, NULL, NULL, 'The primary leaderboard stat'),
(5, 5, 'Timber', 'timber', '', TRUE, NULL, NULL, ''),
(6, 5, 'Fiber', 'fiber', '', TRUE, NULL, NULL, ''),
(7, 5, 'Ore', 'ore', '', TRUE, NULL, NULL, ''),
(8, 2, 'Grain', 'grain', '', TRUE, NULL, NULL, ''),
(9, 2, 'Fruit', 'fruit', '', TRUE, NULL, NULL, ''),
(10, 2, 'Vegetables', 'vegetables', '', TRUE, NULL, NULL, ''),
(11, 2, 'Livestock', 'livestock', '', TRUE, NULL, NULL, ''),
(12, 2, 'Fish', 'fish', '', TRUE, NULL, NULL, ''),
(13, 4, 'Energy', 'energy', '', FALSE, NULL, NULL, ''),
(14, 4, 'Biofuel', 'biofuel', '', TRUE, NULL, NULL, ''),
(15, 4, 'Coal', 'coal', '', TRUE, NULL, NULL, ''),
(16, 4, 'Gas', 'gas', '', TRUE, NULL, NULL, ''),
(17, 4, 'Oil', 'oil', '', TRUE, NULL, NULL, ''),
(18, 4, 'Uranium', 'uranium', '', TRUE, NULL, NULL, ''),
(19, 10, 'Silver', 'silver', '', TRUE, 1, NULL, 'Prices tend to stay low, with brief moments of volatility'),
(20, 10, 'Gold', 'gold', '', TRUE, 2, NULL, 'Prices tend to slowly but steadily increase over time'),
(21, 10, 'Platinum', 'platinum', '', TRUE, 3, NULL, 'Prices tend to be extremely volatile'),
(22, 10, 'Gemstones', 'gemstones', '', TRUE, 4, NULL, 'Prices tend to move higher with frequent volatility'),
(23, 3, 'Coffee', 'coffee', '', TRUE, NULL, NULL, ''),
(24, 3, 'Tea', 'tea', '', TRUE, NULL, NULL, ''),
(25, 3, 'Cannabis', 'cannabis', '', TRUE, NULL, NULL, ''),
(26, 3, 'Alcohol', 'alcohol', '', TRUE, NULL, NULL, ''),
(27, 3, 'Tobacco', 'tobacco', '', TRUE, NULL, NULL, ''),
(28, 6, 'Iron', 'iron', '', TRUE, NULL, NULL, ''),
(29, 6, 'Copper', 'copper', '', TRUE, NULL, NULL, ''),
(30, 6, 'Zinc', 'zinc', '', TRUE, NULL, NULL, ''),
(31, 6, 'Aluminum', 'aluminum', '', TRUE, NULL, NULL, ''),
(32, 6, 'Nickle', 'nickle', '', TRUE, NULL, NULL, ''),
(33, 7, 'Education', 'education', '', FALSE, NULL, NULL, ''),
(34, 7, 'Software', 'software', '', TRUE, NULL, NULL, ''),
(35, 7, 'Healthcare', 'healthcare', '', FALSE, NULL, NULL, ''),
(36, 7, 'Engineering', 'engineering', '', TRUE, NULL, NULL, ''),
(37, 8, 'Merchandise', 'merchandise', '', TRUE, NULL, NULL, ''),
(38, 8, 'Chemicals', 'chemicals', '', TRUE, NULL, NULL, ''),
(39, 8, 'Steel', 'steel', '', TRUE, NULL, NULL, ''),
(40, 8, 'Electronics', 'electronics', '', TRUE, NULL, NULL, ''),
(41, 9, 'Shipping Ports', 'port', '', FALSE, NULL, 50, 'Increases National GDP by 50%'),
(42, 9, 'Machinery', 'machinery', '', TRUE, NULL, 50, 'Increases National GDP by 50%'),
(43, 9, 'Automotive', 'automotive', '', TRUE, NULL, 50, 'Increases National GDP by 50%'),
(44, 9, 'Aerospace', 'aerospace', '', TRUE, NULL, 50, 'Increases National GDP by 50%'),
(45, 7, 'Entertainment', 'entertainment', '', FALSE, NULL, 50, 'Increases National GDP by 50%'),
(46, 7, 'Financial', 'financial', '', FALSE, NULL, 50, 'Increases National GDP by 50%');

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

TRUNCATE TABLE `market_price`;
INSERT INTO `market_price` (`id`, `supply_key`, `amount`, `starting_price`,
  `percent_chance_of_increase`, `max_increase`, `max_decrease`,
  `min_increase`, `min_decrease`, `min_price`, `max_price`) VALUES
-- Silver
(1, 19, 1, 1, 
  40, 4, 5,
  1, 1, 1, 1000
),
-- Gold
(2, 20, 1, 1, 
  55, 3, 2,
  1, 1, 1, 1000
),
-- Platinum
(3, 21, 1, 1, 
  50, 5, 5,
  1, 1, 1, 1000
),
-- Gemstones
(4, 22, 1, 1, 
  50, 2, 1,
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

TRUNCATE TABLE `resource`;
INSERT INTO `resource` (`id`, `label`, `slug`, `output_supply_key`,
  `is_value_resource`, `is_energy_resource`, `is_metal_resource`,
  `frequency_per_world`, `spawns_in_barren`, `spawns_in_mountain`, `spawns_in_tundra`, `spawns_in_coastal`
) VALUES
-- value
(
  1, 'Silver', 'silver', 19,
  TRUE, FALSE, FALSE,
  10, TRUE, TRUE, TRUE, FALSE
),
(
  2, 'Gold', 'gold', 20,
  TRUE, FALSE, FALSE,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  3, 'Platinum', 'platinum', 21,
  TRUE, FALSE, FALSE,
  3, TRUE, TRUE, TRUE, FALSE
),
(
  4, 'Gemstones', 'gemstones', 22,
  TRUE, FALSE, FALSE,
  2, TRUE, TRUE, TRUE, FALSE
),
-- Energy
(
  5, 'Coal', 'coal', 15,
  FALSE, TRUE, FALSE,
  15, TRUE, TRUE, TRUE, FALSE
),
(
  6, 'Gas', 'gas', 16,
  FALSE, TRUE, FALSE,
  12, TRUE, TRUE, TRUE, FALSE
),
(
  7, 'Oil', 'oil', 17,
  FALSE, TRUE, FALSE,
  10, TRUE, FALSE, TRUE, TRUE
),
(
  8, 'Uranium', 'uranium', 18,
  FALSE, TRUE, FALSE,
  3, TRUE, TRUE, TRUE, FALSE
),
-- Metals
(
  9, 'Iron', 'iron', 28,
  FALSE, FALSE, TRUE,
  20, TRUE, TRUE, TRUE, FALSE
),
(
  10, 'Copper', 'copper', 29,
  FALSE, FALSE, TRUE,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  11, 'Zinc', 'zinc', 30,
  FALSE, FALSE, TRUE,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  12, 'Aluminum', 'aluminum', 31,
  FALSE, FALSE, TRUE,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  13, 'Nickle', 'nickle', 32,
  FALSE, FALSE, TRUE,
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

TRUNCATE TABLE `settlement`;
INSERT INTO `settlement` (
  `label`, `slug`, `category_id`,
  `is_township`, `is_food`, `is_material`, `is_energy`, `is_cash_crop`,
  `is_allowed_on_fertile`, `is_allowed_on_coastal`, `is_allowed_on_barren`, `is_allowed_on_mountain`, `is_allowed_on_tundra`,
  `base_population`, `input_desc`, `output_supply_key`, `output_supply_amount`, `gdp`) VALUES
('Unclaimed', 'unclaimed', 1,
  FALSE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  0, '', NULL, NULL, 0
),
('Uninhabited', 'uninhabited', 1,
  FALSE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  0, '', NULL, NULL, 0
),
('Town', 'town', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  100, '1 food, 1 energy', NULL, NULL, 5
),
('City', 'city', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  1000, '3 food, 3 energy, 1 cash crop, 1 merchandise', NULL, NULL, 10
),
('Metro', 'metro', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10000, '5 food, 5 energy, 3 cash crop, 3 merchandise, 1 steel, 1 healthcare', NULL, NULL, 20
),
('Grain', 'grain', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 8, 3, 1
),
('Fruit', 'fruit', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 9, 2, 1
),
('Vegetables', 'vegetables', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 10, 2, 1
),
('Livestock', 'livestock', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 11, 1, 2
),
('Fish', 'fish', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  10, '', 12, 2, 4
),
('Timber', 'timber', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 5, 3, 1
),
('Fiber', 'fiber', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 6, 2, 3
),
('Ore', 'ore', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  FALSE, FALSE, TRUE, TRUE, FALSE,
  10, '', 7, 1, 2
),
('Biofuel', 'biofuel', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 14, 1, 1
),
('Solar', 'solar', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  10, '', 13, 1, 1
),
('Wind', 'wind', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  10, '', 13, 1, 1
),
('Coffee', 'coffee', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 23, 5, 3
),
('Tea', 'tea', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 24, 5, 3
),
('Cannabis', 'cannabis', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 25, 5, 3
),
('Alcohol', 'alcohol', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 26, 5, 3
),
('Tobacco', 'tobacco', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, '', 27, 5, 3
);

DROP TABLE IF EXISTS `industry`;
CREATE TABLE IF NOT EXISTS `industry` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `output_supply_key` int(10) UNSIGNED NULL,
  `output_supply_amount` int(10) NULL,
  `minimum_settlement_size` int(10) UNSIGNED NULL, -- town, city, metro
  `required_terrain_key` int(10) UNSIGNED NULL,
  `gdp` int(10) UNSIGNED NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `industry` ADD PRIMARY KEY (`id`);
ALTER TABLE `industry` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

TRUNCATE TABLE `industry`;
INSERT INTO `industry` (
  `id`, `category_id`, `label`, `slug`, `minimum_settlement_size`, `required_terrain_key`,
  `output_supply_key`, `output_supply_amount`, `gdp`, `meta`) VALUES
-- government
(1, 1, 'Capitol', 'capitol', NULL, NULL,
  null, 10, 10, 'Spawns units, creates corruption'
),
(2, 1, 'Federal', 'federal', NULL, NULL,
  2, 10, 5, ''
),
(3, 1, 'Base', 'base', NULL, NULL,
  null, 1, 3, 'Spawns units'
),
-- energy
(4, 2, 'Biofuel', 'biofuel', NULL, NULL,
  13, 2, 1, ''
),
(5, 2, 'Coal', 'coal', NULL, NULL,
  13, 3, 1, ''
),
(6, 2, 'Gas', 'gas', NULL, NULL,
  13, 4, 2, ''
),
(7, 2, 'Petroleum', 'petroleum', NULL, NULL,
  13, 8, 5, ''
),
(8, 2, 'Nuclear', 'nuclear', NULL, NULL,
  13, 10, 5, ''
),
-- light industry
(9, 3, 'Manufacturing', 'manufacturing', NULL, NULL,
  37, 1, 5, ''
),
(10, 3, 'Chemicals', 'chemicals', NULL, NULL,
  38, 3, 5, ''
),
(11, 3, 'Steel', 'steel', NULL, NULL,
  39, 5, 3, ''
),
(12, 3, 'Electronics', 'electronics', NULL, NULL,
  40, 3, 10, ''
),
-- hevvy industry
(13, 4, 'Shipping Port', 'port', 2, 5,
  41, 1, 50, 'Increases National GDP by 50%'
),
(14, 4, 'Machinery', 'machinery', NULL, NULL,
  42, 3, 30, 'Increases National GDP by 50%'
),
(15, 4, 'Automotive', 'automotive', 2, NULL,
  43, 3, 40, 'Increases National GDP by 50%'
),
(16, 4, 'Aerospace', 'aerospace', 2, NULL,
  44, 3, 50, 'Increases National GDP by 50%'
),
-- tourism
(17, 5, 'Leisure', 'leisure', NULL, 5,
  null, 1, 10, ''
),
(18, 5, 'Resort', 'resort', NULL, 3,
  null, 1, 5, ''
),
(19, 5, 'Gambling', 'gambling', 2, NULL,
  null, 1, 10, ''
),
-- knowledge/quaternary
(20, 6, 'University', 'university', NULL, NULL,
  33, 3, 3, ''
),
(21, 6, 'Software', 'software', 2, NULL,
  34, 3, 8, ''
),
(22, 6, 'Healthcare', 'healthcare', 2, NULL,
  35, 1, 6, ''
),
-- metro
(23, 7, 'Financial & Banking', 'financial_banking', 3, NULL,
  45, 1, 200, 'Increases National GDP by 50%'
),
(24, 7, 'Entertainment & Media', 'entertainment_media', 3, NULL,
  46, 1, 50, 'Increases National GDP by 50%'
),
(25, 7, 'Engineering & Design', 'engineering_design', 3, NULL,
  36, 5, 100, ''
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
  `username` varchar(64) NOT NULL,
  `color` varchar(8) NOT NULL,
  `message` text NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `chat` ADD PRIMARY KEY (`id`);
ALTER TABLE `chat` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

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
