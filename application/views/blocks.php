<!-- Leaderboards -->

<!-- How To Play Block -->
<div id="how_to_play_block" class="center_block">
    <strong>How To Play</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p>
        <strong>Landgrab is a game of fighting for control of the real world</strong>
    </p>
    <p>
        You pick a place to start your conquest. You can then spread outward. You have 20 mobilized army. Once you have more than 20 lands, your army will grow with your empire. A larger mobilized army has more chance of victory (20 Attack over 10 Defense is 2/1 chance of Victory). If your army is defeated, it will reset to 0, but will gain 20 in the next minute, then rebuild to its maximum size over 10 minutes.
    </p>
    <p>
        You can defend against attacks by dedicating a portion your mobilized army to defend a square. This will also reduce your maximum mobilized army. Use them to defend your favorite locations, or position them strategically on battle fronts. If a fortification is surrounded, it is considered under siege and it's defense is downgraded.
    </p>
    <blockquote>
        Fight for the world!
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

<!-- Update Block -->
<div id="update_info_block" class="center_block">
    <strong>Recent Updates</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <ul>
        <li>Castles are downgraded from 10,000 defense to 5,000 defense.</li>
        <li>Land can now be unclaimed.</li>
        <li>Default land names can now be set under your account dropdown.</li>
        <li>Streamlined attacking and updating interface</li>
        <li>Game is now faster and more bandwidth effecient.</li>
    </ul>
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
    <a href="http://ithare.com/" target="_blank">itHare</a>, me on the left, /s4s/, llamaseatsocks, Anonymous, Ricky,
    the rest of the Beta Testers, and all my users. Thank you!</p>
</div>

<!-- About Block -->
<div id="faq_block" class="center_block">
    <strong>Frequently Asked Questions</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p><strong>Why does it say I claimed the land, but it's the wrong color?</strong></p>
    <p>This happens when you get a land and update your map at the exact same time. It is somewhat rare and difficult to fix, but I am working on it.</p>
    <p><strong>Can you make a map with smaller squares?</strong></p>
    <p><strong>Can water squares be seperate from land squares?</strong></p>
    <p>This game is built on the Google Maps API. It does not tell me if a square of coords have land or not. It would need to be manually entered for each of the squares. There's also the sticky case of what counts as land or water (islands). At the moment, no plans to seperate the two.</p>
    <p><strong>Can you make a map with smaller squares?</strong></p>
    <p>Unfortunately, the current 15K+ squares are already pushing the limits of most browsers, as well as bandwidth and backend resources as well. This is not currently on the table.</p>
    <p><strong>Can you make it so new users can't start inside my land?</strong></p>
    <p>New users have to start somewhere. They can take any unfortified land. Also, calculating connected walls for each action requires a large amount of path finding logic that is currently not possible if it was desired.</p>
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