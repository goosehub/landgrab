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

CREATE TABLE `world` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(126) NOT NULL,
  `tile_size` int(4) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `world` ADD PRIMARY KEY (`id`);
ALTER TABLE `world` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `tile` (
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
  `tile_name` varchar(512) NULL,
  `tile_desc` text NULL,
  `color` varchar(8) NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `tile` ADD PRIMARY KEY (`id`);
ALTER TABLE `tile` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `unit_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(126) NOT NULL,
  `strength_against_key` int(10) UNSIGNED NOT NULL,
  `cost_base` int(4) NOT NULL,
  `color` varchar(8) NULL,
  `character` varchar(8) NULL,
  `can_take_tiles` int(1) NOT NULL,
  `can_take_towns` int(1) NOT NULL,
  `can_take_cities` int(1) NOT NULL,
  `can_take_metros` int(1) NOT NULL,
  `desc` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `unit_type` ADD PRIMARY KEY (`id`);
ALTER TABLE `unit_type` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `unit_type` (`id`, `slug`, `strength_against_key`, `cost_base`, `color`, `character`,
  `can_take_tiles`, `can_take_towns`, `can_take_cities`, `can_take_metros`, `desc`) VALUES
(1, 'Infantry', 3, 50, 'AA3939', 'IN'
  TRUE, TRUE, FALSE, FALSE, ''),
(2, 'Tanks', 1, 150, '228B22', 'T',
  TRUE, TRUE, TRUE, TRUE, ''),
(3, 'Commandos', 2, 100, 'FFA500', 'C',
  TRUE, FALSE, FALSE, FALSE, '');

CREATE TABLE `account` (
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
  `government` int(10) UNSIGNED NULL, -- Democracy, Oligarchy, Autocracy, Anarchy as null
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

CREATE TABLE `trade_request` (
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

CREATE TABLE `agreement_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `a_account_key` int(10) UNSIGNED NOT NULL,
  `b_account_key` int(10) UNSIGNED NOT NULL,
  `agreement_key` int(10) UNSIGNED NOT NULL, -- War, Peace, Passage
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `agreement_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `agreement_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `supply_trade_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `trade_key` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_trade_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_trade_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `supply_industry_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `industry_key` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_industry_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_industry_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `supply_industry_lookup` (`industry_key`, `supply_key`, `amount`) VALUES
(4, 7, 1), -- Manufacturing
(5, 5, 1), -- Timber
(6, 6, 1), -- Textile
(7, 14, 1), -- Biofuel
(8, 15, 1), -- Coal
(9, 16, 1), -- Gas
(10, 17, 1), -- Petroleum
(11, 18, 1), -- Nuclear
(12, 13, 1), -- Chemicals
(13, 28, 1), -- Steel
(14, 29, 1), -- Electronics
(15, 39, 1), -- Port
(16, 30, 1), -- Machinery
(16, 39, 1), -- Machinery
(16, 40, 1), -- Machinery
(16, 36, 1), -- Machinery
(16, 38, 1), -- Machinery
(17, 31, 1), -- Automotive
(17, 39, 1), -- Automotive
(17, 40, 1), -- Automotive
(17, 38, 1), -- Automotive
(17, 36, 1), -- Automotive
(18, 31, 1), -- Aerospace
(18, 32, 1), -- Aerospace
(18, 39, 1), -- Aerospace
(18, 40, 1), -- Aerospace
(18, 38, 1), -- Aerospace
(18, 34, 1), -- Aerospace
(18, 36, 1), -- Aerospace
(21, 2, 1), -- Gambling
(22, 1, 10), -- University
(23, 33, 1), -- Software
(24, 1, 10); -- Healthcare

CREATE TABLE `supply_account_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_account_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_account_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `supply_account_trade_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `supply_account_lookup_key` int(10) UNSIGNED NOT NULL,
  `trade_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply_account_trade_lookup` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply_account_trade_lookup` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `supply` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL, -- core,food,cash_crops,materials,energy,valuables,metals,light,heavy,knowledge
  `suffix` varchar(256) NOT NULL,
  `can_trade` int(1) UNSIGNED NOT NULL,
  `can_sell` int(1) UNSIGNED NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `supply` (`id`, `category_id`, `label`, `slug`, `suffix`, `can_trade`, `can_sell`, `meta`) VALUES
(1, 1, 'Cash', 'cash', 'M', TRUE, FALSE, ''),
(2, 1, 'Support', 'support', '%', FALSE, FALSE, ''),
(3, 1, 'Population', 'population', 'K', FALSE, FALSE, ''),
(4, 1, 'Territories', 'tiles', '', FALSE, FALSE, ''),
(5, 2, 'Timber', 'timber', 'Ton', TRUE, FALSE, ''),
(6, 2, 'Fiber', 'fiber', 'Ton', TRUE, FALSE, ''),
(7, 2, 'Ore', 'ore', 'Ton', TRUE, FALSE, ''),
(8, 3, 'Grain', 'grain', 'Ton', TRUE, FALSE, ''),
(9, 3, 'Fruit', 'fruit', 'Ton', TRUE, FALSE, ''),
(10, 3, 'Vegetables', 'vegetables', 'Ton', TRUE, FALSE, ''),
(11, 3, 'Livestock', 'livestock', 'Ton', TRUE, FALSE, ''),
(12, 3, 'Fish', 'fish', 'Ton', TRUE, FALSE, ''),
(13, 4, 'Energy', 'energy', 'MW', FALSE, FALSE, ''),
(14, 4, 'Biofuel', 'biofuel', 'Ton', TRUE, FALSE, ''),
(15, 4, 'Coal', 'coal', 'Ton', TRUE, FALSE, ''),
(16, 4, 'Gas', 'gas', 'Tcf', TRUE, FALSE, ''),
(17, 4, 'Oil', 'oil', 'MMb/d', TRUE, FALSE, ''),
(18, 4, 'Uranium', 'uranium', 'Ton', TRUE, FALSE, ''),
(19, 5, 'Silver', 'silver', 'Ton', TRUE, TRUE, ''),
(20, 5, 'Gold', 'gold', 'Ton', TRUE, TRUE, ''),
(21, 5, 'Platinum', 'platinum', 'Ton', TRUE, TRUE, ''),
(22, 5, 'Gemstones', 'gemstones', 'CT', TRUE, TRUE, ''),
(23, 6, 'Coffee', 'coffee', 'Ton', TRUE, TRUE, ''),
(24, 6, 'Tea', 'tea', 'Ton', TRUE, TRUE, ''),
(25, 6, 'Cannabis', 'cannabis', 'Ton', TRUE, TRUE, ''),
(26, 6, 'Alcohol', 'alcohol', 'Ton', TRUE, TRUE, ''),
(27, 6, 'Tobacco', 'tobacco', 'Ton', TRUE, TRUE, ''),
(28, 7, 'Iron', 'iron', 'Ton', TRUE, FALSE, ''),
(29, 7, 'Copper', 'copper', 'Ton', TRUE, FALSE, ''),
(30, 7, 'Zinc', 'zinc', 'Ton', TRUE, FALSE, ''),
(31, 7, 'Aluminum', 'aluminum', 'Ton', TRUE, FALSE, ''),
(32, 7, 'Nickle', 'nickle', 'Ton', TRUE, FALSE, ''),
(33, 8, 'Education', 'education', '', FALSE, FALSE, ''),
(34, 8, 'Software', 'software', '', TRUE, FALSE, ''),
(35, 8, 'Healthcare', 'healthcare', '', FALSE, FALSE, ''),
(36, 8, 'Engineering', 'engineering', '', TRUE, FALSE, ''),
(37, 9, 'Merchandise', 'merchandise', ' Shipments', TRUE, FALSE, ''),
(38, 9, 'Chemicals', 'chemicals', 'Kl', TRUE, FALSE, ''),
(39, 9, 'Steel', 'steel', 'Ton', TRUE, FALSE, ''),
(40, 9, 'Electronics', 'electronics', ' Shipments', TRUE, FALSE, ''),
(41, 10, 'Shipping Ports', 'port', '', FALSE, FALSE, ''),
(42, 10, 'Machinery', 'machinery', ' Shipments', TRUE, FALSE, ''),
(43, 10, 'Automotive', 'automotive', ' Shipments', TRUE, FALSE, ''),
(44, 10, 'Aerospace', 'aerospace', ' Shipments', TRUE, FALSE, '');

CREATE TABLE `terrain` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `terrain` ADD PRIMARY KEY (`id`);
ALTER TABLE `terrain` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `terrain` (`id`, `label`, `slug`, `meta`) VALUES
(1, 'Fertile', 'fertile', ''),
(2, 'Barren', 'barren', ''),
(3, 'Mountain', 'mountain', ''),
(4, 'Tundra', 'tundra', ''),
(5, 'Coastal', 'coastal', ''),
(6, 'Ocean', 'ocean', '');

CREATE TABLE `resource` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
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

INSERT INTO `resource` (`id`, `label`, `slug`,
  `is_value_resource`, `is_energy_resource`, `is_metal_resource`,
  `frequency_per_world`, `spawns_in_barren`, `spawns_in_mountain`, `spawns_in_tundra`, `spawns_in_coastal`
) VALUES
-- value
(
  1, 'Silver', 'silver',
  TRUE, FALSE, FALSE,
  15, TRUE, TRUE, TRUE, FALSE
),
(
  2, 'Gold', 'gold',
  TRUE, FALSE, FALSE,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  3, 'Platinum', 'platinum',
  TRUE, FALSE, FALSE,
  3, TRUE, TRUE, TRUE, FALSE
),
(
  4, 'Gemstones', 'gemstones',
  TRUE, FALSE, FALSE,
  2, TRUE, TRUE, TRUE, FALSE
),
-- Energy
(
  5, 'Coal', 'coal',
  FALSE, TRUE, FALSE,
  25, TRUE, TRUE, TRUE, FALSE
),
(
  6, 'Gas', 'gas',
  FALSE, TRUE, FALSE,
  12, TRUE, TRUE, TRUE, FALSE
),
(
  7, 'Oil', 'oil',
  FALSE, TRUE, FALSE,
  10, TRUE, FALSE, TRUE, TRUE
),
(
  8, 'Uranium', 'uranium',
  FALSE, TRUE, FALSE,
  3, TRUE, TRUE, TRUE, FALSE
),
-- Metals
(
  9, 'Iron', 'iron',
  FALSE, FALSE, TRUE,
  30, TRUE, TRUE, TRUE, FALSE
),
(
  10, 'Copper', 'copper',
  FALSE, FALSE, TRUE,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  11, 'Zinc', 'zinc',
  FALSE, FALSE, TRUE,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  12, 'Aluminum', 'aluminum',
  FALSE, FALSE, TRUE,
  5, TRUE, TRUE, TRUE, FALSE
),
(
  13, 'Nickle', 'nickle',
  FALSE, FALSE, TRUE,
  5, TRUE, TRUE, TRUE, FALSE
);

CREATE TABLE `settlement` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL, -- incorporated, food, materials, energy, cash_crops,
  `is_incorporated` int(1) NOT NULL,
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
  `defense_bonus` int(10) UNSIGNED NOT NULL,
  `input_desc` varchar(256) NOT NULL,
  `output_desc` varchar(256) NOT NULL,
  `output_supply_key` int(10) UNSIGNED NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `settlement` ADD PRIMARY KEY (`id`);
ALTER TABLE `settlement` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `settlement` (
  `label`, `slug`, `category_id`,
  `is_incorporated`, `is_food`, `is_material`, `is_energy`, `is_cash_crop`,
  `is_allowed_on_fertile`, `is_allowed_on_coastal`, `is_allowed_on_barren`, `is_allowed_on_mountain`, `is_allowed_on_tundra`,
  `base_population`, `defense_bonus`, `input_desc`, `output_desc`, `output_supply_key`) VALUES
('Unclaimed', 'unclaimed', 1,
  FALSE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  0, 1, '', '', NULL
),
('Uninhabited', 'uninhabited', 1,
  FALSE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  0, 1, '', '', NULL
),
('Town', 'town', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  100, 2, '1 energy, 1 food', 'Industry', NULL
),
('City', 'city', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  1000, 3, '5 energy, 3 food, 1 merchandise, 1 cash crop', 'Industry', NULL
),
('Metro', 'metro', 1,
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10000, 4, '15 energy, 10 food, 5 merchandise, 3 cash crop', 'Industry', NULL
),
('Grain', 'grain', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'grain', 8
),
('Fruit', 'fruit', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'fruit', 9
),
('Vegetables', 'vegetables', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'vegetables', 10
),
('Livestock', 'livestock', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'livestock', 11
),
('Fish', 'fish', 2,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'fish', 12
),
('Timber', 'timber', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'timber', 5
),
('Fiber', 'fiber', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'fiber', 6
),
('Ore', 'ore', 3,
  FALSE, FALSE, TRUE, FALSE, FALSE,
  FALSE, FALSE, TRUE, TRUE, FALSE,
  10, 1, '', 'ore', 7
),
('Biofuel', 'biofuel', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'biofuel', 14
),
('Solar', 'solar', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  10, 1, '', 'energy', 13
),
('Wind', 'wind', 4,
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  10, 1, '', 'energy', 13
),
('Coffee', 'coffee', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'coffee', 23
),
('Tea', 'tea', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'tea', 24
),
('Cannabis', 'cannabis', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'cannabis', 25
),
('Alcohol', 'alcohol', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'alcohol', 26
),
('Tobacco', 'tobacco', 5,
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'tobacco', 27
);

CREATE TABLE `industry` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `input_slug` varchar(256) NOT NULL,
  `input_desc` varchar(256) NOT NULL,
  `output_desc` varchar(256) NOT NULL,
  `output_supply_key` int(10) UNSIGNED NULL,
  `input_supply_amount` int(10) UNSIGNED NULL,
  `output_supply_amount` int(10) UNSIGNED NULL,
  `minimum_settlement_size` int(10) UNSIGNED NULL, -- town, city, metro
  `required_terrain_key` int(10) UNSIGNED NULL,
  `gdp` int(10) UNSIGNED NULL,
  `is_stackable` int(1) UNSIGNED NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `industry` ADD PRIMARY KEY (`id`);
ALTER TABLE `industry` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `industry` (
  `id`, `category_id`, `label`, `slug`, `input_slug`, `output_desc`, `minimum_settlement_size`, `required_terrain_key`,
  `output_supply_key`, `input_supply_amount`, `output_supply_amount`, `gdp`, `is_stackable`, `meta`) VALUES
-- government
(1, 1, 'Capitol', 'capitol', 'cash', 'support', NULL, NULL,
  null, 10, 25, 10, FALSE, 'Can spawn units, add political support, creates corruption'
),
(2, 1, 'Federal', 'federal', 'cash', 'support', NULL, NULL,
  null, 10, 10, 5, FALSE, 'Add political support, creates corruption'
),
(3, 1, 'Base', 'base', 'cash', '', NULL, NULL,
  null, 10, 1, 3, TRUE, 'Can spawn units'
),
-- merchandise
(4, 2, 'Manufacturing', 'manufacturing', 'ore', 'merchandise', NULL, NULL,
  null, 1, 1, 4, TRUE, ''
),
(5, 2, 'Timber', 'timber', 'timber', 'merchandise', NULL, NULL,
  null, 1, 1, 6, TRUE, ''
),
(6, 2, 'Textile', 'textile', 'fiber', 'merchandise', NULL, NULL,
  null, 1, 1, 6, TRUE, ''
),
-- energy
(7, 3, 'Biofuel', 'biofuel', 'biofuel', 'energy', NULL, NULL,
  null, 1, 2, 1, TRUE, ''
),
(8, 3, 'Coal', 'coal', 'coal', 'energy', NULL, NULL,
  null, 1, 2, 1, TRUE, ''
),
(9, 3, 'Gas', 'gas', 'gas', 'energy', NULL, NULL,
  null, 1, 3, 2, TRUE, ''
),
(10, 3, 'Petroleum', 'petroleum', 'oil', 'energy', NULL, NULL,
  null, 1, 6, 5, TRUE, ''
),
(11, 3, 'Nuclear', 'nuclear', 'uranium', 'energy', NULL, NULL,
  null, 1, 8, 5, TRUE, ''
),
-- light industry
(12, 4, 'Chemicals', 'chemicals', 'energy', 'chemicals', NULL, NULL,
  null, 1, 3, 5, TRUE, ''
),
(13, 4, 'Steel', 'steel', 'iron', 'steel', NULL, NULL,
  null, 1, 3, 3, TRUE, ''
),
(14, 4, 'Electronics', 'electronics', 'copper', 'electronics', NULL, NULL,
  null, 1, 3, 10, TRUE, ''
),
-- heavy industry
(15, 5, 'Shipping Port', 'port', 'steel', 'port', 2, 5,
  null, 1, 1, 50, FALSE, 'Must be a coastal tile. Must be size at least city. Having a port doubles GDP'
),
(16, 5, 'Machinery', 'machinery', 'zinc,steel,electronics,software', 'machinery', NULL, NULL,
  null, 1, 3, 25, FALSE, ''
),
(17, 5, 'Automotive', 'automotive', 'city,aluminum,steel,electronics,chemicals,engineering', 'automotive', 2, NULL,
  null, 1, 3, 50, FALSE, ''
),
(18, 5, 'Aerospace', 'aerospace', 'city,aluminum,nickle,steel,electronics,chemicals,engineering,software', 'aerospace', 2, NULL,
  null, 1, 3, 100, FALSE, ''
),
-- tourism
(19, 6, 'Leisure', 'leisure', '', '', NULL, 5,
  null, 1, 1, 10, FALSE, 'tile must be coastal'
),
(20, 6, 'Resort', 'resort', '', '', NULL, 3,
  null, 1, 1, 5, FALSE, 'tile must be mountain'
),
(21, 6, 'Gambling', 'gambling', 'support', '', 2, NULL,
  null, 5, 1, 5, FALSE, ''
),
-- knowledge/quaternary
(22, 7, 'University', 'university', 'cash', 'education', NULL, NULL,
  null, 5, 1, 3, FALSE, ''
),
(23, 7, 'Software', 'software', 'education', 'software', 2, NULL,
  null, 1, 3, 8, FALSE, 'Must be a city'
),
(24, 7, 'Healthcare', 'healthcare', 'city,education', 'support', 2, NULL,
  null, 1, 10, 6, FALSE, 'Also increases population by 25%'
),
-- metro
(25, 8, 'Financial & Banking', 'financial_banking', 'metro', '', 3, NULL,
  null, 1, 1, 500, FALSE, ''
),
(26, 8, 'Entertainment & Media', 'entertainment_media', 'metro', 'support', 3, NULL,
  null, 1, 20, 100, FALSE, ''
),
(27, 8, 'Engineering & Design', 'engineering_design', 'metro', 'engineering', 3, NULL,
  null, 1, 3, 200, FALSE, ''
);

-- 
-- 
-- 

CREATE TABLE `user` (
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

CREATE TABLE `chat` (
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

CREATE TABLE `analytics` (
  `id` int(10) UNSIGNED NOT NULL,
  `marketing_slug` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `analytics` ADD PRIMARY KEY (`id`);
ALTER TABLE `analytics` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `ip_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(64) NOT NULL,
  `request` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `ip_request` ADD PRIMARY KEY (`id`);
ALTER TABLE `ip_request` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
