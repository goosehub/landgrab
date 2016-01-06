<!-- Top Right Block -->
<div id="top_right_block">
    <?php if ($log_check) { ?>

  <!-- Recently sold lands button -->
  <?php if ($recently_sold_lands) { ?>
    <button class="sold_lands_button btn btn-action"><strong><?php echo count($recently_sold_lands); ?> Sales</strong>
      <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
    </button>
  <?php } ?>

  <!-- Cash Dropdown -->
    <button id="cash_display" class="btn btn-default" type="button" id="cash_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        $<?php echo number_format($account['cash']); ?>
      <span class="caret"></span>
    </button>
    <ul class="cash_dropdown dropdown-menu" aria-labelledby="cash_dropdown">

      <li role="separator" class="divider"></li>

      <table class="table">
        <tbody>
          <tr>
            <td>Land Taxes: </td>
            <td><span class="money_info_item red_money">$<?php echo number_format($hourly_taxes); ?></span>/Hour</td>
          </tr>
        </tbody>
      </table>

      <li>Land Taxes: <span class="money_info_item red_money">$<?php echo number_format($hourly_taxes); ?></span>/Hour</li>
      <li>Rebates: ~ <span class="money_info_item green_money">$<?php echo number_format($estimated_rebate); ?></span>/Hour</li>
      <li>Income: ~ <span class="money_info_item <?php echo $income_class; ?>">
          <?php echo $income_prefix; ?>$<?php echo number_format(abs($income)); ?>
      </span>/Hour</li>

      <li role="separator" class="divider"></li>

      <li>Purchases: <span class="money_info_item red_money">$<?php echo number_format($purchases['sum']); ?></span>
       - <?php echo $purchases['total']; ?> Lands/Last 7 Days</li>
      <li>Sales: <span class="money_info_item green_money">$<?php echo number_format($sales['sum']); ?></span>
       - <?php echo $sales['total']; ?> Lands/Last 7 Days</li>
      <li>Yield: <span class="money_info_item <?php echo $yield_class; ?>">
          <?php echo $yield_prefix; ?>$<?php echo number_format(abs($yield)); ?>
      </span>/Last 7 Days</li>

      <li role="separator" class="divider"></li>

      <li>Gains: ~ <span class="money_info_item red_money">$<?php echo number_format($gains['sum']); ?></span>/Last 7 Days</li>
      <li>Losses: ~ <span class="money_info_item green_money">$<?php echo number_format($losses['sum']); ?></span>/Last 7 Days</li>
      <li>Profit: ~ <span class="money_info_item <?php echo $profit_class; ?>">
          <?php echo $profit_prefix; ?>$<?php echo number_format(abs($profit)); ?>
      </span>/Last 7 Days</li>

    </ul>

    <!-- User Dropdown -->
    <div class="btn-group">
        <button class="user_button btn btn-primary dropdown-toggle" type="button" id="user_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <?php echo $user['username']; ?>
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="user_dropdown">
          <li><a class="logout_button btn btn-danger" href="<?=base_url()?>user/logout">Log Out</a></li>
      <li role="separator" class="divider"></li>
      <li>
          <?php echo form_open('account/update_color'); ?>
          <div class="row"><div class="col-md-3">
              <label for="_input_primary_color">Color</label>
          </div><div class="col-md-9">
              <input type="hidden" name="world_key_input" value="<?php echo $world['id']; ?>">
              <input type="color" class="color_input form-control" id="account_input_primary_color" name="primary_color" 
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
    <button class="info_button btn btn-success dropdown-toggle" type="button" id="info_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        LandGrab
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="info_dropdown">
      <li><a class="how_to_play_button btn btn-default">How To Play</a></li>
      <li><a class="about_button btn btn-default">About LandGrab</a></li>
      <li><a class="report_bugs_button btn btn-default">Report Bugs</a></li>
      <li role="separator" class="divider"></li>
      <li class="text-center"><strong>Leaderboards</strong></li>
      <!-- <li><a id="leaderboard_net_value_button" class="leaderboard_link">Net Value</a></li> -->
      <li><a id="leaderboard_land_owned_button" class="leaderboard_link">Land</a></li>
      <li><a id="leaderboard_cash_owned_button" class="leaderboard_link">Cash</a></li>
      <li><a id="leaderboard_highest_valued_land_button" class="leaderboard_link">Highest Valued Land</a></li>
      <li><a class="leaderboard_cheapest_land_button leaderboard_link">Cheapest Land</a></li>
      <li role="separator" class="divider"></li>
      <li class="text-center"><strong>Worlds</strong></li>
      <?php foreach ($worlds as $world_list) { ?>
      <li><a class="world_link" href="<?=base_url()?>world/<?php echo $world_list['slug']; ?>">
          <strong class="<?php if ($world_list['id'] === $world['id']) { echo 'current_world'; } ?>"><?php echo $world_list['slug']; ?></strong>
      </a></li>
      <?php } ?>
    </ul>
  </div>
</div>