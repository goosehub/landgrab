TRUNCATE `ip_request`;
-- TRUNCATE `user`;
-- TRUNCATE `account`;

UPDATE `land` 
SET `capitol`= 0, `account_key`= 0, `land_name`= '', `content`= '', `color`= '#000000',
WHERE 1;

DELETE FROM `land_modifier` WHERE `modify_effect_key` > 10;
DELETE FROM `land_modifier` WHERE `modify_effect_key` = 6;

UPDATE `account` 
SET `war_weariness`= 0
WHERE 1;

TRUNCATE `land_modifier`;