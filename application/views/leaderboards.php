<!-- Leaderboard land_owned Block -->
<div id="leaderboard_land_owned_block" class="leaderboard_block center_block">
    <strong>Land Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <table id="leaderboard_land_owned_table" class="table">
        <tr class="info">
            <td>Rank</td>
            <td>Player</td>    
            <td>Lands Owned</td>
            <td>Area <small>(Approx.)</small></td>
        </tr>    
    <?php foreach ($leaderboards['leaderboard_land_owned'] as $leader) { ?>
        <tr>
            <td><?php echo $leader['rank']; ?></td>
            <td>
                <span class="glyphicon glyphicon-user" aria-hidden="true" 
                style="color: <?php echo $leader['account']['color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><?php echo $leader['total']; ?></td>
            <td><?php echo $leader['land_mi']; ?> Mi&sup2; | <?php echo $leader['land_km']; ?> KM&sup2;</td>
        </tr>
    <?php } ?>
    </table>
</div>

<!-- Leaderboard cities Block -->
<div id="leaderboard_cities_block" class="leaderboard_block center_block">
    <strong>Cities Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <table id="leaderboard_cities_table" class="table">
        <tr class="info">
            <td>Rank</td>
            <td>Player</td>    
            <td>Cities Owned</td>
        </tr>    
    <?php foreach ($leaderboards['leaderboard_cities'] as $leader) { ?>
        <tr>
            <td><?php echo $leader['rank']; ?></td>
            <td>
                <span class="glyphicon glyphicon-user" aria-hidden="true" 
                style="color: <?php echo $leader['account']['color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><?php echo $leader['total']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<!-- Leaderboard strongholds Block -->
<div id="leaderboard_strongholds_block" class="leaderboard_block center_block">
    <strong>Strongholds Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <table id="leaderboard_strongholds_table" class="table">
        <tr class="info">
            <td>Rank</td>
            <td>Player</td>    
            <td>Strongholds Owned</td>
        </tr>    
    <?php foreach ($leaderboards['leaderboard_strongholds'] as $leader) { ?>
        <tr>
            <td><?php echo $leader['rank']; ?></td>
            <td>
                <span class="glyphicon glyphicon-user" aria-hidden="true" 
                style="color: <?php echo $leader['account']['color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><?php echo $leader['total']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<!-- Leaderboard army Block -->
<div id="leaderboard_army_block" class="leaderboard_block center_block">
    <strong>Army Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <table id="leaderboard_army_table" class="table">
        <tr class="info">
            <td>Rank</td>
            <td>Player</td>    
            <td>Army Size</td>
        </tr>    
    <?php foreach ($leaderboards['leaderboard_army'] as $leader) { ?>
        <tr>
            <td><?php echo $leader['rank']; ?></td>
            <td>
                <span class="glyphicon glyphicon-user" aria-hidden="true" 
                style="color: <?php echo $leader['account']['color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><?php echo $leader['army']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<!-- Leaderboard population Block -->
<div id="leaderboard_population_block" class="leaderboard_block center_block">
    <strong>Population Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <table id="leaderboard_population_table" class="table">
        <tr class="info">
            <td>Rank</td>
            <td>Player</td>    
            <td>Population Size</td>
        </tr>    
    <?php foreach ($leaderboards['leaderboard_population'] as $leader) { ?>
        <tr>
            <td><?php echo $leader['rank']; ?></td>
            <td>
                <span class="glyphicon glyphicon-user" aria-hidden="true" 
                style="color: <?php echo $leader['account']['color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><?php echo $leader['population']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>