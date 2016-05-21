<!-- Top Right Block -->
<div id="top_right_block">

  <!-- Update Dropdown -->
    <button class="update_info_button menu_element btn btn-danger">
        The Army Type Update
        <span class="glyphicon glyphicon-asterisk"></span>
    </button>

  <?php if ($log_check) { ?>
  <!-- Stat Dropdown -->
    <button id="stat_dropdown" class="menu_element btn btn-default" type="button" id="stat_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <span id="active_army_display_span" class="bold_font text-danger"><?php echo number_format($account['active_army']); ?></span>/
        <span id="ready_army_display_span" class="bold_font text-danger"><?php echo number_format($account['army']); ?></span> 
        <span id="army_type_display_span" class="bold_font"><?php echo ucfirst($account['army_type']); ?></span> Army,
        <span id="population_display_span" class="bold_font text-success"><?php echo number_format($account['population']); ?></span> Pop,
        <span id="food_display_span" class="bold_font text-primary"><?php echo number_format($account['food']); ?></span> Food,
        <span id="ore_display_span" class="bold_font text-info"><?php echo number_format($account['ore']); ?></span> Ore,
        <span id="gold_display_span" class="bold_font text-warning"><?php echo number_format($account['gold']); ?></span> Gold
      <span class="caret"></span>
    </button>
    <ul id="stat_dropdown_block" class="stat_dropdown dropdown-menu" aria-labelledby="stat_dropdown">
      <li>Owned Lands: <span id="owned_lands_span" class="bold_font pull-right"><?php echo $account['land_count']; ?></span></li>
      <li>Ready Army: <span id="active_army_span" class="bold_font pull-right text-danger"><?php echo $account['active_army']; ?></span></li>
      <li>Potential Army: <span id="ready_army_span" class="bold_font pull-right text-danger"><?php echo $account['army']; ?></span></li>
      <li>Army Type: <span id="army_type_span" class="bold_font pull-right"><?php echo ucfirst($account['army_type']); ?></span></li>
      <li>Population: <span id="population_span" class="bold_font pull-right text-success"><?php echo $account['population']; ?></span></li>
      <li>Food: <span id="food_span" class="bold_font pull-right text-primary"><?php echo $account['food']; ?></span></li>
      <li>Ore: <span id="ore_span" class="bold_font pull-right text-info"><?php echo $account['ore']; ?></span></li>
      <li>Gold: <span id="gold_span" class="bold_font pull-right text-warning"><?php echo $account['gold']; ?></span></li>
      <li>Army Type: 
        <div id="scissors_select" class="btn btn-danger btn-sm pull-right">Scissors</div>
        <div id="paper_select" class="btn btn-primary btn-sm pull-right">Paper</div>
        <div id="rock_select" class="btn btn-success btn-sm pull-right">Rock</div>
      </li>
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
        <li><a class="leaderboard_army_button leaderboard_link text-right">Army</a></li>
        <li><a class="leaderboard_population_button leaderboard_link text-right">Population</a></li>
        <li><a class="leaderboard_strongholds_button leaderboard_link text-right">Strongholds</a></li>
        <li><a class="leaderboard_cities_button leaderboard_link text-right">Cities</a></li>
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
                    <button type="submit" id="submit_default_land_name" class="hidden">Submit</button>
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
      <li class="text-center"><strong class="text-danger">Version 3.0.0</strong></li>
      <li role="separator" class="divider"></li>
      <li><a class="how_to_play_button btn btn-warning">How To Play</a></li>
      <li><a class="about_button btn btn-info">About LandGrab</a></li>
      <li><a class="faq_button btn btn-primary">FAQ</a></li>
      <li><a class="btn btn-success" href="https://www.reddit.com/r/LandgrabXYZ/" target="_blank">/r/LandgrabXYZ</a></li>
      <li><a class="btn btn-success" href="https://github.com/goosehub/landgrab" target="_blank">GitHub</a></li>
      <li><a class="report_bugs_button btn btn-danger">Report Bugs</a></li>
    </ul>
  </div>

</div>