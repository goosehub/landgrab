TRUNCATE TABLE `trade_request`;
TRUNCATE TABLE `treaty_lookup`;
TRUNCATE TABLE `supply_industry_lookup`;
TRUNCATE TABLE `supply_account_trade_lookup`;

UPDATE `tile` SET
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
WHERE 1;

UPDATE `supply_account_lookup` SET
`amount` = 0
WHERE 1;

UPDATE `account` SET
`ideology` = 1,
`tax_rate` = 25,
`last_law_change` = NULL
WHERE 1;

--

TRUNCATE TABLE `supply_account_lookup`;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `account`;

-- TRUNCATE TABLE `world`;
-- TRUNCATE TABLE `tiles`;

-- TRUNCATE TABLE `unit_type`;
-- TRUNCATE TABLE `supply`;
-- TRUNCATE TABLE `terrain`;
-- TRUNCATE TABLE `resource`;
-- TRUNCATE TABLE `settlement`;
-- TRUNCATE TABLE `industry`;

-- TRUNCATE TABLE `chat`;

-- TRUNCATE TABLE `analytics`;
-- TRUNCATE TABLE `ip_request`;