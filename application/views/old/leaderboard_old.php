<div id="leaderboard_block" class="leaderboard_block center_block">
    <strong>Player Leaderboard</strong> <small> - Updates every <?php echo $leaderboard_update_interval_minutes; ?> minutes</small>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <div id="last_winner_parent">
        <p>
            This map resets <?php echo $next_reset; ?>
        </p>
        <p class="lead">
            Last Winner with <strong class="text-success"><?php echo number_format($world['last_winner_land_count']); ?></strong> Territories - <strong class="text-primary"><?php echo $last_winner_account['username']; ?></strong>
            <a href="<?=base_url()?>uploads/<?php echo $last_winner_account['leader_portrait']; ?>" target="_blank">
                <img class="leaderboard_leader_portrait" src="<?=base_url()?>uploads/<?php echo $last_winner_account['leader_portrait']; ?>">
            </a>
            <a href="<?=base_url()?>uploads/<?php echo $last_winner_account['nation_flag']; ?>" target="_blank">
                <img class="leaderboard_nation_flag" src="<?=base_url()?>uploads/<?php echo $last_winner_account['nation_flag']; ?>">
            </a>
        </p>
    </div>

    <table id="leaderboard_table" class="table table-bordered table-hover table-condensed jquery-datatable" style="width=100%;">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Leader</th>
                <th>Nation</th>
                <th>Territories</th>
                <th>Population</th>
                <th>Culture</th>
                <th>GDP</th>
                <th>Military</th>
            </tr>    
        </thead>
        <!-- Changes in this html should also be reflected in update_leaderboards() -->
        <tbody>
            <?php if (!empty($leaderboards)) { ?>
            <?php $rank = 1; ?>
            <?php foreach ($leaderboards as $leader) { ?>
            <tr>
                <td><strong><?php echo $rank; ?></strong></td>
                <td>
                    <span class="glyphicon glyphicon-user" aria-hidden="true" style="color: <?php echo $leader['color']; ?>"> </span>
                    <strong class="leaderboard_username"><?php echo $leader['username']; ?></strong>
                    <br>
                    <a href="<?=base_url()?>uploads/<?php echo $leader['leader_portrait']; ?>" target="_blank">
                        <img class="leaderboard_leader_portrait" src="<?=base_url()?>uploads/<?php echo $leader['leader_portrait']; ?>">
                    </a>
                </td>
                <td>
                    <strong class="leaderboard_nation_name"><?php echo $leader['nation_name']; ?></strong>
                    <br>
                    <a href="<?=base_url()?>uploads/<?php echo $leader['nation_flag']; ?>" target="_blank">
                        <img class="leaderboard_nation_flag" src="<?=base_url()?>uploads/<?php echo $leader['nation_flag']; ?>">
                    </a>
                </td>
                <td>
                    <?php // First instance for jquery datatables sorting ?>
                    <strong class="text-success"><?php echo number_format($leader['stats']['land_count']); ?></strong>
                </td>
                <td>
                    <strong class="text-info"><?php echo number_format($leader['stats']['population']); ?></strong><span class="text-info">,000</span>
                </td>
                <td>
                    <strong class="text-purple"><?php echo number_format($leader['stats']['culture']); ?></strong>
                </td>
                <td>
                    <strong class="text-action">$<?php echo number_format($leader['stats']['gdp']); ?></strong><span class="text-action">,000,000</span>
                </td>
                <td>
                    <?php
                    // Emergency Debug
                    $emergency_debug = false;
                    if ($emergency_debug && $leader['username'] === $emergency_debug) {
                    echo
                    ' ~ ' . $leader['stats']['tax_income_total'] . 
                    ' ~ ' . $leader['stats']['corruption_rate'] . 
                    ' ~ ' . $leader['stats']['corruption_total'] . 
                    ' ~ ' . $leader['stats']['tax_income'] . 
                    ' ~ ' . $leader['military_budget'] . 
                    ' ~ ' . $leader['stats']['military'] . 
                    ' ~ ' . $leader['stats']['military_spending'] . 
                    ' ~ ' . $leader['stats']['military_total'] . 
                    ' ~ '; } ?>
                    <strong class="text-danger">$<?php echo number_format($leader['stats']['military_total']); ?></strong><span class="text-danger">,000,000</span>
                </td>
            </tr>
            <?php $rank++; ?>
            <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>