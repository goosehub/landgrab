<!DOCTYPE html>
<html>
  <head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<meta charset="utf-8">

	<title>Land</title>

	<!-- Bootstrap -->
	<link href="<?=base_url()?>resources/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">

	<!-- Custom Fonts -->
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

	<style>
	  /* Global */
	  html, body {
		height: 100%;
		margin: 0;
		padding: 0;
		font-family: "Lato";
	  }
	  /* Orange Action Bootstrap-Styled Button */
	  .btn-action {   
	  	background-color: hsl(44, 100%, 56%) !important; 
	  	background-repeat: repeat-x; 
	  	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffc31e", endColorstr="#ffc31e"); 
	  	background-image: -khtml-gradient(linear, left top, left bottom, from(#ffc31e), to(#ffc31e)); 
	  	background-image: -moz-linear-gradient(top, #ffc31e, #ffc31e); 
	  	background-image: -ms-linear-gradient(top, #ffc31e, #ffc31e); 
	  	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffc31e), color-stop(100%, #ffc31e)); 
	  	background-image: -webkit-linear-gradient(top, #ffc31e, #ffc31e); 
	  	background-image: -o-linear-gradient(top, #ffc31e, #ffc31e); 
	  	background-image: linear-gradient(#ffc31e, #ffc31e); 
	  	border-color: #ffc31e #ffc31e hsl(44, 100%, 56%); 
	  	color: #333 !important; 
	  	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.00); 
	  	-webkit-font-smoothing: antialiased;
	  }
	  .btn-action:hover {
	  	background-color: hsl(38, 100%, 54%) !important; 
	  	background-repeat: repeat-x; 
	  	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffa814", endColorstr="#ffa814"); 
	  	background-image: -khtml-gradient(linear, left top, left bottom, from(#ffa814), to(#ffa814)); 
	  	background-image: -moz-linear-gradient(top, #ffa814, #ffa814); 
	  	background-image: -ms-linear-gradient(top, #ffa814, #ffa814); 
	  	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffa814), color-stop(100%, #ffa814)); 
	  	background-image: -webkit-linear-gradient(top, #ffa814, #ffa814); 
	  	background-image: -o-linear-gradient(top, #ffa814, #ffa814); 
	  	background-image: linear-gradient(#ffa814, #ffa814); 
	  	border-color: #ffa814 #ffa814 hsl(38, 100%, 54%); 
	  	color: #333 !important; 
	  	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.00); 
	  	-webkit-font-smoothing: antialiased;
	  }

	  /* Map */
	  #map {
		height: 100%;
	  }

	  /* Top right block*/
	  #top_right_block {
	  	position: absolute;
		top: 0.5em;
		right: 6em;
		opacity: 0.9;
	  }
      #cash_display {
        opacity: 1;
      }
      .leaderboard_link {
        cursor: pointer;
      }

	  /* Center Block */
	  .center_block {
	  	display: none;
	  	position: absolute;
		top: 30vh;
		left: 30%;
		width: 40%;
		background: #fff;
		padding: 1em;
		border-radius: 1em;
	  }
	  .center_block strong {
	  	font-size: 1.4em;
	  }
	  .exit_center_block {
	  	float: right;
	  }

	  /* Land Form */
	  #land_form .row {
	  	margin-right: 0;
	  }
	  #land_form textarea {
	  	height: 3em;
	  }

	  /* Overlay */
	  #overlay {
	      position: absolute;
	      left: 0;
	      top: 0;
	      bottom: 0;
	      right: 0;
	      background: #000;
          opacity: 0.9;
	  }
      #overlay p {
        font-size: 2em;
        color: #222;
        position: absolute;
        text-align: center;
        top: 30vh;
        left: 40%;
        width: 20%;
        background: #fff;
        padding: 0.5em;
      }
	  #loading {
	      width: 50px;
	  }
	</style>
  </head>
  <body>

  	<!-- Map Element -->
	<div id="map"></div>

	<!-- Top Right Block -->
	<div id="top_right_block">
		<?php if ($log_check) { ?>
    	<button id="cash_display" disabled="disabled" class="btn btn-default">$<?php echo number_format($account['cash']); ?>.00</button>
        <div class="btn-group">
    		<button class="user_button btn btn-primary dropdown-toggle" type="button" id="user_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    			<?php echo $user['username']; ?>
    		  <span class="caret"></span>
    		</button>
    		<ul class="dropdown-menu" aria-labelledby="user_dropdown">
    		  <li><a class="logout_button btn btn-default" href="<?=base_url()?>user/logout">Log Out</a></li>
              <li role="separator" class="divider"></li>
              <li>
                  <?php echo form_open('account/update_color'); ?>
                  <div class="row"><div class="col-md-3">
                      <label for="_input_primary_color">Color</label>
                  </div><div class="col-md-9">
                      <input type="hidden" name="world_key_input" value="<?php echo $world['id']; ?>">
                      <input type="color" class="form-control" id="account_input_primary_color" name="primary_color" value="<?php echo $account['primary_color']; ?>">
                  </div></div>
                  <li role="separator" class="divider"></li>
                  <button type="submit" class="btn btn-primary form-control">Update</button>
                  </form>
              </li>
    		</ul>
        </div>
	    <?php } else { ?>
    	<button class="login_button btn btn-primary">Login</button>
    	<button class="register_button btn btn-action">Register</button>
	    <?php } ?>
        <div class="btn-group">
            <button class="info_button btn btn-info dropdown-toggle" type="button" id="info_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                LandGrab
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="info_dropdown">
              <li><a class="how_to_play_button btn btn-default">How To Play</a></li>
              <li><a class="about_button btn btn-default">About LandGrab</a></li>
              <li><a class="report_bugs_button btn btn-default">Report Bugs</a></li>
              <li role="separator" class="divider"></li>
              <li class="text-center"><strong>Leaderboards</strong></li>
              <li><a id="leaderboard_net_value_button" class="leaderboard_link">Net Value</a></li>
              <li><a id="leaderboard_land_owned_button" class="leaderboard_link">Land</a></li>
              <li><a id="leaderboard_cash_owned_button" class="leaderboard_link">Cash</a></li>
              <li><a id="leaderboard_highest_valued_land_button" class="leaderboard_link">Highest Valued Land</a></li>
              <li><a id="leaderboard_cheapest_land_button" class="leaderboard_link">Cheapest Land</a></li>
              <li role="separator" class="divider"></li>
              <li class="text-center"><strong>Worlds</strong></li>
              <?php foreach ($worlds as $world_list) { ?>
              <li><a class="world_link" href="<?=base_url()?>world/<?php echo $world_list['slug']; ?>">
                  <strong><?php echo $world_list['slug']; ?></strong>
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
    </div>

    <!-- Register Block -->
    <div id="register_block" class="center_block">
    	<strong>Register</strong>

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
    	  <button type="submit" class="btn btn-action form-control">Register To Play</button>
	    </form>
    </div>

    <!-- How To Play Block -->
    <div id="how_to_play_block" class="center_block">
    	<strong>How To Play</strong>

    	<button type="button" class="exit_center_block btn btn-default btn-sm">
    	  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    	</button>
        
        <hr>
        <p>
            LandGrab is a game of Claiming, Buying, and Selling Land.
            You can aim to accumulate the most wealth, to own the most land, or to own famous areas like New York, Paris, or Tel-Aviv.
        </p>
        <p>
            You start the game with $1,000,000.
            You can claim any unowned land for free.
            You must set a price on land you own.
            You will have to pay a tax of 1% on the price you set each hour.
            Every hour, the taxes gets distributed among the land owners based on amount of land owned.
            When you run out of cash, you lose.
        </p>
        <p>
            It is important not to set your land prices so high you lose cash, yet not so low you lose land. Finding undervalued land, and selling it for a profit is key.
        </p>
    </div>

    <!-- About Block -->
    <div id="about_block" class="center_block">
    	<strong>About LandGrab</strong>

    	<button type="button" class="exit_center_block btn btn-default btn-sm">
    	  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    	</button>
        
        <hr>
        <p>LandGrab is a game developed by Goose. Developed in PHP with CodeIgniter 3.</p>
        <strong> <a href="http://gooseweb.io/" target="_blank">gooseweb.io</a></strong>
        <br>
        <p>You can contribute to this project on Github</p>
        <strong> <a href="http://github.com/goosehub/landgrab/" target="_blank">github.com/goosehub/landgrab</a></strong>
        <br>
        <p>Special Thanks goes to Google Maps, EllisLabs, <a href="http://ithare.com/" target="_blank">itHare</a>, and all of my users</p>
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

    <!-- Leaderboards -->

    <!-- Leaderboard net_value Block -->
    <div id="leaderboard_net_value_block" class="center_block">
        <strong>net_value_block</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <hr>

    </div>

    <!-- Leaderboard land_owned Block -->
    <div id="leaderboard_land_owned_block" class="center_block">
        <strong>land_owned_block</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <hr>

    </div>

    <!-- Leaderboard cash_owned Block -->
    <div id="leaderboard_cash_owned_block" class="center_block">
        <strong>cash_owned_block</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <hr>

    </div>

    <!-- Leaderboard highest_valued_land Block -->
    <div id="leaderboard_highest_valued_land_block" class="center_block">
        <strong>highest_valued_land_block</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <hr>

    </div>

    <!-- Leaderboard cheapest_land Block -->
    <div id="leaderboard_cheapest_land_block" class="center_block">
        <strong>cheapest_land_block</strong>

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <hr>

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

function initMap() 
{
    // 
    // Constants
    // 

    // Set World
    var world_key = <?php echo $world['id']; ?>

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

    // Map options
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 20, lng: 0},
        zoom: 3,
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
		var lat = round_down(event.latLng.lat());
		var lng = round_down(event.latLng.lng());
		var coord_key = lat + ',' + lng;
		// Get land_data
		land = get_single_land(coord_key, world_key, function(land){
			land_data = JSON.parse(land);
			// Create string
            var content_string = '<div class="land_window">';
            if (land_data['claimed'] === '0' && log_check) {
                // 
            }
			else if (land_data['claimed'] === '0') {
				content_string += '<strong>Unclaimed</strong><br>';
			} else  {
				content_string += '<div class="land_window"><strong>' + land_data['land_name'] + '</strong><br>'
				+ 'Owned by <strong>' + land_data['username'] + '</strong><br>'
				+ '' + land_data['content'] + '<br>';
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
						content_string += '<button class="btn btn-default" disabled="disabled">Not enough cash to buy</button>';
					}
				}
			}
			// content_string += 'Coord Key: ' + land_data['coord_key'] + '<br>Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() + '<br>';
			content_string += '</div>';
			// Set InfoWindow Interaction
			infoWindow.setContent(content_string);
			infoWindow.setPosition(event.latLng);
			infoWindow.open(map);
		});
	}

	// For claiming, updating, and buying land forms
	function land_update_form(form_type, button_class, d) {
        $('#' + form_type + '_input_land_name').focus();
		result = '<form action="<?=base_url()?>land_form" method="post"><button class="' + form_type + '_land btn ' + button_class + '" type="button" '
		+ 'data-toggle="collapse" data-target="#land_form" aria-expanded="false" aria-controls="land_form">'
		  + '' + ucwords(form_type) + ' This Land';
		if (form_type === 'buy') {
			result += ' ($' + money_format(d['price']) + ')';
		}
		result += '</button><br><br>'
		+ '<div id="land_form" class="collapse">'
          + '<div class="form-group">'
            + '<input type="hidden" id="' + form_type + '_input_form_type" name="form_type_input" value="' + form_type + '">'
            + '<input type="hidden" id="' + form_type + '_input_world_key" name="world_key_input" value="' + world_key + '">'
            + '<input type="hidden" id="' + form_type + '_input_coord_key" name="coord_key_input" value="' + d['coord_key'] + '">'
            + '<input type="hidden" id="' + form_type + '_input_lng" name="lng_input" value="' + d['lng'] + '">'
            + '<input type="hidden" id="' + form_type + '_input_lat" name="lat_input" value="' + d['lat'] + '">'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="' + form_type + '_input_land_name">Name</label>'
            + '</div><div class="col-md-8">'
            + '<input type="text" class="form-control" id="' + form_type + '_input_land_name" name="land_name" placeholder="Land Name" value="' + d['land_name'] + '">'
            + '</div></div>'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="' + form_type + '_input_price">Price</label>'
            + '</div><div class="col-md-8">'
            + '<input type="text" class="form-control" id="' + form_type + '_input_price" name="price" value="' + money_format(d['price']) + '">'
            + '</div></div>'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="' + form_type + '_input_content">Description</label>'
            + '</div><div class="col-md-8">'
            + '<textarea class="form-control" id="' + form_type + '_input_content" name="content" placeholder="Description">' + d['content'] + '</textarea>'
            + '</div></div>'
          + '</div>'
          + '<button type="submit" id="submit_land_form" class="btn btn-primary form-control">' + ucwords(form_type) + '</button>'
		+ '</div></form>';
		return result;
	}

	// Get single land ajax
	function get_single_land(coord_key, world_key, callback) {
		$.ajax({
			url: "<?=base_url()?>get_single_land",
			type: "GET",
			data: { 
                coord_key: coord_key,
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
	        return Math.floor(n/land_size) * land_size;
		}
	    else if ( n < 0) {return Math.ceil(n/land_size) * land_size;
	    }
	    else {
	        return land_size;
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
        $fill_opacity = '0.1';
        if ($log_check && $land['account_key'] === $account['id']) { 
            $stroke_weight = 3; 
            $stroke_color = '#428BCA';
        }
        if ($land['claimed']) {
          $fill_color = $land['primary_color'];
          $fill_opacity = '0.5';
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
	// Game Controls
	// 

    // ...

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
$('#leaderboard_cheapest_land_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_cheapest_land_block').show();
});

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