<!-- Info tag -->
<div id="info_tag">
    <strong>
    <!-- <a href="https://www.reddit.com/r/LandGrab/comments/9oicbv/the_embassy_limiting_update/?" target="_blank">The Embassy Limit Update</a> -->
    Industry Update - ALPHA
    </strong>
</div>

<!--  -->
<div id="combat_block" class="center_block center_block_small">
    <div class="combat_pending_message">
        <h3 class="text-center text-primary">
            <span id="chance_of_victory_text"></span>% chance of victory
        </h3>
    </div>
    <div class="victory_message">
        <h3 class="text-center text-success">Victory</h3>
    </div>
    <div class="defeat_message">
        <h3 class="text-center text-danger">Defeat</h3>
    </div>
    <hr>
    <img id="defender_unit_image" class="combat_unit_image pull-left" src=""/>
    <img id="attacker_unit_image" class="combat_unit_image pull-right" src=""/>
    <div class="progress">
        <div id="victory_bar" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
        <div id="defender_bar" class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
        <div id="total_bar" class="progress-bar progress-bar-primary progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
    </div>
    <div class="pull-left">
        <p id="attack_power_parent">
            <strong id="attack_power" class="text-success"></strong>
            <label id="attack_power_label" class="text-purple">
                Total Attack Power:
            </label>
        </p>
        <p id="matchup_offensive_bonus_parent">
            <strong id="matchup_offensive_bonus" class="text-success"></strong>
            <label id="matchup_offensive_bonus_label" class="text-primary">
                Matchup Bonus:
            </label>
        </p>
        <p id="terrain_offensive_bonus_parent">
            <strong id="terrain_offensive_bonus" class="text-success"></strong>
            <label id="terrain_offensive_bonus_label" class="text-primary">
                Terrain Bonus:
            </label>
        </p>
    </div>
    <div class="pull-right">
        <p id="defend_power_parent">
            <strong id="defend_power" class="text-success"></strong>
            <label id="defend_power_label" class="text-purple">
                :Total Defend Power
            </label>
        </p>
        <p id="matchup_defensive_bonus_parent">
            <strong id="matchup_defensive_bonus" class="text-danger"></strong>
            <label id="matchup_defensive_bonus_label" class="text-primary">
                :Matchup Bonus
            </label>
        </p>
        <p id="terrain_defensive_bonus_parent">
            <strong id="terrain_defensive_bonus" class="text-danger"></strong>
            <label id="terrain_defensive_bonus_label" class="text-primary">
                :Terrain Bonus
            </label>
        </p>
        <p id="township_defensive_bonus_parent">
            <strong id="township_defensive_bonus" class="text-danger"></strong>
            <label id="township_defensive_bonus_label" class="text-primary">
                :Township Bonus
            </label>
        </p>
    </div>
</div>

<!-- Tutorial Block -->
<div id="tutorial_block">
    <span class="exit_tutorial glyphicon glyphicon-remove-sign pull-right" aria-hidden="true"></span>
    <span id="tutorial_title"></span>
    <br>
    <span id="tutorial_text"></span>
</div>

<!-- Account Update -->
<?php if ($account) { ?>
<div id="account_update_block" class="center_block center_block_small">
    <strong>National Charter</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <!-- Form -->
    <?php echo form_open_multipart('user/update_account_info'); ?>
        <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_nation_name">Nation Name:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="nation_name" name="nation_name" value="<?php echo $account['nation_name']; ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_nation_flag">National Flag:</label>
                </div>
                <div class="col-md-2">
                    <a href="<?=base_url()?>uploads/<?php echo $account['nation_flag']; ?>" target="_blank">
                        <img id="account_nation_flag_image" src="<?=base_url()?>uploads/<?php echo $account['nation_flag']; ?>"/>
                    </a>
                </div>
                <div class="col-md-6">
                    <input type="hidden" name="existing_nation_flag" value="<?php echo $account['nation_flag']; ?>">
                    <input type="file" class="form-control" id="nation_flag" name="nation_flag" value="<?php echo $account['nation_flag']; ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_nation_color">National Color:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="jscolor color_input form-control" id="nation_color" name="nation_color" value="<?php echo $account['color']; ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_leader_portrait">Leader Portrait:</label>
                </div>
                <div class="col-md-2">
                    <a href="<?=base_url()?>uploads/<?php echo $account['leader_portrait']; ?>" target="_blank">
                        <img id="account_leader_portrait_image" src="<?=base_url()?>uploads/<?php echo $account['leader_portrait']; ?>"/>
                    </a>
                </div>
                <div class="col-md-6">
                    <input type="hidden" name="existing_leader_portrait" value="<?php echo $account['leader_portrait']; ?>">
                    <input type="file" class="form-control" id="leader_portrait" name="leader_portrait" value="<?php echo $account['leader_portrait']; ?>">
                </div>
            </div>
        </div>
        <hr>
        <button id="update_nation_button" type="submit" class="btn btn-success form-control">Update National Charter</button>
        <br> <br>
        <a class="logout_button btn btn-sm btn-danger pull-right" href="<?=base_url()?>user/logout">Logout</a>
    </form>
</div>
<?php } ?>

