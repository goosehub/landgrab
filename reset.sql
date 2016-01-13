TRUNCATE `transaction_log`;

UPDATE `land` 
SET `claimed`= 0,`account_key`= 0,`land_name`= '',`price`= 0,`content`= '',`primary_color`= '#000000',`secondary_color`= '#000000' 
WHERE `claimed` = 1 AND `world_key` = 1;

UPDATE `account`
SET `cash` = 1000000
WHERE 1;

UPDATE `world`
SET `latest_rebate` = 0, `bank` = 0
WHERE 1;