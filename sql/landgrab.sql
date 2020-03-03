-- Database sql
DROP TABLE IF EXISTS world;
DROP TABLE IF EXISTS tile;
DROP TABLE IF EXISTS unit_type;
DROP TABLE IF EXISTS account;
DROP TABLE IF EXISTS trade_request;
DROP TABLE IF EXISTS supply_account_lookup;
DROP TABLE IF EXISTS supply_account_trade_lookup;
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
  `army_unit_key` int(10) UNSIGNED NULL, -- Infantry, Guerrilla, Commandos, none as null
  `army_unit_owner_key` int(10) UNSIGNED NULL,
  `is_capitol` int(1) NOT NULL,
  `tile_name` varchar(512) NULL,
  `tile_desc` varchar(1024) NULL,
  `color` varchar(8) NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `tile` ADD PRIMARY KEY (`id`);
ALTER TABLE `tile` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `unit_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(126) NOT NULL,
  `strength_against_key` int(10) UNSIGNED NOT NULL,
  `cost_base` int(4) NOT NULL,
  `can_take_tiles` int(1) NOT NULL,
  `can_take_towns` int(1) NOT NULL,
  `can_take_cities` int(1) NOT NULL,
  `can_take_metros` int(1) NOT NULL,
  `desc` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `unit_type` ADD PRIMARY KEY (`id`);
ALTER TABLE `unit_type` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `unit_type` (`id`, `slug`, `strength_against_key`, `cost_base`, `can_take_tiles`, `can_take_towns`, `can_take_cities`, `can_take_metros`, `desc`) VALUES
(1, 'Infantry', 3, 100, TRUE, TRUE, TRUE, TRUE, ''),
(2, 'Guerrilla', 1, 50, TRUE, TRUE, FALSE, FALSE, ''),
(3, 'Commandos', 2, 200, TRUE, FALSE, FALSE, FALSE, '');

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
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `account` ADD PRIMARY KEY (`id`);
ALTER TABLE `account` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `trade_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_account_key` int(10) UNSIGNED NOT NULL,
  `requested_account_key` int(10) UNSIGNED NOT NULL,
  `status` int(1) UNSIGNED NULL,
  `message` varchar(256) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `trade_request` ADD PRIMARY KEY (`id`);
ALTER TABLE `trade_request` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

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
  `agreement_key` int(10) UNSIGNED NOT NULL, -- War, Peace, Passage
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `trade_request` ADD PRIMARY KEY (`id`);
ALTER TABLE `trade_request` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

CREATE TABLE `agreement_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `a_account_key` int(10) UNSIGNED NOT NULL,
  `b_account_key` int(10) UNSIGNED NOT NULL,
  `agreement_key` int(10) UNSIGNED NOT NULL, -- War, Peace, Passage
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
  `can_trade` int(1) UNSIGNED NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `supply` ADD PRIMARY KEY (`id`);