<!-- Account Update -->
<?php if ($account) { ?>
<div id="update_password_block" class="center_block">
    <strong>Update Password</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'update_password') { echo $validation_errors; } ?>
    <!-- Form -->
    <?php echo form_open('user/update_password'); ?>
      <div class="form-group">
        <label for="input_password">
            Current Password
        </label>
        <input type="password" class="form-control" id="update_password_current" name="current_password" placeholder="Password">
      </div>
      <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label for="input_password">
                    New Password
                </label>
                <input type="password" class="form-control" id="update_password_new" name="new_password" placeholder="Password">
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label for="input_confirm">
                    Confirm
                </label>
                <input type="password" class="form-control" id="update_password_confirm" name="confirm" placeholder="Confirm">
              </div>
          </div>
      </div>
      <button type="submit" class="btn btn-action form-control">Update Password</button>
    </form>
</div>
<?php } ?>

<!-- Update Block -->
<div id="update_info_block" class="center_block">
    <strong>Recent Updates</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <hr>

    <small>Version 5.0.0</small>
    <ul>
        <li></li>
    </ul>
</div>

<!-- About Block -->
<div id="about_block" class="center_block center_block_small">
    <strong>About LandGrab</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p>LandGrab is a game developed by Goose.</p>
    <strong> <a href="http://gooseweb.io/" target="_blank">gooseweb.io</a></strong>
    <br>
    <br>
    <p>Developed in PHP with CodeIgniter 3 and the Google Maps API. Icons provided by <a href="https://icons8.com/">Icons8.com</a>. You can view and contribute to this project on GitHub.</p>
    <strong> <a href="http://github.com/goosehub/landgrab/" target="_blank">github.com/goosehub/landgrab</a></strong>
    <br>
    <br>
    <p>Special Thanks goes to Google Maps, The StackExchange Network, <a href="http://gleamplay.com/" target="_blank">GleamPlay</a>, <a href="https://css-tricks.com/" target="_blank">CSS-Tricks</a>, <a href="https://www.youtube.com/user/ExtraCreditz" target="_blank">Extra Credits</a>, <a href="https://www.youtube.com/watch?v=dUnM3lPMb1Q" target="_blank">Yahtzee's Dev Diary Series</a>,
    <a href="http://ithare.com/" target="_blank">ITHare</a>, EllisLabs and British Columbia Institute of Technology for providing CodeIgniter, me on the left, /s4s/, llamaseatsocks, Anonymous, Ricky,
    the rest of the v1/v2 Beta Testers, and all my users. Thank you!</p>
</div>

<!-- About Block -->
<div id="faq_block" class="center_block">
    <strong>Frequently Asked Questions</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p><strong>Why does it say I claimed the land, but it's the wrong color?</strong></p>
    <p>This happens when you get a land and update your map at the exact same time. It is somewhat rare and difficult to fix, but I am working on it.</p>
    <p><strong>Can you make a map with smaller squares?</strong></p>
    <p>The map currently consists of 15,000+ squares. Any more would be pushing most computers and browsers beyond its limits.</p>
    <p><strong>Can water squares be seperate from land squares?</strong></p>
    <p>This game is built on the Google Maps API. It does not tell me if a square of coords have land or not. It would need to be manually entered for each of the squares. There's also the sticky case of what counts as land or water (islands). At the moment, there's no plans to seperate the two.</p>
    <p><strong>Can you make it so new users can't start inside my land?</strong></p>
    <p>New users have to start somewhere. They can take any unfortified land. Also, calculating connected walls for each action requires a large amount of path finding logic that is currently not possible if it was desired.</p>
