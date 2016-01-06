<!DOCTYPE html>
<html>
  <head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<meta charset="utf-8">

    <!-- For Mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
	<title>Landgrab</title>

    <!-- Link to Favicon -->
    <link rel="icon" href="<?=base_url()?>resources/icon.ico">

	<!-- Bootstrap -->
	<link href="<?=base_url()?>resources/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">

	<!-- Custom Fonts -->
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

  <!-- Local Style -->
  <link href="<?=base_url()?>resources/style.css?<?php echo time(); ?>" rel="stylesheet" type="text/css">

  </head>
  <body>
  <!-- Facebook like code -->
  <div id="fb-root"></div>
  <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '523758294469574',
        xfbml      : true,
        version    : 'v2.5'
      });
    };

   (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=523758294469574";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
   </script>

  <div class="top_left_block">
    <div class="fb-like" data-href="http://landgrab.xyz/" data-layout="button" data-action="recommend" data-show-faces="false" data-share="true"></div>
  </div>

  	<!-- Map Element -->
	<div id="map"></div>

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

          <li>Total Lands: <?php echo $total_lands; ?> | 
          ~<?php echo number_format($total_lands * (70 * $world['land_size'])); ?> Mi&sup2; | 
          ~<?php echo number_format($total_lands * (112 * $world['land_size'])); ?> KM&sup2;</li>

          <li role="separator" class="divider"></li>

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

    <!-- Center Blocks -->

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
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <p class="lead">Not registered?</p>
            </div>
            <div class="col-md-4">
                <button class="register_button btn btn-success form-control">Join to play</button>
            </div>
        </div>
    </div>

    <!-- Join Block -->
    <div id="register_block" class="center_block">
    	<strong>Join</strong>

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
    	  <div class="form-group">
    	    <label for="input_password">Password</label>
    	    <input type="password" class="form-control" id="register_input_password" name="password" placeholder="Password">
    	  </div>
    	  <div class="form-group">
    	    <label for="input_confirm">Confirm</label>
    	    <input type="password" class="form-control" id="register_input_confirm" name="confirm" placeholder="Confirm">
    	  </div>
    	  <button type="submit" class="btn btn-action form-control">Join To Play</button>
        </form>
        <hr>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <p class="lead">Already registered?</p>
            </div>
            <div class="col-md-4">
                <button class="login_button btn btn-primary form-control">Login</button>
            </div>
        </div>
    </div>

    <!-- How To Play Block -->
    <div id="how_to_play_block" class="center_block">
    	<strong>How To Play</strong>

    	<button type="button" class="exit_center_block btn btn-default btn-sm">
    	  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    	</button>
        <hr>
        <p>
            <strong>LandGrab is a game of Claiming, Buying, and Selling the Real World.</strong>
        </p>
        <p>
            Begin by buying and claiming land.
            Set a price on land you buy, but be careful not to charge too much, land is taxed at 1% hourly on the prices you set.
            Each plot of land scores you an hourly income.
            Famous areas are valuable.
            Find undervalued areas and sell them for a big profit.
            Run out of cash, you lose all land and your account is reset.
        </p>
        <p>
            Use the menu to view finances,
            the leaderboards (<a class="leaderboard_cheapest_land_button fake_link">View the Cheapest Land</a>),
            or different worlds (<a href="<?=base_url()?>world/big">Here's a bigger world</a>).
        </p>

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
                <?php echo form_open('account/update_color'); ?>
                <div class="row"><div class="col-md-6">
                    <label for="_input_primary_color">Your Land Color</label>
                </div><div class="col-md-6">
                    <input type="hidden" name="world_key_input" value="<?php echo $world['id']; ?>">
                    <input type="color" class="color_input form-control" id="account_input_primary_color" name="primary_color" 
                    value="<?php echo $account['primary_color']; ?>" onchange="this.form.submit()">
                </div></div>
                </form>
              <?php } ?>
            </div>
        </div>
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
        <p>Developed in PHP with CodeIgniter 3. You can contribute to this project on Github</p>
        <strong> <a href="http://github.com/goosehub/landgrab/" target="_blank">github.com/goosehub/landgrab</a></strong>
        <br>
        <br>
        <p>Special Thanks goes to Google Maps, EllisLabs, The StackExchange Network, CSS-Tricks,
        <a href="http://ithare.com/" target="_blank">itHare</a>, Muddy Dubs, chucke, Finesir6969, me on the left, the rest of the Beta Testers, and all my users. Thank you!</p>
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

    <!-- Bankruptcy Block -->
    <div id="bankruptcy_block" class="center_block">
      <strong>You've gone Bankrupt</strong>

      <button type="button" class="exit_center_block btn btn-default btn-sm">
        <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
      </button>
        <hr>

        <p>You've ran out of cash. You're lands have been repossessed, and you're cash reset. You've lost this round.
        Try to avoid setting prices too high, so that taxes don't eat you to death.</p>
    </div>

    <!-- Recently Sold Lands -->
    <?php if ($log_check && $recently_sold_lands ) { ?>
    <div id="recently_sold_lands_block" class="center_block">
        <strong>Land Sales since last page load</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>
        <hr>

        <table class="table table-bordered table-hover">
          <tr class="info">
            <td>Land</td>
            <td>Bought by</td>
            <td>Amount</td>
          </tr>
        <?php $i = 0; 
        foreach ($recently_sold_lands as $transaction) {
          $paying_account = $this->user_model->get_account_by_id($transaction['paying_account_key']);
          $paying_user = $this->user_model->get_user($paying_account['user_key']); ?>
          <tr>
          <td><a href="<?=base_url()?>world/<?php echo $world['id'] ?>/?land=<?php echo $transaction['coord_slug']; ?>">
            <?php echo $transaction['name_at_sale']; ?>
            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
          </a></td>
          <td><?php echo $paying_user['username']; ?></td>
          <td><strong>$<?php echo number_format($transaction['amount']); ?></strong></td>
          </tr>
          <!-- Break after 10 iterations -->
          <?php $i++; if ($i === 10) { ?>
          <tr><td><strong>More sales not includes</strong></td></tr>
          <?php } ?>
        <?php } ?>
        </table>
    </div>
    <?php } ?>

    <!-- Leaderboards -->

    <!-- Leaderboard net_value Block -->
    <div id="leaderboard_net_value_block" class="leaderboard_block center_block">
        <strong>Net Value Leaderboard</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>
        <hr>
    </div>

    <!-- Leaderboard land_owned Block -->
    <div id="leaderboard_land_owned_block" class="leaderboard_block center_block">
        <strong>Land Leaderboard</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>
        <hr>
        <table class="table">
            <tr class="info">
                <td>Rank</td>
                <td>Player</td>    
                <td>Lands Owned</td>
                <td>Area <small>(Approx.)</small></td>
            </tr>    
        <?php $rank = 1; ?>
        <?php foreach ($leaderboard_land_owned_data as $leader) { ?>
        <?php $leader_account = $this->user_model->get_account_by_id($leader['account_key']); ?>
        <?php $leader_user = $this->user_model->get_user($leader_account['user_key']); ?>
            <tr>
                <td><?php echo $rank; ?></td>
                <td>
                    <span class="glyphicon glyphicon-user" aria-hidden="true" 
                    style="color: <?php echo $leader_account['primary_color']; ?>"> </span>
                    <?php echo $leader_user['username']; ?>
                </td>
                <td><?php echo $leader['COUNT(*)']; ?></td>
                <?php // Math for finding approx area of land owned ?>
                <td>~<?php echo number_format($leader['COUNT(*)'] * (70 * $world['land_size'])); ?> Mi&sup2; | 
                ~<?php echo number_format($leader['COUNT(*)'] * (112 * $world['land_size'])); ?> KM&sup2;</td>
            </tr>
        <?php $rank++; ?>
        <?php } ?>
        </table>

    </div>

    <!-- Leaderboard cash_owned Block -->
    <div id="leaderboard_cash_owned_block" class="leaderboard_block center_block">
        <strong>Cash Leaderboard</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>
        <hr>
        <table class="table">
            <tr class="info">
                <td>Rank</td>
                <td>Player</td>
                <td>Cash</td>
            </tr>    
        <?php $rank = 1; ?>
        <?php foreach ($leaderboard_cash_owned_data as $leader_account) { ?>
        <?php $leader_user = $this->user_model->get_user($leader_account['user_key']); ?>
            <tr>
                <td><?php echo $rank; ?></td>
                <td>
                    <span class="glyphicon glyphicon-user" aria-hidden="true" 
                    style="color: <?php echo $leader_account['primary_color']; ?>"> </span>
                    <?php echo $leader_user['username']; ?>
                </td>
                <td>$<?php echo number_format($leader_account['cash']); ?></td>
            </tr>
        <?php $rank++; ?>
        <?php } ?>
        </table>

    </div>

    <!-- Leaderboard highest_valued_land Block -->
    <div id="leaderboard_highest_valued_land_block" class="leaderboard_block center_block">
        <strong>Highest Value Land Leaderboard</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>
        <hr>
        <table class="table">
            <tr class="info">
                <td>Rank</td>
                <td>Player</td>
                <td>Land Name</td>
                <td>Land Price</td>
                <td>Land Description</td>
            </tr>    
        <?php $rank = 1; ?>
        <?php foreach ($leaderboard_highest_valued_land_data as $leader) { ?>
        <?php $leader_account = $this->user_model->get_account_by_id($leader['account_key']); ?>
        <?php $leader_user = $this->user_model->get_user($leader_account['user_key']); ?>
            <tr>
                <td><?php echo $rank; ?></td>
                <td>
                    <span class="glyphicon glyphicon-user" aria-hidden="true" 
                    style="color: <?php echo $leader_account['primary_color']; ?>"> </span>
                    <?php echo $leader_user['username']; ?>
                </td>
                <td><a class="leaderboard_land_link" href="<?=base_url()?>world/<?php echo $world['id']; ?>/?land=<?php echo $leader['coord_slug']; ?>">
                    <?php echo $leader['land_name']; ?>
                </a></td>
                <td><a class="leaderboard_land_link" href="<?=base_url()?>world/<?php echo $world['id']; ?>/?land=<?php echo $leader['coord_slug']; ?>">
                    $<?php echo number_format($leader['price']); ?>
                </a></td>
                <td><?php echo mb_substr(strip_tags($leader['content']), 0, 42); if (strlen(strip_tags($leader['content'])) > 42) { echo '...'; } ?></td>
            </tr>
        <?php $rank++; ?>
        <?php } ?>
        </table>

    </div>

    <!-- Leaderboard cheapest_land Block -->
    <div id="leaderboard_cheapest_land_block" class="leaderboard_block center_block">
        <strong>Cheapest Land Leaderboard</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>
        <hr>
        <table class="table">
            <tr class="info">
                <td>Rank</td>
                <td>Player</td>
                <td>Land Name</td>
                <td>Land Price</td>
                <td>Land Description</td>
            </tr>    
        <?php $rank = 1; ?>
        <?php foreach ($leaderboard_cheapest_land_data as $leader) { ?>
        <?php $leader_account = $this->user_model->get_account_by_id($leader['account_key']); ?>
        <?php $leader_user = $this->user_model->get_user($leader_account['user_key']); ?>
            <tr>
                <td><?php echo $rank; ?></td>
                <td>
                    <span class="glyphicon glyphicon-user" aria-hidden="true" 
                    style="color: <?php echo $leader_account['primary_color']; ?>"> </span>
                    <?php echo $leader_user['username']; ?>
                </td>
                <td><a class="leaderboard_land_link" href="<?=base_url()?>world/<?php echo $world['id']; ?>/?land=<?php echo $leader['coord_slug']; ?>">
                    <?php echo $leader['land_name']; ?>
                </a></td>
                <td><a class="leaderboard_land_link" href="<?=base_url()?>world/<?php echo $world['id']; ?>/?land=<?php echo $leader['coord_slug']; ?>">
                    $<?php echo number_format($leader['price']); ?>
                </a></td>
                <td><?php echo mb_substr(strip_tags($leader['content']), 0, 42); if (strlen(strip_tags($leader['content'])) > 42) { echo '...'; } ?></td>
            </tr>
        <?php $rank++; ?>
        <?php } ?>
        </table>

    </div>

	<!-- jQuery -->
	<script src="<?=base_url()?>resources/jquery/jquery-1.11.1.min.js"></script>
	<script src="<?=base_url()?>resources/bootstrap/js/bootstrap.min.js"></script>

    <!-- Loading Overlay -->
    <script>
        loading = function() {
            // add the overlay with loading image to the page
            var over = '<div id="overlay"><p>Loading...</p></div>';
            $(over).appendTo('body');
        };
        loading();
    </script>
    
	<!-- Master Script -->
    <script>
// 
// Constants
// 

// Set World
var world_key = <?php echo $world['id']; ?>;

var latest_rebate = <?php echo $world['latest_rebate']; ?>;

var land_tax_rate = <?php echo $world['land_tax_rate']; ?>;

// Size of land box squares
var land_size = <?php echo $world['land_size'] ?>;

// Set user variables
<?php if ($log_check) { ?>
    var log_check = true;
    var user_id = <?php echo $user_id + ''; ?>;
    var account_id = <?php echo $account['id'] + ''; ?>;
    var username = "<?php echo $user['username']; ?>";
    var cash = <?php echo $account['cash'] + ''; ?>;
<?php } else { ?>
    var log_check = false;
<?php } ?>

function initMap() 
{
    // Map options
    var map = new google.maps.Map(document.getElementById('map'), {
        // Zoom on land if set as parameter
        <?php if ( isset($_GET['land']) ) { 
        $land_coords_split = explode(',', $_GET['land']); ?>
        // Logic to center isn't fully understand, but results in correct behavior in all 4 corners
        center: {lat: <?php echo $land_coords_split[0] + ($world['land_size'] / 2); ?>, lng: <?php echo $land_coords_split[1] - ($world['land_size'] / 2); ?>},
        zoom: 6,
        <?php } else { ?>
        center: {lat: 20, lng: 0},
        zoom: 3,
        <?php } ?>
        minZoom: 3,
        maxZoom: 10,
        mapTypeId: google.maps.MapTypeId.TERRAIN 
        // mapTypeId: google.maps.MapTypeId.HYBRID 
    });

    <?php
    // Javascript Land Array no longer used
    // $js_lands = json_encode($lands);
    // echo "var lands = ". $js_lands . ";\n";
    ?>

	// 
	// Functions
	// 

    // Declare square
    function declare_square(land_lat, land_lng, stroke_weight, stroke_color, fill_color, fill_opacity) {
        shape = [
            {lat: land_lat, lng: land_lng},
            {lat: land_lat + land_size, lng: land_lng},
            {lat: land_lat + land_size, lng: land_lng - land_size},
            {lat: land_lat, lng: land_lng - land_size}
        ];
        box = new google.maps.Polygon({
          map: map,
          paths: shape,
          strokeWeight: stroke_weight,
          strokeColor: stroke_color,
          fillColor: fill_color,
          fillOpacity: fill_opacity,
        });
        box.setMap(map);
        box.addListener('click', set_window);
        infoWindow = new google.maps.InfoWindow;
    }

	// Set land window
	function set_window(event) {
		// Set Parameters
        // Not entirely sure why I have to subtract land_size on lat for this to work, but results in correct behavior in all 4 corners
		var lat = round_down(event.latLng.lat()) - land_size;
		var lng = round_down(event.latLng.lng());
		var coord_slug = lat + ',' + lng;
        console.log(event.latLng.lat() + ',' + event.latLng.lng());
        console.log(coord_slug);
		// Get land_data
		land = get_single_land(coord_slug, world_key, function(land){
      console.log(land);
  		land_data = JSON.parse(land);
      if (land_data['error']) {
        console.log(land_data['error']);
        return false;
      }
      console.log(land_data['token']);
			// Create string
      var content_string = '<div class="land_window">';
			if (land_data['claimed'] === '0') {
				content_string += '<strong>Unclaimed</strong><br><br>';
			} else  {
        // Calculate income
        income_prefix = '';
        income_class = 'green_money';
        income = Math.floor(parseFloat(latest_rebate - (land_data['price'] * land_tax_rate)));
        if (income < 0) {
          income_prefix = '-';
          income_class = 'red_money';
          income = Math.abs(income);
        }
        content_string += '<div class="land_window"><a href="<?=base_url()?>world/' + world_key + '?land=' + coord_slug + '"><strong>' 
        + land_data['land_name'] + '</strong></a><br>'
        + 'Owned by <strong>' + land_data['username'] + '</strong><br>'
        + 'Estimated Income: <strong class="' + income_class + '">'  + income_prefix + '$' + money_format(income) + '/Hour</strong><br>'
        + '' + land_data['content'] + '<br>';
			}
      if (! log_check) {
        if (land_data['claimed'] === '0') {
          content_string += '<a class="register_to_play btn btn-default" href="<?=base_url()?>world/' + world_key 
          + '?register">Join to Claim!</a><br>';
        } else {
          content_string += '<a class="register_to_play btn btn-default" href="<?=base_url()?>world/' + world_key 
          + '?register">Join to Buy! (' + money_format(land_data['price']) + ')</a><br>';
        }
      }
			if (log_check) {
				// 
				// Abstract to be shorter
				// 
				if (land_data['claimed'] === '0') {
					content_string += land_update_form('claim', 'btn-action', land_data);
				} else if (land_data['account_key'] == account_id) {
					content_string += land_update_form('update', 'btn-info', land_data);
				} else {
					if (land_data['price'] < cash)
					{
						content_string += land_update_form('buy', 'btn-success', land_data);
					} else {
						content_string += '<button class="btn btn-default" disabled="disabled">Not enough cash (' + money_format(land_data['price']) + ')</button>';
					}
				}
			}
            // debug coord_slug
			// content_string += 'Coord Key: ' + land_data['coord_slug'] + ' | ' + coord_slug +
            // '<br>Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() + '<br>';
			content_string += '</div>';
			// Set InfoWindow Interaction
			infoWindow.setContent(content_string);
			infoWindow.setPosition(event.latLng);
			infoWindow.open(map);
		});
	}

	// For claiming, updating, and buying land forms
	function land_update_form(form_type, button_class, d) {
		result = '<form action="<?=base_url()?>land_form" method="post"><button class="expand_land_form btn ' + button_class + '" type="button" '
		+ 'data-toggle="collapse" data-target="#land_form" aria-expanded="false" aria-controls="land_form">'
		  + '' + ucwords(form_type) + ' This Land';
		if (form_type != 'claim') {
			result += ' ($' + money_format(d['price']) + ')';
		}
		result += ' <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button><br><br>'
		+ '<div id="land_form" class="collapse">'
          + '<div class="form-group">'
            + '<input type="hidden" id="input_form_type" name="form_type_input" value="' + form_type + '">'
            + '<input type="hidden" id="input_world_key" name="world_key_input" value="' + world_key + '">'
            + '<input type="hidden" id="input_coord_slug" name="coord_slug_input" value="' + d['coord_slug'] + '">'
            + '<input type="hidden" id="input_lng" name="lng_input" value="' + d['lng'] + '">'
            + '<input type="hidden" id="input_lat" name="lat_input" value="' + d['lat'] + '">'
            + '<input type="hidden" id="token" name="token" value="' + d['token'] + '">'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="input_land_name">Name</label>'
            + '</div><div class="col-md-8">'
            + '<input type="text" class="form-control" id="input_land_name" name="land_name" placeholder="Land Name" value="' + d['land_name'] + '">'
            + '</div></div>'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="input_price">Price</label>'
            + '</div><div class="col-md-8">'
            + '<input type="text" class="form-control" id="input_price" name="price" value="' + money_format(d['price']) + '">'
            + '</div></div>'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="input_content">Description</label>'
            + '</div><div class="col-md-8">'
            + '<textarea class="form-control" id="input_content" name="content" placeholder="Description">' + d['content'] + '</textarea>'
            + '</div></div>'
          + '</div>'
          + '<button type="submit" id="submit_land_form" class="btn btn-primary form-control">' + ucwords(form_type) + '</button>'
		+ '</div></form>';
		return result;
	}

	// Get single land ajax
	function get_single_land(coord_slug, world_key, callback) {
		$.ajax({
			url: "<?=base_url()?>get_single_land",
			type: "GET",
			data: { 
                coord_slug: coord_slug,
                world_key: world_key 
            },
			cache: false,
			success: function(html)
			{
				callback(html);
				return true;
			}
		});
	}

	// Uppercase words
	function ucwords (str) {
	    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
	        return $1.toUpperCase();
	    });
	}

	// For money formatting
	function money_format(nStr) {
	    nStr += '';
	    x = nStr.split('.');
	    x1 = x[0];
	    x2 = x.length > 1 ? '.' + x[1] : '';
	    var rgx = /(\d+)(\d{3})/;
	    while (rgx.test(x1)) {
	            x1 = x1.replace(rgx, '$1' + ',' + '$2');
	    }
	    return x1 + x2;
	}

	// For rounding land coords
	function round_down(n) {
		if (n > 0) {
	        return Math.ceil(n/land_size) * land_size;
		}
	    else if ( n < 0) {return Math.ceil(n/land_size) * land_size;
	    }
	    else {
	        return 0;
	    }
	}

	// 
	// Land loop
	// 

	<?php // This foreach loop runs between 3000 to 60000 times, so be as dry as possible here, no comments
    foreach ($lands as $land) { 
        $stroke_weight = 0.2; 
        $stroke_color = '#222222';
        $fill_color = "#FFFFFF";
        $fill_opacity = '0';
        if ($log_check && $land['account_key'] === $account['id']) { 
            $stroke_weight = 5; 
            $stroke_color = '#428BCA';
        }
        if ($land['claimed']) {
          $fill_color = $land['primary_color'];
          $fill_opacity = '0.4';
        }
        ?>
        declare_square(<?php echo 
            $land['lat'] . ',' .
            $land['lng'] . ',' .
            $stroke_weight . ',' .
            '"' . $stroke_color . '"' . ',' .
            '"' . $fill_color . '"' . ',' .
            $fill_opacity; ?>);
	<?php } ?>

	// 
	// Map Styling
	// 

	// Optional Styling of map
	var styles = [
	  {
		featureType: "poi.business",
		elementType: "labels",
		stylers: [
		  { visibility: "off" }
		]
	  }
	];

	var styled_map = new google.maps.StyledMapType(styles,
	  {name: "Styled Map"});
	map.mapTypes.set('map_style', styled_map);
	map.setMapTypeId('map_style');

    // 
    // Remove overlay
    // 

    // Remove loading overlay based on idle status
    google.maps.event.addListenerOnce(map, 'idle', function(){
    });
    // Remove loading overlay based on tiles loaded status
    google.maps.event.addListenerOnce(map, 'tilesloaded', function(){
        $('#overlay').fadeOut();
    });
}

