-- Users currently on
SELECT * FROM `account`
WHERE `last_load` > DATE_SUB(NOW(), INTERVAL 3 MINUTE);

-- IPs currently on
SELECT * FROM `account`
LEFT JOIN user
	on account.user_key = user.id
WHERE `last_load` > DATE_SUB(NOW(), INTERVAL 3 MINUTE)
GROUP BY user.ip

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

-- ab test
SELECT COUNT(*), `ab_test`
FROM `user`
GROUP BY `ab_test`

-- Get user
SELECT a.*, u.* FROM `account` as a
LEFT JOIN
	`user` as u on `user_key` = u.`id`
WHERE `username` = 'foobar';