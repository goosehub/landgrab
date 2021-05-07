-- Users currently on
SELECT * FROM `account`
WHERE `last_load` > DATE_SUB(NOW(), INTERVAL 3 MINUTE);

-- IPs currently on
SELECT * FROM `account`
LEFT JOIN user
	on account.user_key = user.id
WHERE `last_load` > DATE_SUB(NOW(), INTERVAL 3 MINUTE)
GROUP BY user.ip

-- Most common power_structure
SELECT `power_structure` , COUNT(*) AS count
FROM  `account`
WHERE `tutorial` > 4
AND last_load >= ( CURDATE() - INTERVAL 3 DAY )
GROUP BY `power_structure`
ORDER BY count DESC 

-- Most common tax_rate
SELECT `tax_rate` , COUNT(*) AS count
FROM  `account`
WHERE `tutorial` > 4
AND last_load >= ( CURDATE() - INTERVAL 3 DAY )
GROUP BY `tax_rate`
ORDER BY count DESC 

-- Most common settlement
SELECT  COUNT(*) AS count, settlement.label
FROM  `tile`
LEFT JOIN `settlement`
	ON settlement.id = settlement_key
GROUP BY `settlement_key`
ORDER BY count DESC 

-- Most common industry
SELECT  COUNT(*) AS count, industry.label
FROM  `tile`
LEFT JOIN `industry`
	ON industry.id = industry_key
GROUP BY `industry_key`
ORDER BY count DESC

-- ab test
SELECT COUNT(*), `ab_test`
FROM `user`
GROUP BY `ab_test`

-- Get user
SELECT a.*, u.* FROM `account` as a
LEFT JOIN
	`user` as u on `user_key` = u.`id`
WHERE `username` = 'foobar';

-- Set cash and support
UPDATE `landgrab_new`.`supply_account_lookup` SET `amount` = '200' WHERE supply_key IN (1, 2);

-- 
-- Below is used to clear dead data, helps prevent deadlocks
-- 

-- Worlds by activity
SELECT 
world.id,
COUNT(tile.account_key), 
tile.modified 
FROM `world` 
LEFT JOIN `tile` 
    on world.id = tile.world_key 
GROUP BY world.id
ORDER BY COUNT(tile.account_key) DESC

-- Hard Delete All Worlds except for provided, does not include chat, use ids from above query
--DELETE FROM world WHERE id NOT IN (1);
--DELETE FROM tile WHERE world_key NOT IN (1);
--DELETE FROM account WHERE world_key NOT IN (1);

-- Clear dead supply account lookups
DELETE supply_account_lookup
FROM supply_account_lookup
LEFT JOIN `account`
on account_key = account.id
WHERE account.id IS NULL