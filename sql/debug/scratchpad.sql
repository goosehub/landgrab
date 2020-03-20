
UPDATE supply_account_lookup AS sal
LEFT JOIN (
	SELECT SUM(tile_count * supply_industry_lookup.amount) as new_amount
	FROM supply_industry_lookup
    LEFT JOIN (
	    SELECT COUNT(*) as tile_count, industry_key, account_key
	    FROM tile
	    GROUP BY industry_key, account_key
    ) AS tile_by_industry_and_account ON supply_industry_lookup.industry_key = tile_by_industry_and_account.industry_key
	GROUP BY supply_industry_lookup.industry_key, supply_industry_lookup.supply_key, tile_by_industry_and_account.account_key
) AS sil ON sil.supply_key = sal.supply_key AND sal.account_key = sil.account_key
SET sal.amount = sal.amount - new_amount



-- steel minus 1, expected minus 3
UPDATE supply_account_lookup AS sal
LEFT JOIN (
	SELECT (tile_count * amount) as new_amount, supply_key, account_key, amount, COUNT(*) as sil_count
	FROM supply_industry_lookup
    LEFT JOIN (
	    SELECT industry_key, account_key, COUNT(*) as tile_count
	    FROM tile
	    GROUP BY industry_key, account_key
    ) AS tile_by_industry_and_account ON supply_industry_lookup.industry_key = tile_by_industry_and_account.industry_key
	GROUP BY supply_industry_lookup.industry_key, supply_industry_lookup.supply_key
) AS sil ON sil.supply_key = sal.supply_key AND sal.account_key = sil.account_key
SET sal.amount = sal.amount - new_amount






-- steel minus 2, expected minus 3
UPDATE supply_account_lookup AS sal
LEFT JOIN (
	SELECT industry_key, supply_key, amount
	FROM supply_industry_lookup
	GROUP BY industry_key, supply_key
) AS sil ON sil.supply_key = sal.supply_key
LEFT JOIN (
	SELECT industry_key, account_key, COUNT(*) as tile_count
	FROM tile
	GROUP BY industry_key, account_key
) AS tile_by_industry_and_account ON sal.account_key = tile_by_industry_and_account.account_key AND sil.industry_key = tile_by_industry_and_account.industry_key
SET sal.amount = sal.amount - (tile_by_industry_and_account.tile_count * sil.amount)









-- steel minus 4, expected minus 3
UPDATE supply_account_lookup AS sal
LEFT JOIN (
	SELECT industry_key, supply_key, amount
	FROM supply_industry_lookup
	GROUP BY industry_key, supply_key
) AS sil ON sil.supply_key = sal.supply_key
LEFT JOIN (
	SELECT industry_key, account_key, COUNT(*) as tile_count
	FROM tile
	GROUP BY industry_key, account_key
) AS tile_by_industry_and_account ON sal.account_key = tile_by_industry_and_account.account_key
SET sal.amount = sal.amount - (tile_by_industry_and_account.tile_count * sil.amount)









-- Sets to zero
UPDATE supply_account_lookup AS sal
LEFT JOIN (
	SELECT industry_key, supply_key, amount
	FROM supply_industry_lookup
	GROUP BY industry_key
) AS sil ON sil.supply_key = sal.supply_key
LEFT JOIN (
	SELECT industry_key, account_key, COUNT(*) as tile_count
	FROM tile
	GROUP BY industry_key, account_key
) AS tile_by_industry_and_account ON sil.industry_key = tile_by_industry_and_account.industry_key AND sal.account_key = tile_by_industry_and_account.account_key
SET sal.amount = sal.amount - (tile_by_industry_and_account.tile_count * sil.amount)









-- steel minus 1, expected minus 3
UPDATE supply_account_lookup AS sal
INNER JOIN supply_industry_lookup AS sil ON sil.supply_key = sal.supply_key
INNER JOIN (
	SELECT industry_key, account_key, COUNT(*) as tile_count
	FROM tile
	GROUP BY industry_key, account_key
) AS tile_by_industry_and_account ON sil.industry_key = tile_by_industry_and_account.industry_key AND sal.account_key = tile_by_industry_and_account.account_key
SET sal.amount = sal.amount - (tile_by_industry_and_account.tile_count * sil.amount)