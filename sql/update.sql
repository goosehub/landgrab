CREATE TABLE `world` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(126) NOT NULL,
  `tile_size` int(4) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tile` (
  `id` int(10) UNSIGNED NOT NULL,
  `lat` int(4) NOT NULL,
  `lng` int(4) NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `owner_account_key` int(10) UNSIGNED NOT NULL,
  `terrian_key` int(10) UNSIGNED NOT NULL,
  `resource_key` int(10) UNSIGNED NOT NULL,
  `settlement_key` int(10) UNSIGNED NOT NULL,
  `industry_key` int(10) UNSIGNED NOT NULL,
  `army_unit_key` int(10) UNSIGNED NULL, -- Infantry, Guerrilla, Commandos, none as null
  `army_unit_owner_key` int(10) UNSIGNED NOT NULL,
  `is_capitol` int(1) NOT NULL,
  `tile_name` varchar(512) NOT NULL,
  `tile_desc` varchar(1024) NOT NULL,
  `color` varchar(8) NOT NULL,
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `unit_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(126) NOT NULL,
  `strength_against_key` int(10) UNSIGNED NOT NULL,
  `cost_base` int(4) NOT NULL,
  `support_base` int(4) NOT NULL,
  `can_take_tiles` int(1) NOT NULL,
  `can_take_towns` int(1) NOT NULL,
  `can_take_cities` int(1) NOT NULL,
  `can_take_metros` int(1) NOT NULL
  `desc` varchar(256) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `unit_type` (`id`, `slug`, `strength_against_key`, `cost_base`, `support_base`, `can_take_tiles`, `can_take_towns`, `can_take_cities`, `can_take_metros`, `desc`) VALUES