ALTER TABLE `supply` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `supply` (`label`, `slug`, `can_trade`, `meta`) VALUES
-- core
('cash', 'cash', TRUE, ''),
('support', 'support', FALSE, ''),
('population', 'population', FALSE, ''),
('land_tiles', 'land_tiles', FALSE, ''),
('ocean_tiles', 'ocean_tiles', FALSE, ''),
-- food
('grain', 'grain', TRUE, ''),
('fruit', 'fruit', TRUE, ''),
('vegetable', 'vegetable', TRUE, ''),
('livestock', 'livestock', TRUE, ''),
('fish', 'fish', TRUE, ''),
-- cash_crops
('coffee', 'coffee', TRUE, ''), -- cash_crop
('tea', 'tea', TRUE, ''), -- cash_crop
('cannabis', 'cannabis', TRUE, ''), -- cash_crop
('alcohol', 'alcohol', TRUE, ''), -- cash_crop
('tobacco', 'tobacco', TRUE, ''), -- cash_crop
-- harvested supplies
('timber', 'timber', TRUE, ''),
('fiber', 'fiber', TRUE, ''),
('ore', 'ore', TRUE, ''),
-- energy
('biofuel', 'biofuel', TRUE, ''),
('solar', 'solar', FALSE, ''),
('wind', 'wind', FALSE, ''),
('coal', 'coal', TRUE, ''),
('gas', 'gas', TRUE, ''),
('oil', 'oil', TRUE, ''),
('uranium', 'uranium', TRUE, ''),
-- valuables
('silver', 'silver', TRUE, ''), -- cash_crop
('gold', 'gold', TRUE, ''), -- cash_crop
('platinum', 'platinum', TRUE, ''), -- cash_crop
('gemstones', 'gemstones', TRUE, ''), -- cash_crop
-- metals
('iron', 'iron', TRUE, ''),
('copper', 'copper', TRUE, ''),
('zinc', 'zinc', TRUE, ''),
('aluminum', 'aluminum', TRUE, ''),
('nickle', 'nickle', TRUE, ''),
-- light industry
('merchandise', 'merchandise', TRUE, ''),
('energy', 'energy', TRUE, ''),
('chemicals', 'chemicals', TRUE, ''),
('steel', 'steel', TRUE, ''),
('electronics', 'electronics', TRUE, ''),
-- heavy industry
('port', 'port', TRUE, ''),
('machinery', 'machinery', TRUE, ''),
('automotive', 'automotive', TRUE, ''),
('aerospace', 'aerospace', TRUE, ''),
-- service industry
('education', 'education', FALSE, ''),
('it', 'it', TRUE, ''),
('healthcare', 'healthcare', TRUE, ''),
('engineering', 'engineering', TRUE, '');

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
  10, TRUE, TRUE, TRUE, FALSE
),
(
  7, 'Oil', 'oil',
  FALSE, TRUE, FALSE,
  10, TRUE, FALSE, TRUE, TRUE
),
(
  8, 'Uranium', 'uranium',
  FALSE, TRUE, FALSE,
  5, TRUE, TRUE, TRUE, FALSE
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
  `is_settlement` int(1) NOT NULL,
  `is_food` int(1) NOT NULL,
  `is_material` int(1) NOT NULL,
  `is_energy` int(1) NOT NULL,
  `is_cash_crop` int(1) NOT NULL,
  `is_allowed_on_fertile` int(1) NOT NULL,
  `is_allowed_on_coastal` int(1) NOT NULL,
  `is_allowed_on_barren` int(1) NOT NULL,
  `is_allowed_on_mountain` int(1) NOT NULL,
  `base_population` int(10) UNSIGNED NOT NULL,
  `defense_bonus` int(10) UNSIGNED NOT NULL,
  `input_desc` varchar(256) NOT NULL,
  `output_desc` varchar(256) NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `settlement` ADD PRIMARY KEY (`id`);
ALTER TABLE `settlement` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `settlement` (
  `label`, `slug`,
  `is_settlement`, `is_food`, `is_material`, `is_energy`, `is_cash_crop`,
  `is_allowed_on_fertile`, `is_allowed_on_coastal`, `is_allowed_on_barren`, `is_allowed_on_mountain`, `is_allowed_on_tundra`,
  `base_population`, `defense_bonus`, `input_desc`, `output_desc`) VALUES
-- settlement
-- foods, cash crops, merch, energy, small settlements
('unclaimed', 'unclaimed',
  FALSE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  100, 1, '', ''
),
('default', 'default',
  FALSE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  100, 1, '', ''
),
('town', 'town',
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, TRUE,
  100, 2, '1 food', ''),
('city', 'city',
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  1000, 3, '3 kinds of food, 3 kinds, 3 energy, 1 merchandise, 1 cash crop', ''),
('metro', 'metro',
  TRUE, FALSE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10000, 4, '5 kinds of food, 10 energy, 5 merchandise, 3 kinds of cash crop', ''),
-- food
('grain', 'grain',
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'grain'
),
('fruit', 'fruit',
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'fruit'
),
('vegetable', 'vegetable',
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'vegetable'
),
('livestock', 'livestock',
  FALSE, TRUE, FALSE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'livestock'
),
('fish', 'fish',
  FALSE, TRUE, FALSE, FALSE, FALSE,
  FALSE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'fish'
),
-- materials
('timber', 'timber',
  FALSE, FALSE, TRUE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'timber'
),
('fiber', 'fiber',
  FALSE, FALSE, TRUE, FALSE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'fiber'
),
('ore', 'ore',
  FALSE, FALSE, TRUE, FALSE, FALSE,
  FALSE, FALSE, TRUE, TRUE, FALSE,
  10, 1, '', 'ore'
),
-- energy
('biofuel', 'biofuel',
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'biofuel'
),
('solar', 'solar',
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  10, 1, '', 'solar'
),
('wind', 'wind',
  FALSE, FALSE, FALSE, TRUE, FALSE,
  TRUE, TRUE, TRUE, TRUE, FALSE,
  10, 1, '', 'wind'
),
-- cash_crops
('coffee', 'coffee',
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'coffee'
),
('tea', 'tea',
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'tea'
),
('cannabis', 'cannabis',
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'cannabis'
),
('alcohol', 'alcohol',
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'alcohol'
),
('tobacco', 'tobacco',
  FALSE, FALSE, FALSE, FALSE, TRUE,
  TRUE, TRUE, FALSE, FALSE, FALSE,
  10, 1, '', 'tobacco'
);

CREATE TABLE `industry` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL, -- Special, CashCrop, Material, Advanced, Service, Knowledge, Metro
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `input_slug` varchar(256) NOT NULL,
  `input_desc` varchar(256) NOT NULL,
  `output_desc` varchar(256) NOT NULL,
  `output_supply_key` int(10) UNSIGNED NULL,
  `input_supply_amount` int(10) UNSIGNED NULL,
  `output_supply_amount` int(10) UNSIGNED NULL,
  `gdp` int(10) UNSIGNED NULL,
  `is_stackable` int(1) UNSIGNED NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `industry` ADD PRIMARY KEY (`id`);
ALTER TABLE `industry` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

INSERT INTO `industry` (
  `category_id`, `label`, `slug`, `input_slug`, `output_desc`,
  `output_supply_key`, `input_supply_amount`, `output_supply_amount`, `gdp`, `is_stackable`, `meta`) VALUES
-- federal
(1, 'capital', 'capital', 'cash', 'support',
  null, 10, 25, 1, FALSE, 'Acts as federal and base.'
),
(1, 'federal', 'federal', 'cash', 'support',
  null, 10, 10, 1, FALSE, 'Adds political support, but also increases corruption'
),
(1, 'base', 'base', 'cash', '',
  null, 10, 1, 1, TRUE, 'Allows units to be spawned in and flown in to this tile'
),
-- merchandise
(2, 'manufacturing', 'manufacturing', 'ore', 'merchandise',
  null, 1, 1, 1, TRUE, ''
),
(2, 'timber', 'timber', 'timber', 'merchandise',
  null, 1, 1, 1, TRUE, ''
),
(2, 'textile', 'textile', 'fiber', 'merchandise',
  null, 1, 1, 1, TRUE, ''
),
-- energy
(2, 'biofuel', 'biofuel', 'biofuel', 'energy',
  null, 1, 2, 1, TRUE, ''
),
(2, 'coal', 'coal', 'coal', 'energy',
  null, 1, 3, 1, TRUE, ''
),
(2, 'gas', 'gas', 'gas', 'energy',
  null, 1, 3, 1, TRUE, ''
),
(2, 'petroleum', 'petroleum', 'oil', 'energy',
  null, 1, 5, 1, TRUE, ''
),
(2, 'nuclear', 'nuclear', 'uranium', 'energy',
  null, 1, 10, 1, TRUE, ''
),
-- other light industry
(2, 'chemicals', 'chemicals', 'energy', 'chemicals',
  null, 1, 1, 1, TRUE, ''
),
(2, 'steel', 'steel', 'iron', 'steel',
  null, 1, 1, 1, TRUE, ''
),
(2, 'electronics', 'electronics', 'copper', 'electronics',
  null, 1, 1, 1, TRUE, ''
),
-- heavy industry
(3, 'port', 'port', 'steel', 'port',
  null, 1, 1, 1, FALSE, 'Must be a coastal tile. Must be size at least city. Having a port doubles GDP'
),
(3, 'machinery', 'machinery', 'zinc,steel,electronics,it', 'machinery',
  null, 1, 1, 1, FALSE, ''
),
(3, 'automotive', 'automotive', 'city,aluminum,steel,electronics,chemicals,engineering', 'automotive',
  null, 1, 1, 1, FALSE, ''
),
(3, 'aerospace', 'aerospace', 'city,aluminum,nickle,steel,electronics,chemicals,engineering,it', 'aerospace',
  null, 1, 1, 1, FALSE, ''
),
-- tourism
(4, 'leisure', 'leisure', '', '',
  null, 1, 1, 1, FALSE, 'tile must be coastal'
),
(4, 'resort', 'resort', '', '',
  null, 1, 1, 1, FALSE, 'tile must be mountain'
),
(4, 'gambling', 'gambling', 'support', '',
  null, 5, 1, 1, FALSE, ''
),
-- knowledge/quaternary
(5, 'university', 'university', 'cash', 'education',
  null, 5, 1, 1, FALSE, ''
),
(5, 'it', 'it', 'education', 'it',
  null, 1, 1, 1, FALSE, 'Must be a city'
),
(5, 'healthcare', 'healthcare', 'city,education', 'support',
  null, 1, 10, 1, FALSE, 'Also increases population by 25%'
),
-- metro
(6, 'financial_banking', 'financial_banking', 'metro', '',
  null, 1, 1, 1, FALSE, ''
),
(6, 'entertainment_media', 'entertainment_media', 'metro', 'support',
  null, 1, 20, 1, FALSE, ''
),
(6, 'engineering_design', 'engineering_design', 'metro', 'engineering',
  null, 1, 1, 1, FALSE, ''
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
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
