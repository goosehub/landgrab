<div id="leaderboard_block" class="leaderboard_block center_block">
	<strong>Leaders</strong>

	<button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
	  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
	</button>
	<hr>

	<div class="row">
		<div class="col-md-10">
		</div>
		<div class="col-md-2">
			<select id="leaderboard_supply_select" class="form-control">
				<?php foreach ($supplies as $supply) { ?>
					<option value="<?= $supply['id']; ?>" <?php echo $supply['id'] == DEFAULT_LEADERBOARD_SUPPLY_KEY ? ' selected="selected"' : ''; ?> >
						<?= $supply['label']; ?>
					</option>
				<?php } ?>
			</select>
		</div>
	</div>

	<section id="leaderboard">
	</section>
</div>