<!-- Info tag -->
<div id="info_tag">
    <strong>
    <a href="https://www.reddit.com/r/LandGrab/comments/9g6jqm/rubberband_balance_update/?" target="_blank">The Rubberband Update</a>
    </strong>
</div>

<!-- Tutorial Block -->
<div id="tutorial_block">
    <span class="exit_tutorial glyphicon glyphicon-remove-sign pull-right" aria-hidden="true"></span>
    <span id="tutorial_title"></span>
    <br>
    <span id="tutorial_text"></span>
</div>

<!-- Account Update -->
<?php if ($log_check) { ?>
<div id="account_update_block" class="center_block">
    <strong>Nation Settings</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
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
        <button id="update_nation_button" type="submit" class="btn btn-success form-control">Update Nation</button>
        <br> <br>
        <a class="logout_button btn btn-sm btn-danger pull-right" href="<?=base_url()?>user/logout">Logout</a>
    </form>
</div>
<?php } ?>

<!-- Account Update -->
<?php if ($log_check) { ?>
<div id="update_password_block" class="center_block">
    <strong>Update Password</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
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

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <hr>

    <small>Version 4.1.0</small>
    <ul>
        <li></li>
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
    <p>Special Thanks goes to Google Maps, The StackExchange Network, <a href="http://gleamplay.com/" target="_blank">GleamPlay</a>, <a href="https://css-tricks.com/" target="_blank">CSS-Tricks</a>, <a href="https://www.youtube.com/user/ExtraCreditz" target="_blank">Extra Credits</a>
    <a href="http://ithare.com/" target="_blank">itHare</a>, EllisLabs and British Columbia Institute of Technology for providing CodeIgniter, me on the left, /s4s/, llamaseatsocks, Anonymous, Ricky,
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
    <p>The map currently consists of 15,000+ squares. Any more would be pushing most computers and browsers beyond its limits.</p>
    <p><strong>Can water squares be seperate from land squares?</strong></p>
    <p>This game is built on the Google Maps API. It does not tell me if a square of coords have land or not. It would need to be manually entered for each of the squares. There's also the sticky case of what counts as land or water (islands). At the moment, there's no plans to seperate the two.</p>
    <p><strong>Can you make it so new users can't start inside my land?</strong></p>
    <p>New users have to start somewhere. They can take any unfortified land. Also, calculating connected walls for each action requires a large amount of path finding logic that is currently not possible if it was desired.</p>
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
<div id="register_block" class="center_block">
    <strong>Start Playing</strong>
    <br><br>
    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'register') { echo $validation_errors; } ?>
    <!-- Form -->
    <?php echo form_open('user/register'); ?>
      <div class="form-group">
        <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
        <input type="hidden" name="ab_test" id="ab_test" value="">
        <label for="input_username">Username</label>
        <input type="username" class="form-control" id="register_input_username" name="username">
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
      <button type="submit" class="btn btn-action form-control text-is-bold">Start Playing</button>
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