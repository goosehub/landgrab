-- Most common land modifications
SELECT me.name, COUNT(*) AS count
FROM  `land_modifier` as lm
LEFT JOIN `modify_effect` as me
	on `modify_effect_key`  = me.`id`
WHERE lm.created >= ( CURDATE() - INTERVAL 3 DAY )
GROUP BY `modify_effect_key`
ORDER BY count DESC 

-- Most common government
SELECT `government` , COUNT(*) AS count
FROM  `account`
WHERE `tutorial` > 4
AND last_load >= ( CURDATE() - INTERVAL 3 DAY )
GROUP BY `government`
ORDER BY count DESC 

-- Most common tax_rate
SELECT `tax_rate` , COUNT(*) AS count
FROM  `account`
WHERE `tutorial` > 4
AND last_load >= ( CURDATE() - INTERVAL 3 DAY )
GROUP BY `tax_rate`
ORDER BY count DESC 

-- Most common military_budget
SELECT `military_budget` , COUNT(*) AS count
FROM  `account`
WHERE `tutorial` > 4
AND last_load >= ( CURDATE() - INTERVAL 3 DAY )
GROUP BY `military_budget`
ORDER BY count DESC 

-- Most common entitlements_budget
SELECT `entitlements_budget` , COUNT(*) AS count
FROM  `account`
WHERE `tutorial` > 4
AND last_load >= ( CURDATE() - INTERVAL 3 DAY )
GROUP BY `entitlements_budget`
ORDER BY count DESC 

-- Most common weariness
SELECT `weariness` , COUNT(*) AS count
FROM  `account`
WHERE `tutorial` > 4
AND last_load >= ( CURDATE() - INTERVAL 3 DAY )
GROUP BY `weariness`
ORDER BY count DESC 

-- Land modifiers that are not land types
SELECT *
FROM `land_modifier`
WHERE modify_effect_key NOT IN (1, 2, 3, 4, 5, 6)
ORDER BY `created` DESC

-- ab test
SELECT COUNT(*), `ab_test`
FROM `user`
GROUP BY `ab_test`

-- Get user
SELECT a.*, u.* FROM `account` as a
LEFT JOIN
	`user` as u on `user_key` = u.`id`
WHERE `username` = 'foobar';