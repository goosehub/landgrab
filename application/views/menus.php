<!-- Top Right Block -->
<div id="top_right_block">

  <!-- Update Dropdown -->
    <button class="update_info_button menu_element btn btn-warning">
        Tweak Update
        <span class="glyphicon glyphicon-asterisk"></span>
    </button>

  <?php if ($log_check) { ?>
  <!-- Army Dropdown -->
    <button id="active_army_dropdown" class="menu_element btn btn-default" type="button" id="active_army_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <strong><span id="active_army_display_span"><?php echo number_format($account['active_army']); ?></span></strong> Mobilized Army
      <span class="caret"></span>
    </button>
    <ul class="active_army_dropdown dropdown-menu" aria-labelledby="active_army_dropdown">
      <li>Owned Lands: <strong><span id="owned_lands_span"><?php echo $account['land_count']; ?></span></strong></li>
      <li>Mobilized Army: <strong><span id="active_army_span"><?php echo $account['active_army']; ?></span></strong></li>
      <li>Defensive Army: <strong><span id="passive_army_span"><?php echo $account['passive_army']; ?></span></strong></li>
    </ul>

    <?php } ?>

    <!-- Leaderboards dropdown -->
    <div class="leaderboard_parent menu_element btn-group">
      <button class="info_button btn btn-primary dropdown-toggle" type="button" id="leaderboard_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
          Leaderboards
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu" aria-labelledby="leaderboard_dropdown">
        <li class="text-center"><strong class="text-primary">Leaderboards</strong></li>
        <li><a class="leaderboard_land_owned_button leaderboard_link text-right">Land</a></li>
      </ul>
    </div>

    <?php if ($log_check) { ?>

    <!-- User Dropdown -->
    <div class="user_parent menu_element btn-group">
        <button class="user_button btn btn-success dropdown-toggle" type="button" id="user_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <?php echo $user['username']; ?>
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="user_dropdown">
          <li class="text-center"><strong class="text-success">Joined <?php echo date('M jS Y', strtotime($user['created']) ); ?></strong></li>
          <li role="separator" class="divider"></li>
          <li><a class="logout_button btn btn-danger" href="<?=base_url()?>user/logout">Log Out</a></li>
          <li role="separator" class="divider"></li>
          <li>
              <?php echo form_open('user/update_color'); ?>
              <div class="row"><div class="col-md-4">
                  <label for="_input_color">Color</label>
              </div><div class="col-md-8">
                  <input type="hidden" name="world_key_input" value="<?php echo $world['id']; ?>">
                  <input class="jscolor color_input form-control" id="account_input_color" name="color" 
                  value="<?php echo $account['color']; ?>" onchange="this.form.submit()">
              </div></div>
              </form>
          </li>
          <li role="separator" class="divider"></li>
          <li>
              <?php echo form_open('user/update_default_land_name'); ?>
              <div class="row"><div class="col-md-4">
                  <label for="_input_color">Default Land Name</label>
              </div><div class="col-md-8">
                  <input type="hidden" name="world_key_input" value="<?php echo $world['id']; ?>">
                  <input type="text" id="account_input_default_land_name" class="form-control" name="default_land_name" 
                    value="<?php echo $account['default_land_name']; ?>">
              </div></div>
              </form>
          </li>
        </ul>
    </div>
    <?php } else { ?>
    <button class="login_button menu_element btn btn-primary">Login</button>
    <button class="register_button menu_element btn btn-action">Join</button>
    <?php } ?>

  <!-- Main Menu dropdown -->
  <div class="main_menu_parent menu_element btn-group">
    <button class="info_button btn btn-default dropdown-toggle" type="button" id="site_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        LandGrab
      <span class="caret"></span>
    </button>
    <ul class="landgrab_menu dropdown-menu" aria-labelledby="site_dropdown">
      <li class="text-center"><strong class="text-danger">Version 2.0.0</strong></li>
      <li role="separator" class="divider"></li>
      <li><a class="how_to_play_button btn btn-warning">How To Play</a></li>
      <li><a class="about_button btn btn-info">About LandGrab</a></li>
      <li><a class="faq_button btn btn-primary">FAQ</a></li>
      <li><a class="btn btn-success" href="https://www.reddit.com/r/LandgrabXYZ/" target="_blank">/r/LandgrabXYZ</a></li>
      <li><a class="report_bugs_button btn btn-danger">Report Bugs</a></li>
    </ul>
  </div>

</div>