<!-- Leaderboards -->

<!-- How To Play Block -->
<div id="how_to_play_block" class="center_block">
    <strong>How To Play</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p>
        <strong>...</strong>
    </p>
    <p>
        ...
    </p>
    <blockquote>
        ...
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
                <label for="_input_color">Your Land Color</label>
            </div><div class="col-md-6">
                <input type="hidden" name="world_key_input" value="<?php echo $world['id']; ?>">
                <input class="jscolor color_input form-control" id="account_input_color" name="color" 
                value="<?php echo $account['color']; ?>" onchange="this.form.submit()">
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
    <p>Developed in PHP with CodeIgniter 3 and the Google Maps API. You can view and contribute to this project on GitHub. All Rights Reserved.</p>
    <strong> <a href="http://github.com/goosehub/landgrab/" target="_blank">github.com/goosehub/landgrab</a></strong>
    <br>
    <br>
    <p>Special Thanks goes to Google Maps, EllisLabs, The StackExchange Network, CSS-Tricks,
    <a href="http://ithare.com/" target="_blank">itHare</a>, Muddy Dubs, me on the left, /s4s/, llamaseatsocks, Anonymous,
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
                style="color: <?php echo $leader['account']['color']; ?>"> </span>
                <?php echo $leader['user']['username']; ?>
            </td>
            <td><?php echo $leader['COUNT(*)']; ?></td>
            <td><?php echo $leader['land_mi']; ?> Mi&sup2; | <?php echo $leader['land_km']; ?> KM&sup2;</td>
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
        <div class="col-md-6"></div>
        <div class="col-md-4">
            <p class="lead">Not registered?</p>
        </div>
        <div class="col-md-2">
            <button class="register_button btn btn-success form-control">Join</button>
        </div>
    </div>
</div>

<!-- Join Block -->
<div id="register_block" class="center_block">
    <strong>Start Playing</strong>

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
      <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label for="input_password">Password <small>(Optional)</small></label>
                <input type="password" class="form-control" id="register_input_password" name="password" placeholder="Password">
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label for="input_confirm">Confirm <small>(Optional)</small></label>
                <input type="password" class="form-control" id="register_input_confirm" name="confirm" placeholder="Confirm">
              </div>
          </div>
      </div>
      <button type="submit" class="btn btn-action form-control">Start Playing</button>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-4">
            <p class="lead">Already a user?</p>
        </div>
        <div class="col-md-2">
            <button class="login_button btn btn-info form-control">Login</button>
        </div>
    </div>
</div>