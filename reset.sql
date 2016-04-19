TRUNCATE `transaction_log`;
-- TRUNCATE `ip_request`;

UPDATE `land` 
SET `claimed`= 0,`account_key`= 0,`land_name`= '',`price`= 0,`lease_price`= 0,`lease_duration` = 0,`last_lease_end` = 1460000000,`content`= '',`default_content`= '',`primary_color`= '#000000',`secondary_color`= '#000000' 
WHERE `claimed` = 1;
-- AND `world_key` = ?;

UPDATE `account`
SET `cash` = 1000000
WHERE 1;

UPDATE `world`
SET `land_rebate` = 0, `bank` = 0
WHERE 1;