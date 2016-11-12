TRUNCATE `ip_request`;
-- TRUNCATE `user`;
-- TRUNCATE `account`;

UPDATE `land` 
SET `claimed`= 0, `account_key`= 0, `land_name`= '', `content`= '', `color`= '#000000',
WHERE `claimed` = 1;

TRUNCATE `land_modifier`;