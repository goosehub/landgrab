TRUNCATE `ip_request`;
-- TRUNCATE `user`;
-- TRUNCATE `account`;

UPDATE `account` 
SET `tutorial`= 0, `government`= 0, `tax_rate`= 0, `military_budget`= 0, `entitlements_budget`= 0, `war_weariness`= 0
WHERE 1;

UPDATE `land` 
SET `capitol`= 0, `account_key`= 0, `land_name`= '', `content`= '', `color`= '#000000', `land_type`= 1, `modified`= CURRENT_TIMESTAMP
WHERE 1;

DELETE FROM `land_modifier` 
WHERE 1;

DELETE FROM `land_modifier` WHERE `modify_effect_key` > 10;
DELETE FROM `land_modifier` WHERE `modify_effect_key` = 6;

UPDATE `account` 
SET `war_weariness`= 0
WHERE 1;

TRUNCATE `land_modifier`;

SELECT
    *, COUNT(`ip`) AS `value_occurrence`
    FROM     `user`
    GROUP BY `ip`
    ORDER BY `value_occurrence` DESC
    LIMIT    10;