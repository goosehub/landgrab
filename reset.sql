TRUNCATE `transaction_log`;
TRUNCATE `ip_request`;
-- TRUNCATE `user`;
-- TRUNCATE `account`;

UPDATE `land` 
SET `claimed`= 0, `account_key`= 0, `land_name`= '', `price`= 500, `lease_price`= 1, `lease_duration` = 10, `last_lease_end` = 1460000000,
`content`= '', `default_content`= '', `primary_color`= '#000000', `secondary_color`= '#000000' 
WHERE `claimed` = 1;

UPDATE `world`
SET `land_rebate` = 10, `claim_fee` = 500
WHERE 1;

UPDATE `account`
SET `cash` = 100000
WHERE 1;