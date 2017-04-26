-- Most common land modifications
SELECT me.name , COUNT(*) AS count
FROM  `land_modifier` as lm
LEFT JOIN `modify_effect` as me
	on `modify_effect_key`  = me.`id`
GROUP BY `modify_effect_key`
ORDER BY count DESC 

-- Most common governments
SELECT `government` , COUNT(*) AS count
FROM  `account`
WHERE `tutorial` > 4
GROUP BY `government`
ORDER BY count DESC 