// 
// User Controls
// 

// Show error block if errors exist
<?php if ($failed_form === 'error_block') { ?>
	$('#error_block').show();
<?php } ?>

// Show how to play after registering
<?php if ($just_registered) { ?>
$('#how_to_play_block').show();
<?php } ?>

if (!log_check) {
    $('#register_block').show();
}

$('.login_button').click(function(){
	$('.center_block').hide();
	$('#login_block').show();
});

$('.register_button').click(function(){
	$('.center_block').hide();
	$('#register_block').show();
});

$('.how_to_play_button').click(function(){
	$('.center_block').hide();
	$('#how_to_play_block').show();
});

$('.about_button').click(function(){
	$('.center_block').hide();
	$('#about_block').show();
});

$('.report_bugs_button').click(function(){
	$('.center_block').hide();
	$('#report_bugs_block').show();
});

$('.exit_center_block').click(function(){
	$('.center_block').hide();
});

$('.login_button').click(function(){
	$('#login_input_username').focus();
});

$('.register_button').click(function(){
	$('#register_input_username').focus();
});

$('.sold_lands_button').click(function(){
  $('#recently_sold_lands_block').show();
});

$('#leaderboard_net_value_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_net_value_block').show();
});
$('#leaderboard_land_owned_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_land_owned_block').show();
});
$('#leaderboard_cash_owned_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_cash_owned_block').show();
});
$('#leaderboard_highest_valued_land_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_highest_valued_land_block').show();
});
$('.leaderboard_cheapest_land_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_cheapest_land_block').show();
});

if (window.location.href.indexOf('register') >= 0) {
    $('#register_block').show();
}

// Stop dropdown closing when clicking color input
$('#account_input_primary_color').click(function(e) {
    e.stopPropagation();
});

// Validation errors shown on page load if exist
<?php if ($failed_form === 'login') { ?>
$('#login_block').show();
<?php } else if ($failed_form === 'register') { ?> 
$('#register_block').show();
<?php } ?>

	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD_lT8RkN6KffGEfJ3xBcBgn2VZga-a05I&callback=initMap&signed_in=true" async defer>
	</script>
  </body>
</html>