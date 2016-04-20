<!-- Bankruptcy Block -->
<div id="bankruptcy_block" class="center_block">
  <strong class="text-danger">You've gone Bankrupt</strong>

  <button type="button" class="exit_center_block btn btn-default btn-sm">
    <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
  </button>
    <hr>
    <p>You've ran out of cash. You're lands have been repossessed, and you're cash reset.</p>
    <p>Try to avoid setting prices too high, so that taxes don't take you to bankruptcy.</p>
    <p>You've lost this round, but what doesn't kill you makes you stronger.</p>
</div>

<!-- Sold Lands -->
<?php if ($log_check) { ?>
<div id="sales_since_last_update_block" class="center_block">
    <strong>Land Sales (24 hours)</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <table id="sales_table" class="table table-bordered table-hover">
      <tr class="info">
        <td>Land</td>
        <td>Bought by</td>
        <td>Amount</td>
        <td>When</td>
      </tr>
    <?php $i = 0; 
    // Keep up to date with update_sales JS function
    if ($sales['sales_history']) {
        foreach ($sales['sales_history'] as $transaction) { ?>
          <tr>
              <td><a href="<?=base_url()?>world/<?php echo $world['id'] ?>/?land=<?php echo $transaction['coord_slug']; ?>">
              <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                <?php echo $transaction['name_at_sale']; ?>
              </a></td>
              <td><?php echo $transaction['paying_username']; ?></td>
              <td><strong>$<?php echo number_format($transaction['amount']); ?></strong></td>
              <td><span><?php echo $transaction['when']; ?> Ago</span></td>
          </tr>
        <?php } ?>
    <?php } ?>
    </table>
</div>
<?php } ?>

<!-- Leased Lands -->
<?php if ($log_check) { ?>
<div id="leases_since_last_update_block" class="center_block">
    <strong>Land Leases (24 hours)</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <table id="leases_table" class="table table-bordered table-hover">
      <tr class="info">
        <td>Land</td>
        <td>Leased to</td>
        <td>Amount</td>
        <td>When</td>
      </tr>
    <?php $i = 0; 
    // Keep up to date with update_leases JS function
    if ($leases['leases_history']) {
        foreach ($leases['leases_history'] as $transaction) { ?>
          <tr>
              <td><a href="<?=base_url()?>world/<?php echo $world['id'] ?>/?land=<?php echo $transaction['coord_slug']; ?>">
              <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                <?php echo $transaction['name_at_sale']; ?>
              </a></td>
              <td><?php echo $transaction['paying_username']; ?></td>
              <td><strong>$<?php echo number_format($transaction['amount']); ?></strong></td>
              <td><span><?php echo $transaction['when']; ?> Ago</span></td>
          </tr>
        <?php } ?>
    <?php } ?>
    </table>
</div>
<?php } ?>

<!-- Leaderboards -->

<!-- Leaderboard net_value Block -->
<div id="leaderboard_net_value_block" class="leaderboard_block center_block">
    <strong>Net Value Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
</div>

<!-- How To Play Block -->
<div id="how_to_play_block" class="center_block">
    <strong>How To Play</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p>
        <strong>LandGrab is a game of Owning, Trading, and Leasing the World</strong>
    </p>
    <p>
        Click on any square to buy or claim it.
        Each square makes $<?php echo $world['land_rebate']; ?> each minute with a tax of 1% on the land price you set.
    </p>
    <p>
        Own cheap squares to make a safe profit.
        Trade squares for big profits (or loses).
        Lease out rights to your squares description.
        If you run out of cash, you lose all land and your cash is reset.
    </p>
    <blockquote>
        And remember, buy low, sell high.
    </blockquote>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <p>
                This game is in beta, so feel free to point out bugs or give suggestions.
                Contact me at <a href="mailto:goosepostbox@gmail.com" target="_blank">goosepostbox@gmail.com </a>.
            </p>
        </div>
        <div class="col-md-6">
          <?php if ($log_check) { ?>
            <?php echo form_open('user/update_color'); ?>
            <div class="row"><div class="col-md-6">
                <label for="_input_primary_color">Your Land Color</label>
            </div><div class="col-md-6">
                <input type="hidden" name="world_key_input" value="<?php echo $world['id']; ?>">
                <input class="jscolor color_input form-control" id="account_input_primary_color" name="primary_color" 
                value="<?php echo $account['primary_color']; ?>" onchange="this.form.submit()">
            </div></div>
            </form>
          <?php } ?>
        </div>
    </div>
</div>

<!-- About Block -->
<div id="about_block" class="center_block">
    <strong>About LandGrab</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p>LandGrab is a game developed by Goose.</p>
    <strong> <a href="http://gooseweb.io/" target="_blank">gooseweb.io</a></strong>
    <br>
    <br>
    <p>Developed in PHP with CodeIgniter 3. You can contribute to this project on Github</p>
    <strong> <a href="http://github.com/goosehub/landgrab/" target="_blank">github.com/goosehub/landgrab</a></strong>
    <br>
    <br>
    <p>Special Thanks goes to Google Maps, EllisLabs, The StackExchange Network, CSS-Tricks,
    <a href="http://ithare.com/" target="_blank">itHare</a>, Muddy Dubs, chucke, Finesir6969, me on the left, /s4s/, 
    the rest of the Beta Testers, and all my users. Thank you!</p>
</div>

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
                style="color: <?php echo $leader['account']['primary_color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><?php echo $leader['COUNT(*)']; ?></td>
            <td><?php echo $leader['land_mi']; ?> Mi&sup2; | <?php echo $leader['land_km']; ?> KM&sup2;</td>
        </tr>
    <?php } ?>
    </table>