</div>

<!-- Report Bugs Block -->
<div id="report_bugs_block" class="center_block center_block_small">
    <strong>Report Bugs</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
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

    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'error_block') { echo $validation_errors; } ?>
</div>

<!-- Login Block -->
<div id="login_block" class="center_block center_block_extra_small">
    <strong>Login</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
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
        <div class="col-md-3"></div>
        <div class="col-md-7">
            <p>Not registered?</p>
        </div>
        <div class="col-md-2">
            <button class="register_button btn btn-success form-control">Join</button>
        </div>
    </div>
</div>

<!-- Join Block -->
<div id="register_block" class="center_block center_block_extra_small">
    <strong>Start Playing</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <br><br>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'register') { echo $validation_errors; } ?>
    <!-- Form -->
    <?php echo form_open('user/register'); ?>
      <div class="row">
          <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
                <input type="hidden" name="ab_test" id="ab_test" value="">
                <label for="input_username">Username</label>
                <input type="username" class="form-control" id="register_input_username" name="username">
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label for="input_password">
                    Password
                    <small class="text-primary">(Optional, but needed to save progress)</small>
                </label>
                <input type="password" class="form-control" id="register_input_password" name="password">
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label for="input_confirm">
                    Confirm
                </label>
                <input type="password" class="form-control" id="register_input_confirm" name="confirm">
              </div>
          </div>
      </div>
      <div class="row">
           <div class="col-md-3">
                <button class="open_terms_block btn btn-info btn-sm" type="button">
                    Terms
                </button>
            </div>
          <div class="col-md-9">
              <button type="submit" class="btn btn-action form-control text-is-bold">Start Playing</button>
          </div>
    </div>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-7"></div>
        <div class="col-md-3    ">
            <p>Already a user?</p>
        </div>
        <div class="col-md-2">
            <button class="login_button btn btn-info form-control">Login</button>
        </div>
    </div>

</div>

<div id="terms_block" class="center_block center_block_extra_small">
    <strong>Terms and Conditions</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <br>
    <br>
    <p class="lead">
        Landgrab is developed and maintained primarily by a single person in their spare time.
        Please understand their limited ability to moderate chat, monitor cheating, and guarantee stability.
    </p>
    <br>
    By Using Landgrab, you agree to the following Terms and Conditions
    <br>
    <br>
    <strong>Content</strong>
    <br>
    Landgrab may contain user submitted content not appropriate for individuals who are less than 18 years of age.
    <br>
    <strong>Cookies</strong>
    <br>
    Landgrab uses Cookies to track gameplay preferences.
    Landgrab does not use marketing cookies.
    <br>
    <strong>Privacy Policy</strong>
    <br>
    Landgrab may share anonymized data for educational purposes free of charge.
    Landgrab does not sell any collected data.
    <br>
    <strong>Limitation of Liability</strong>
    <br>
    Landgrab is not responsible for user content, chat, and/or communications.
    Users are responsible for their own actions and all consequences that may arise.
    Landgrab does not make any warranty that the website is free from infection from viruses.
    Landgrab will not be liable for any loss or damage as a consequence of Landgrab becoming temporarily or permanently unavailable. 
    You agree to assume all risk related to your use of Landgrab, including but not limited to, the risk of communications with other people or damage to your computer.
    <br>
    <strong>User Rules</strong>
    <br>
    You will not post content that is illegal, pornographic, overly disruptive, abusive towards individual users, or contains personally identifying information.
    You will not use multiple active accounts without obtaining permission from Landgrab.
    If you feel that the behaviour of another user breaches these Terms and Conditions then please let me know by sending an email to goosepostbox@gmail.com.
    <br>
    <br>
    These Terms and Conditions are governed by the laws of the United States of America.
    <br>
    The above Terms and Conditions are subject to change
    <br>
    <button class="register_button btn btn-success form-control">Agree and return to Create Account</button>
    <br>
</div>