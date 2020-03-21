			SELECT sal.account_key, sal.amount, SUM(tile_join.count) as outer_count
			FROM supply_account_lookup AS sal
			LEFT JOIN (
			    SELECT COUNT(tile.id) * settlement.gdp AS count, account_key
			    FROM tile
			    INNER JOIN settlement ON tile.settlement_key = settlement.id
			    GROUP BY account_key, settlement_key
			) AS tile_join ON tile_join.account_key = sal.account_key
			WHERE sal.supply_key = 1
			GROUP BY sal.account_key


			SELECT settlement.label, settlement_key, settlement.gdp, COUNT(*)
			FROM tile
			INNER JOIN settlement ON tile.settlement_key = settlement.id
			WHERE account_key = 2
			GROUP BY settlement_key

			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT settlement.gdp * COUNT(tile.id) AS sum_settlement_gdp, tile.account_key
				FROM tile
				INNER JOIN settlement ON tile.settlement_key = settlement.id
				GROUP BY settlement_key, account_key
			) AS settlement_tiles ON settlement_tiles.account_key = sal.account_key
			INNER JOIN (
				SELECT industry.gdp * COUNT(tile.id) AS sum_industry_gdp, tile.account_key
				FROM tile
				INNER JOIN industry ON tile.industry_key = industry.id
				GROUP BY industry_key, account_key
			) AS industry_tiles ON industry_tiles.account_key = sal.account_key
			SET sal.amount = sum_settlement_gdp + sum_industry_gdp
			WHERE sal.supply_key = " . CASH_KEY . "



			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT (industry.gdp * COUNT(tile.id)) + (settlement.gdp * COUNT(tile.id)) AS sum_gdp, tile.account_key
				FROM tile
				INNER JOIN settlement ON tile.settlement_key = settlement.id
				INNER JOIN industry ON tile.industry_key = industry.id
				GROUP BY settlement_key, industry_key, account_key
			) AS outer_tile ON outer_tile.account_key = sal.account_key
			SET sal.amount = sum_gdp
			WHERE sal.supply_key = " . CASH_KEY . "



			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT SUM(industry.gdp) as sum_industry_gdp, industry_key, account_key
				FROM tile
				INNER JOIN industry AS industry ON industry.id = tile.industry_key
				GROUP BY industry_key, account_key
			) AS tile_by_industry_and_account ON sal.account_key = tile_by_industry_and_account.account_key
			INNER JOIN (
				SELECT SUM(settlement.gdp) as sum_settlement_gdp, settlement_key, account_key
				FROM tile
				INNER JOIN settlement AS settlement ON settlement.id = tile.settlement_key
				GROUP BY settlement_key, account_key
			) AS tile_by_settlement_and_account ON sal.account_key = tile_by_settlement_and_account.account_key
			SET sal.amount = sal.amount + sum_industry_gdp + sum_settlement_gdp
			WHERE sal.supply_key = " . CASH_KEY . "




			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT id, gdp, account_key, SUM(gdp * tile_count) AS settlement_gdp
				FROM settlement
				INNER JOIN (
					SELECT COUNT(tile.id) AS tile_count, settlement_key, account_key
					FROM tile
					GROUP BY account_key, settlement_key
				) AS tile_by_settlement_and_account ON settlement.id = tile_by_settlement_and_account.settlement_key
			) AS settlement ON sal.account_key = settlement.account_key
			INNER JOIN (
				SELECT id, gdp, account_key, SUM(gdp * tile_count) AS industry_gdp
				FROM industry
				INNER JOIN (
					SELECT COUNT(tile.id) AS tile_count, industry_key, account_key
					FROM tile
					GROUP BY account_key, industry_key
				) AS tile_by_industry_and_account ON industry.id = tile_by_industry_and_account.industry_key
			) AS industry ON sal.account_key = industry.account_key
			SET sal.amount = sal.amount + sum_industry_gdp + sum_settlement_gdp
			WHERE sal.supply_key = " . CASH_KEY . "

			
			UPDATE supply_account_lookup AS sal
			INNER JOIN (
				SELECT SUM(industry_gdp) AS sum_industry_gdp, outer_tile.industry_key, outer_tile.account_key
				FROM tile AS outer_tile
				INNER JOIN (
					SELECT industry.id, (industry.gdp * COUNT(inner_tile.id)) AS industry_gdp
					FROM industry
					INNER JOIN (
						SELECT tile.id, tile.industry_key
						FROM tile
					) AS inner_tile ON industry.id = inner_tile.industry_key
				) AS industry ON industry.id = outer_tile.industry_key
				GROUP BY outer_tile.industry_key, outer_tile.account_key
			) AS tile_by_industry_and_account ON sal.account_key = tile_by_industry_and_account.account_key
			INNER JOIN (
				SELECT SUM(settlement_gdp) AS sum_settlement_gdp, outer_tile.settlement_key, outer_tile.account_key
				FROM tile AS outer_tile
				INNER JOIN (
					SELECT settlement.id, (settlement.gdp * COUNT(inner_tile.id)) AS settlement_gdp
					FROM settlement
					INNER JOIN (
						SELECT tile.id, tile.settlement_key
						FROM tile
					) AS inner_tile ON settlement.id = inner_tile.settlement_key
				) AS settlement ON settlement.id = outer_tile.settlement_key
				GROUP BY outer_tile.settlement_key, outer_tile.account_key
			) AS tile_by_settlement_and_account ON sal.account_key = tile_by_settlement_and_account.account_key
			SET sal.amount = sum_industry_gdp
			WHERE sal.supply_key = " . CASH_KEY . "