</div>

<!-- Leaderboard cash_owned Block -->
<div id="leaderboard_cash_owned_block" class="leaderboard_block center_block">
    <strong>Cash Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <table id="leaderboard_cash_owned_table" class="table">
        <tr class="info">
            <td>Rank</td>
            <td>Player</td>
            <td>Cash</td>
        </tr>    
    <?php foreach ($leaderboards['leaderboard_cash_owned'] as $leader) { ?>
        <tr>
            <td><?php echo $leader['rank']; ?></td>
            <td>
                <span class="glyphicon glyphicon-user" aria-hidden="true" 
                style="color: <?php echo $leader['primary_color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td>$<?php echo number_format($leader['cash']); ?></td>
        </tr>
    <?php } ?>
    </table>

</div>

<!-- Leaderboard highest_valued_land Block -->
<div id="leaderboard_highest_valued_land_block" class="leaderboard_block center_block">
    <strong>Highest Value Land Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <table id="leaderboard_highest_valued_land_table" class="table">
        <tr class="info">
            <td>Rank</td>
            <td>Player</td>
            <td>Land Name</td>
            <td>Land Price</td>
            <td>Land Description</td>
        </tr>    
    <?php foreach ($leaderboards['leaderboard_highest_valued_land'] as $leader) { ?>
        <tr>
            <td><?php echo $leader['rank']; ?></td>
            <td>
                <span class="glyphicon glyphicon-user" aria-hidden="true" 
                style="color: <?php echo $leader['account']['primary_color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><a class="leaderboard_land_link" href="<?=base_url()?>world/<?php echo $world['id']; ?>/?land=<?php echo $leader['coord_slug']; ?>">
                <?php echo $leader['land_name']; ?>
            </a></td>
            <td><a class="leaderboard_land_link" href="<?=base_url()?>world/<?php echo $world['id']; ?>/?land=<?php echo $leader['coord_slug']; ?>">
                $<?php echo number_format($leader['price']); ?>
            </a></td>
            <td><?php echo $leader['content']; ?></td>
        </tr>
    <?php } ?>
    </table>

</div>

<!-- Leaderboard cheapest_land Block -->
<div id="leaderboard_cheapest_land_block" class="leaderboard_block center_block">
    <strong>Cheapest Land Leaderboard</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <table id="leaderboard_cheapest_land_table" class="table">
        <tr class="info">
            <td>Rank</td>
            <td>Player</td>
            <td>Land Name</td>
            <td>Land Price</td>
            <td>Land Description</td>
        </tr>    
    <?php foreach ($leaderboards['leaderboard_cheapest_land'] as $leader) { ?>
        <tr>
            <td><?php echo $leader['rank']; ?></td>
            <td>
                <span class="glyphicon glyphicon-user" aria-hidden="true" 
                style="color: <?php echo $leader['account']['primary_color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><a class="leaderboard_land_link" href="<?=base_url()?>world/<?php echo $world['id']; ?>/?land=<?php echo $leader['coord_slug']; ?>">
                <?php echo $leader['land_name']; ?>
            </a></td>
            <td><a class="leaderboard_land_link" href="<?=base_url()?>world/<?php echo $world['id']; ?>/?land=<?php echo $leader['coord_slug']; ?>">
                $<?php echo number_format($leader['price']); ?>
            </a></td>
            <td><?php echo $leader['content']; ?></td>
        </tr>
    <?php } ?>
    </table>

</div>

<!-- Report Bugs Block -->
<div id="report_bugs_block" class="center_block">
    <strong>Report Bugs</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <p>Please report all bugs to 
        <strong>
            <a href="mailto:goosepostbox@gmail.com" target="_blank">goosepostbox@gmail.com </a>
        </strong>
    </p>
</div>

<!-- Error Block -->
<div id="error_block" class="center_block">
    <strong>There was an issue</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'error_block') { echo $validation_errors; } ?>
</div>

<!-- Login Block -->
<div id="login_block" class="center_block">
    <strong>Login</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'login') { echo $validation_errors; } ?>
    <!-- Form -->
    <?php echo form_open('user/login'); ?>
      <div class="form-group">
        <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
        <label for="input_username">Username</label>
        <input type="username" class="form-control" id="login_input_username" name="username" placeholder="Username">
      </div>
      <div class="form-group">
        <label for="input_password">Password</label>
        <input type="password" class="form-control" id="login_input_password" name="password" placeholder="Password">
      </div>
      <button type="submit" class="btn btn-action form-control">Login</button>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <p class="lead">Not registered?</p>
        </div>
        <div class="col-md-4">
            <button class="register_button btn btn-success form-control">Join and start immediately</button>
        </div>
    </div>
</div>

<!-- Join Block -->
<div id="register_block" class="center_block">
    <strong>Join</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'register') { echo $validation_errors; } ?>
    <!-- Form -->
    <?php echo form_open('user/register'); ?>
      <div class="form-group">
        <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
        <label for="input_username">Username</label>
        <input type="username" class="form-control" id="register_input_username" name="username" placeholder="Username">
      </div>
      <div class="form-group">
        <label for="input_password">Password</label>
        <input type="password" class="form-control" id="register_input_password" name="password" placeholder="Password">
      </div>
      <div class="form-group">
        <label for="input_confirm">Confirm</label>
        <input type="password" class="form-control" id="register_input_confirm" name="confirm" placeholder="Confirm">
      </div>
      <button type="submit" class="btn btn-action form-control">Join To Play</button>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <p class="lead">Already registered?</p>
        </div>
        <div class="col-md-4">
            <button class="login_button btn btn-primary form-control">Login</button>
        </div>
    </div>
</div>