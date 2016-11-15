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
      | Tax: <strong><span id="tax_rate_span" class="text-success"><?php echo $account['tax_rate']; ?></span></strong>%
      | Pop: <strong><span span="population_span"><?php echo $account['stats']['population']; ?>K</span></strong>
      | GDP: $<strong><span span="gdp_span" class="text-success"><?php echo $account['stats']['gdp']; ?>M</span></strong>
      | Treasury: $<strong><span span="treasury_span" class="text-warning"><?php echo $account['stats']['treasury_after']; ?>M</span></strong>
      | Miliary: $<strong><span span="military_span" class="text-danger"><?php echo $account['stats']['military_after']; ?>M</span></strong>
      | Entitlements: $<strong><span span="entitlements_span" class="text-danger"><?php echo $account['stats']['entitlements']; ?>M</span></strong>
      | Support: <strong><span span="political_support_span" class="text-primary"><?php echo $account['stats']['support']; ?></span></strong>%

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