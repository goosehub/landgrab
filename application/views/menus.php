<!-- Top Right Block -->
<div id="top_right_block">

  <!-- Update Dropdown -->
<!--     <button class="update_info_button menu_element btn btn-danger">
        The ___ Update
        <span class="glyphicon glyphicon-asterisk"></span>
    </button> -->

  <?php if ($log_check) { ?>
  <!-- Stat Dropdown -->
    <button id="stat_dropdown" class="menu_element btn btn-default" type="button" id="stat_dropdown">
      <strong><span id="government_span" class=""><?php echo $government_dictionary[$account['government']]; ?></span></strong>
      |
      <strong><span id="tax_rate_span" class="text-success"><?php echo $account['tax_rate']; ?></span></strong>% -
      <strong><span id="military_budget_span" class="text-danger"><?php echo $account['military_budget']; ?></span></strong>% -
      <strong><span id="entitlements_span" class="text-info"><?php echo $account['entitlements_budget']; ?></span></strong>%
      |
      Pop: <strong><span id="population_span">1K</span></strong>
      |
      $<strong><span id="gdp_span" class="text-success">812M</span></strong> -
      $<strong><span id="treasury_span" class="text-warning">200M</span></strong> -
      $<strong><span id="military_span" class="text-danger">182M</span></strong>
      |
      Support: <strong><span id="political_support_span" class="text-primary">59</span></strong>% -

      <span class="caret"></span>
    </button>
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
        <button class="user_button btn btn-success" type="button" id="user_dropdown">
            <?php echo $user['username']; ?>
          <span class="caret"></span>
        </button>
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
      <li class="text-center"><strong class="text-danger">Version 4.0.0</strong></li>
      <li role="separator" class="divider"></li>
      <li><a class="how_to_play_button btn btn-warning">How To Play</a></li>
      <li><a class="about_button btn btn-info">About LandGrab</a></li>
      <li><a class="faq_button btn btn-primary">FAQ</a></li>
      <li><a class="btn btn-success" href="https://www.reddit.com/r/Landgrab/" target="_blank">/r/Landgrab</a></li>
      <li><a class="btn btn-success" href="https://github.com/goosehub/landgrab" target="_blank">GitHub</a></li>
      <li><a class="report_bugs_button btn btn-danger">Report Bugs</a></li>
    </ul>
  </div>

</div>