(1, 'Infantry', 3, 100, 3, TRUE, TRUE, TRUE, TRUE),
(2, 'Guerrilla', 1, 50, 1, TRUE, TRUE, FALSE, FALSE, ''),
(3, 'Commandos', 2, 200, 2, TRUE, FALSE, FALSE, FALSE, '');

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
  `last_government_switch` timestamp NOT NULL,
  `tax_rate` int(10) UNSIGNED NOT NULL,
  -- meta
  `last_load` varchar(32) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `trade_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_account_key` int(10) UNSIGNED NOT NULL,
  `requested_account_key` int(10) UNSIGNED NOT NULL,
  `status` int(1) UNSIGNED NULL,
  `message` varchar(256) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `supply_account_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `supply_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `supply_account_trade_lookup` (
  `id` int(10) UNSIGNED NOT NULL,
  `supply_account_lookup_key` int(10) UNSIGNED NOT NULL,
  `trade_key` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `supply` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `can_trade` int(1) UNSIGNED NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
('coffee', 'coffee', TRUE, TRUE, 1, ''),
('tea', 'tea', TRUE, TRUE, 1, ''),
('cannabis', 'cannabis', TRUE, TRUE, 1, ''),
('alcohol', 'alcohol', TRUE, TRUE, 1, ''),
('tobacco', 'tobacco', TRUE, TRUE, 1, ''),
-- harvested supplies
('timber', 'timber', TRUE, ''),
('fiber', 'fiber', TRUE, ''),
('ore', 'ore', TRUE, ''),
-- harvested energy
('biofuel', 'biofuel', TRUE, ''),
('solar', 'solar', FALSE, ''),
('wind', 'wind', FALSE, ''),
-- primary industries
('coal', 'coal', TRUE, ''),
('gas', 'gas', TRUE, ''),
('oil', 'oil', TRUE, ''),
('uranium', 'uranium', TRUE, ''),
('silver', 'silver', TRUE, TRUE, 1, ''),
('gold', 'gold', TRUE, TRUE, 1, ''),
('platinum', 'platinum', TRUE, TRUE, 1, ''),
('gemstones', 'gemstones', TRUE, TRUE, 1, ''),
('iron', 'iron', TRUE, ''),
('copper', 'copper', TRUE, ''),
('zinc', 'zinc', TRUE, ''),
('aluminum', 'aluminum', TRUE, ''),
('nickle', 'nickle', TRUE, ''),
-- secondary industries
('merchandise', 'merchandise', TRUE, ''),
('energy', 'energy', TRUE, ''),
('textile', 'textile', TRUE, ''),
('chemicals', 'chemicals', TRUE, ''),
('steel', 'steel', TRUE, ''),
('electronics', 'electronics', TRUE, ''),
-- tertiary industries
('construction', 'construction', TRUE, ''),
('telecommunications', 'telecommunications', TRUE, ''),
('port', 'port', TRUE, ''),
('machinery', 'machinery', TRUE, ''),
('automotive', 'automotive', TRUE, ''),
('aerospace', 'aerospace', TRUE, ''),
-- quaternary industries
('education', 'education', TRUE, ''),
('it', 'it', TRUE, ''),
('healthcare', 'healthcare', TRUE, ''),
('engineering', 'engineering', TRUE, '');

CREATE TABLE `terrian` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `terrian` (`label`, `slug`, `meta`) VALUES
('Fertile', 'fertile', 1),
('Barren', 'barren', 1),
('Mountain', 'mountain', 1),
('Tundra', 'tundra', 1),
('Coastal', 'coastal', 1),
('Ocean', 'ocean', 1);

CREATE TABLE `resource` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `frequency_per_world` int(10) UNSIGNED NOT NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `resource` (`label`, `slug`, `frequency_per_world`) VALUES
-- Valuables
('Silver', 'silver', 15),
('Gold', 'gold', 5),
('Platinum', 'platinum', 3),
('Gemstones', 'gemstones', 2),
-- Energy
('Coal', 'coal', 25),
('Gas', 'gas', 15),
('Oil', 'oil', 10),
('Uranium', 'uranium', 5),
-- Metals
('Iron', 'iron', 25),
('Copper', 'copper', 7),
('Zinc', 'zinc', 6),
('Aluminum', 'aluminum', 5),
('Nickle', 'nickle', 4);

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

INSERT INTO `settlement` (`label`, `slug`, `is_settlement`, `is_food`, `is_material`, `is_energy`, `is_cash_crop`, `is_allowed_on_fertile`, `is_allowed_on_coastal`, `is_allowed_on_barren`, `is_allowed_on_mountain`, `base_population`, `defense_bonus`, `input_desc`, `output_desc`) VALUES
-- settlement
-- foods, cash crops, merch, energy, small settlements
('unclaimed', 'unclaimed', FALSE, FALSE, FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, TRUE, 100, 1, '', ''),
('default', 'default', FALSE, FALSE, FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, TRUE, 100, 1, '', ''),
('town', 'town', TRUE, FALSE, FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, TRUE, 100, 2, '3 kinds of food', ''),
('city', 'city', TRUE, FALSE, FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, TRUE, 1000, 3, '5 kinds of food, 2 kinds of cash crops, 10 energy, 5 merchandise, 5 towns', ''),
('metro', 'metro', TRUE, FALSE, FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, TRUE, 10000, 4, '5 kinds of food, 5 kinds of cash crops, 100 energy, 10 merchandise, contrusction, 5 cities', ''),
-- food
('grain', 'grain', FALSE, TRUE, FALSE, FALSE, FALSE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('fruit', 'fruit', FALSE, TRUE, FALSE, FALSE, FALSE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('vegetable', 'vegetable', FALSE, TRUE, FALSE, FALSE, FALSE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('livestock', 'livestock', FALSE, TRUE, FALSE, FALSE, FALSE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('fish', 'fish', FALSE, TRUE, FALSE, FALSE, FALSE, FALSE, TRUE, FALSE, FALSE, 10, 1, '', ''),
-- materials
('timber', 'timber', FALSE, FALSE, TRUE, FALSE, FALSE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('fiber', 'fiber', FALSE, FALSE, TRUE, FALSE, FALSE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('ore', 'ore', FALSE, FALSE, TRUE, FALSE, FALSE, FALSE, FALSE, TRUE, TRUE, 10, 1, '', ''),
-- energy
('biofuel', 'biofuel', FALSE, FALSE, FALSE, TRUE, FALSE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('solar', 'solar', FALSE, FALSE, FALSE, TRUE, FALSE, TRUE, TRUE, TRUE, TRUE, 10, 1, '', ''),
('wind', 'wind', FALSE, FALSE, FALSE, TRUE, FALSE, TRUE, TRUE, TRUE, TRUE, 10, 1, '', ''),
-- cash_crops
('coffee', 'coffee', FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('tea', 'tea', FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('cannabis', 'cannabis', FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('alcohol', 'alcohol', FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', ''),
('tobacco', 'tobacco', FALSE, FALSE, FALSE, TRUE, TRUE, TRUE, FALSE, FALSE, 10, 1, '', '');

CREATE TABLE `industry` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL, -- Special, CashCrop, Material, Advanced, Service, Knowledge, Metro
  `label` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `input_slug` varchar(256) NOT NULL,
  `input_desc` varchar(256) NOT NULL,
  `output_desc` varchar(256) NOT NULL,
  `output_supply_key` int(10) UNSIGNED NULL,
  `output_supply_amount` int(10) UNSIGNED NULL,
  `gdp` int(10) UNSIGNED NULL,
  `meta` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `industry` (`category_id`, `label`, `slug`, `input_slug`, `output_desc`, `output_supply_key`, `output_supply_amount`, `gdp`, `meta`) VALUES
-- federal
(1, 'capital', 'capital', 'cash', 'support,spawn', null, 25, 1, 'Acts as federal and base.'),
(1, 'federal', 'federal', 'cash', 'support', null, 10, 1, ''),
(1, 'base', 'base', 'cash', 'spawn', null, 1, 1, 'Allows units to be spawned in and flown to this tile'),
-- raw/secondary
(1, 'manufacturing', 'manufacturing', 'ore|timber|fiber', 'merchandise', null, 1, 1, ''),
(1, 'energy', 'energy', 'coal|gas|oil|nuclear|biofuel', 'energy', null, 1, 1, ''),
(1, 'chemicals', 'chemicals', 'coal|gas|oil', 'chemicals', null, 1, 1, ''),
(1, 'steel', 'steel', 'iron', 'steel', null, 1, 1, ''),
(1, 'electronics', 'electronics', 'copper', 'electronics', null, 1, 1, ''),
-- advanced/tertiary
(1, 'construction', 'construction', 'steel', 'construction', null, 1, 1, ''),
(1, 'telecommunications', 'telecommunications', 'copper,it', 'telecommunications', null, 1, 1, ''),
(1, 'port', 'port', 'coast,city,steel', 'port', null, 1, 1, ''),
(1, 'machinery', 'machinery', 'zinc,steel,electronics,it', 'machinery', null, 1, 1, ''),
(1, 'automotive', 'automotive', 'city,aluminum,steel,chemicals,engineering,it', 'automotive', null, 1, 1, ''),
(1, 'aerospace', 'aerospace', 'city,aluminum,nickle,steel,chemicals,engineering,it', 'aerospace', null, 1, 1, ''),
-- tourism
(1, 'leisure', 'leisure', 'coastal', '', null, 1, 1, ''),
(1, 'resort', 'resort', 'mountain', '', null, 1, 1, ''),
(1, 'gambling', 'gambling', 'support', '', null, 1, 1, ''),
-- knowledge/quaternary
(1, 'university', 'university', 'cash', 'education', null, 1, 1, ''),
(1, 'it', 'it', 'city,education', 'it', null, 1, 1, ''),
(1, 'healthcare', 'healthcare', 'city,education', 'population', null, 1, 1, ''),
-- metro
(1, 'financial_banking', 'financial_banking', 'metro', 'cash', null, 1, 1, ''),
(1, 'entertainment_media', 'entertainment_media', 'metro', 'support', null, 1, 1, ''),
(1, 'engineering_design', 'engineering_design', 'metro', 'engineering', null, 1, 1, '');

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
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `chat` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_key` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `color` varchar(8) NOT NULL,
  `message` text NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `analytics` (
  `id` int(10) UNSIGNED NOT NULL,
  `marketing_slug` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `ip_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(64) NOT NULL,
  `request` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;