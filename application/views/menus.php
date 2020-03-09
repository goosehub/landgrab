<!-- Top Right Block -->
<div id="top_right_block">

  <div class="views_parent menu_element btn-group">
    <button class="info_button btn btn-warning dropdown-toggle <?= DEFAULT_BORDER_TOGGLE ? '' : 'active'; ?>" type="button" title="Toggle Borders and Terrain" id="border_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      <span class="fa fa-map"></span>
    </button>
  </div>
  <div class="views_parent menu_element btn-group">
    <button class="info_button btn btn-success dropdown-toggle <?= DEFAULT_RESOURCE_TOGGLE ? '' : 'active'; ?>" type="button" title="Toggle Resources" id="resource_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      <span class="fa fa-mountain"></span>
    </button>
  </div>
  <div class="views_parent menu_element btn-group">
    <button class="info_button btn btn-primary dropdown-toggle <?= DEFAULT_SETTLEMENT_TOGGLE ? 'active' : ''; ?>" type="button" title="Toggle Settlements" id="settlement_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      <span class="fa fa-city"></span>
    </button>
  </div>
  <div class="views_parent menu_element btn-group">
    <button class="info_button btn btn-danger dropdown-toggle <?= DEFAULT_UNIT_TOGGLE ? 'active' : '' ?>" type="button" title="Toggle Units" id="unit_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      <span class="fa fa-fist-raised"></span>
    </button>
  </div>
  <div class="views_parent menu_element btn-group">
    <button class="info_button btn btn-info dropdown-toggle <?= DEFAULT_GRID_TOGGLE ? 'active' : '' ?>" type="button" title="Toggle Grid" id="grid_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      <span class="fa fa-crop"></span>
    </button>
  </div>

  <?php if ($account) { ?>
    <div class="government_parent menu_element btn-group">
      <button id="government_dropdown" class="government_dropdown menu_element btn btn-default" type="button" title="Laws, Budget, Supplies">
        Government <strong class="cash_span text-success">($4,120), (78/100)</strong>
        <span class="caret"></span>
      </button>
    </div>
    <div class="diplomacy_parent menu_element btn-group">
      <button id="diplomacy_dropdown" class="diplomacy_dropdown menu_element btn btn-default" type="button" title="Trade, War, Peace">
        Diplomacy <strong class="diplomacy_unread_count text-danger">(3)</strong>
        <span class="caret"></span>
      </button>
    </div>
  <?php } ?>

    <!-- Leaderboards dropdown -->
    <div class="leaderboard_parent menu_element btn-group">
      <button class="info_button btn btn-primary dropdown-toggle" type="button" id="leaderboard_dropdown" title="Leaderboard">
          Leaders
        <span class="caret"></span>
      </button>
    </div>

    <!-- worldss dropdown -->
    <div class="worlds_parent menu_element btn-group" style="display: none;">
      <button class="info_button btn btn-info dropdown-toggle" type="button" id="worlds_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
          Worlds
        <span class="caret"></span>
      </button>
      <ul class="landgrab_menu dropdown-menu" aria-labelledby="site_dropdown">
        <?php foreach ($worlds as $world) { ?>
        <li class="text-center"><a href="<?=base_url();?>world/<?php echo $world['id']; ?>"><strong class="text-default"><?php echo ucfirst($world['slug']); ?></strong></a></li>
        <?php } ?>
      </ul>
      </ul>
    </div>

    <?php if ($account) { ?>

    <!-- User Dropdown -->
    <div class="user_parent menu_element btn-group">
        <button class="user_button btn btn-primary" type="button" id="user_dropdown">
            <?php echo $account['username']; ?>
          <span class="caret"></span>
        </button>
    </div>
    <?php } else { ?>
    <button class="login_button menu_element btn btn-primary">Login</button>
    <button class="register_button menu_element btn btn-action">Join</button>
    <?php } ?>

  <!-- Main Menu dropdown -->
  <div class="main_menu_parent menu_element btn-group">
    <button class="info_button btn btn-primary dropdown-toggle" type="button" id="site_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        LandGrab
      <span class="caret"></span>
    </button>
    <ul class="landgrab_menu dropdown-menu" aria-labelledby="site_dropdown">
      <li class="text-center"><strong class="text-danger">Version 5.0.0</strong></li>
      <li role="separator" class="divider"></li>
      <!-- <li><a class="how_to_play_button btn btn-warning">How To Play</a></li> -->
      <li><a class="about_button btn btn-info">About LandGrab</a></li>
      <!-- <li><a class="faq_button btn btn-primary">FAQ</a></li> -->
      <li><a class="btn btn-primary" href="https://www.reddit.com/r/Landgrab/" target="_blank">/r/Landgrab</a></li>
      <!-- <li><a class="btn btn-success" href="http://gleamplay.com/" target="_blank">GleamPlay</a></li> -->
      <li><a class="btn btn-success" href="https://github.com/goosehub/landgrab" target="_blank">GitHub</a></li>
      <li><a class="btn btn-success" href="https://gooseweb.io/" target="_blank">GooseWeb</a></li>
      <li><a class="report_bugs_button btn btn-warning">Report Bugs</a></li>
      <li><a class="update_password_button btn btn-danger">Update Password</a></li>
      <li><a class="logout_button btn btn-danger" href="<?=base_url()?>user/logout">Logout</a></li>
      <li><small>Get your friends playing</small><div class="fb-like" data-href="https://landgrab.xyz/" data-layout="button" data-="recommend" data-show-faces="false" data-share="true"></div></li>
    </ul>
  </div>

</div>