<!-- Top Right Block -->
<div id="top_right_block">

  <!-- Live Auctions dropdown -->
  <?php 
  $auctions_active = 'btn-danger';
  if ( empty($auctions) ) {
    $auctions_active = 'hidden';
  } ?>
  <div class="btn-group">
    <button class="info_button btn dropdown-toggle <?php echo $auctions_active; ?>" type="button" id="auction_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        Auctions
      <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
      <span class="caret"></span>
    </button>
    <ul id="auctions_listing" class="dropdown-menu" aria-labelledby="auction_dropdown">
      <?php foreach ($auctions as $auction) { ?>
      <li><a class="auction_link" 
        href="<?=base_url()?>world/<?php echo $world['slug']; ?>/?land=<?php echo $auction['coord_slug']; ?>&auction=<?php echo $auction['id']; ?>">
          <strong class="auction_land_name"><?php echo $auction['land_data']['land_name']; ?></strong>
      </a></li>
      <?php } ?>
    </ul>
  </div>

  <!-- Recently sold lands button -->
  <?php if ($log_check) { ?>
    <span id="recently_sold_alert">
      <button class="sold_lands_button btn btn-action"><strong><span id="sales_since_last_update_number">
      <?php echo $sales['sales_since_last_update'] ? count($sales['sales_since_last_update']) : 0; ?> </span> Sales</strong>
        <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
      </button>
    </span>
  <?php } ?>


  <?php if ($log_check) { ?>
  <!-- Cash Dropdown -->
    <button id="cash_dropdown" class="btn btn-default" type="button" id="cash_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        $<span id="cash_display"><?php echo number_format($account['cash']); ?></span>
      <span class="caret"></span>
    </button>
    <ul class="cash_dropdown dropdown-menu" aria-labelledby="cash_dropdown">

      <li class="text-center"><strong>Financials</strong></li>
      <li role="separator" class="divider"></li>

      <li>
        <strong><span id="player_land_count_display"><?php echo $financials['player_land_count']; ?></span> Lands</strong> | 
        <span id="player_land_mi_display"><?php echo number_format($financials['player_land_count'] * (70 * $world['land_size'])); ?></span> Mi&sup2; | 
       <span id="player_land_km_display"><?php echo number_format($financials['player_land_count'] * (112 * $world['land_size'])); ?></span> KM&sup2; 
      </li>

      <li role="separator" class="divider"></li>

      <li><button class="sold_lands_button btn btn-success form-control"><strong>Sales Over Last 24 Hours</strong> </button></li>

      <li role="separator" class="divider"></li>

      <table class="table table-striped table-condensed">
        <tbody>

          <tr class="info"><td class="text-center"><strong>Income</strong></td></tr>

          <tr class="danger">
          <?php 
          $monopoly_tax_string = '';
          if ($financials['monopoly_tax'] > 0) {
          $monopoly_tax_string = '(<span id="monopoly_tax_span">' . $financials['monopoly_tax'] . ' Penalty</span> )';
          } ?>
            <td class="text-left">Land Taxes (<?php echo $world['land_tax_rate'] * 100; ?>%): <?php echo $monopoly_tax_string; ?></td>
            <td class="text-right"><span class="money_info_item red_money">
              $<span id="periodic_taxes"><?php echo number_format($financials['periodic_taxes']); ?></span>
              </span>/Day
            </td>
          </tr>
          <tr class="success">
            <td class="text-left">Earnings: ($<span id="unique_sales_span"><?php echo $financials['unique_sales']; ?></span> Bonus)</td>
            <td class="text-right"><span class="money_info_item green_money">
              $<span id="periodic_rebate"><?php echo number_format($financials['periodic_rebate']); ?></span>
            </span>/Day
          </td>
          </tr>
          <tr class="active">
            <td class="text-left">Profit: </td>
            <td class="text-right">
              <span id="income_span" class="money_info_item <?php echo $financials['income_class']; ?>">
                <span id="income_prefix"><?php echo $financials['income_prefix']; ?></span>
                $<span id="income"><?php echo number_format(abs($financials['income'])); ?></span>
              </span>/Day
            </td>
          </tr>

          <tr class="info"><td class="text-center"><strong>Trades</strong></td></tr>

          <tr class="danger">
            <td class="text-left">Purchases: </td>
            <td class="text-right">
              <span class="money_info_item red_money">
                $<span id="purchases"><?php echo number_format($financials['purchases']['sum']); ?></span>
              </span> /Day
            </td>
          </tr>
          <tr class="success">
            <td class="text-left">Sales: </td>
            <td class="text-right">
              <span class="money_info_item green_money">
                $<span id="sales"><?php echo number_format($financials['sales']['sum']); ?></span>
              </span> /Day
            </td>
          </tr>
          <tr class="active">
            <td class="text-left">Profit: </td>
            <td class="text-right">
              <span id="trades_profit_span" class="money_info_item <?php echo $financials['trades_profit_class']; ?>">
                <span id="trades_profit_prefix"><?php echo $financials['trades_profit_prefix']; ?></span>
                $<span id="trades_profit"><?php echo number_format(abs($financials['trades_profit'])); ?></span>
              </span> /Day
            </td>
          </tr>
<!-- 
          <tr class="info"><td class="text-center"><strong>Balance</strong></td></tr>

          <tr class="danger">
            <td class="text-left">Losses: </td>
            <td class="text-right"><span class="money_info_item red_money">
              $<span id="losses"><?php echo number_format($financials['losses']['sum']); ?></span>
              </span>/Day
            </td>
          </tr>
          <tr class="success">
            <td class="text-left">Gains: </td>
            <td class="text-right"><span class="money_info_item green_money">
              $<span id="gains"><?php echo number_format($financials['gains']['sum']); ?></span>
              </span>/Day
            </td>
          </tr>
          <tr class="active">
            <td class="text-left">Profit: </td>
            <td class="text-right">
              <span id="profit_span" class="money_info_item <?php echo $financials['profit_class']; ?>">
                <span id="span_prefix"><?php echo $financials['profit_prefix']; ?></span>
                $<span id="profit"><?php echo number_format(abs($financials['profit'])); ?></span>
              </span>/Day
            </td>
          </tr>
 -->

        </tbody>
      </table>

    </ul>

    <?php } ?>

    <!-- Leaderboards dropdown -->
    <div class="btn-group">
      <button class="info_button btn btn-primary dropdown-toggle" type="button" id="leaderboard_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
          Leaderboards
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu" aria-labelledby="leaderboard_dropdown">
        <li class="text-center"><strong class="text-primary">Leaderboards</strong></li>
        <!-- <li><a class="leaderboard_net_value_button leaderboard_link text-right">Net Value</a></li> -->
        <li><a class="leaderboard_land_owned_button leaderboard_link text-right">Land</a></li>
        <li><a class="leaderboard_cash_owned_button leaderboard_link text-right">Cash</a></li>
        <li><a class="leaderboard_highest_valued_land_button leaderboard_link text-right">Highest Valued Land</a></li>
        <!-- <li><a class="leaderboard_cheapest_land_button leaderboard_link text-right">Cheapest Land</a></li> -->
      </ul>
    </div>

    <!-- Worlds dropdown -->
    <div class="btn-group">
      <button class="info_button btn btn-info dropdown-toggle" type="button" id="worlds_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
          Worlds
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu" aria-labelledby="worlds_dropdown">
        <li class="text-center"><strong class="text-info">Worlds</strong></li>
        <?php foreach ($worlds as $world_list) { ?>
        <li><a class="world_link" href="<?=base_url()?>world/<?php echo $world_list['slug']; ?>">
            <strong class="<?php if ($world_list['id'] === $world['id']) { echo 'current_world'; } ?>"><?php echo $world_list['slug']; ?></strong>
        </a></li>
        <?php } ?>
      </ul>
    </div>

    <?php if ($log_check) { ?>

    <!-- User Dropdown -->
    <div class="btn-group">
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
              <div class="row"><div class="col-md-3">
                  <label for="_input_primary_color">Color</label>
              </div><div class="col-md-9">
                  <input type="hidden" name="world_key_input" value="<?php echo $world['id']; ?>">
                  <input class="jscolor color_input form-control" id="account_input_primary_color" name="primary_color" 
                  value="<?php echo $account['primary_color']; ?>" onchange="this.form.submit()">
              </div></div>
              </form>
          </li>
        </ul>
    </div>
    <?php } else { ?>
    <button class="login_button btn btn-primary">Login</button>
    <button class="register_button btn btn-action">Join</button>
    <?php } ?>

  <!-- Main Menu dropdown -->
  <div class="btn-group">
    <button class="info_button btn btn-default dropdown-toggle" type="button" id="site_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        LandGrab
      <span class="caret"></span>
    </button>
    <ul class="landgrab_menu dropdown-menu" aria-labelledby="site_dropdown">
      <li class="text-center"><strong class="text-danger">Version 1.1.0</strong></li>
      <li role="separator" class="divider"></li>
      <li><a class="how_to_play_button btn btn-warning">How To Play</a></li>
      <li><a class="about_button btn btn-info">About LandGrab</a></li>
      <li><a class="report_bugs_button btn btn-danger">Report Bugs</a></li>
    </ul>
  </div>

</div>