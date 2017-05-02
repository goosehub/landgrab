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

-- Land modifiers that are not land types
SELECT *
FROM `land_modifier`
WHERE modify_effect_key NOT IN (1, 2, 3, 4, 5, 6)
ORDER BY `created` DESC