-- Most common land modifications
SELECT * , COUNT( * ) AS count
FROM  `land_modifier` 
GROUP BY modify_effect_key
ORDER BY count DESC 