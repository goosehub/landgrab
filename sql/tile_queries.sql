-- Count by terrain type
SELECT terrain_key, COUNT(id) FROM `tile` GROUP BY `terrain_key`;