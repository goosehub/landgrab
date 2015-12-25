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
	  	background-color: hsl(41, 100%, 49%) !important;
	  	background-repeat: repeat-x;
	  	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffcc60", endColorstr="#f9aa00");
	  	background-image: -khtml-gradient(linear, left top, left bottom, from(#ffcc60), to(#f9aa00));
	  	background-image: -moz-linear-gradient(top, #ffcc60, #f9aa00);
	  	background-image: -ms-linear-gradient(top, #ffcc60, #f9aa00);
	  	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffcc60), color-stop(100%, #f9aa00));
	  	background-image: -webkit-linear-gradient(top, #ffcc60, #f9aa00);
	  	background-image: -o-linear-gradient(top, #ffcc60, #f9aa00);
	  	background-image: linear-gradient(#ffcc60, #f9aa00);
	  	border-color: #f9aa00 #f9aa00 hsl(41, 100%, 44%);
	  	color: #333 !important;
	  	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.33);
	  	-webkit-font-smoothing: antialiased;
	  }

	  /* Map */
	  #map {
		height: 100%;
	  }

	  /* Top right block*/
	  #top_right_block {
	  	display: none;
	  	position: absolute;
		top: 0.5em;
		right: 6em;
		opacity: 0.8;
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

	  /* Overlay */
	  #overlay {
	      position: absolute;
	      left: 0;
	      top: 0;
	      bottom: 0;
	      right: 0;
	      background: #000;
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
		<button class="user_button btn btn-primary"><?php echo $username; ?></button>
    	<a class="logout_button btn btn-default" href="<?=base_url()?>user/logout">Log Out</a>
	    <?php } else { ?>
    	<button class="login_button btn btn-info">Login</button>
    	<button class="register_button btn btn-action">Register</button>
	    <?php } ?>
    </div>

    <!-- Center Blocks -->

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
    	  <button type="submit" class="btn btn-action form-control">Register</button>
	    </form>
    </div>

	<!-- jQuery -->
	<script src="resources/jquery/jquery-1.11.1.min.js"></script>
	<script src="resources/bootstrap/js/bootstrap.min.js"></script>

	<!-- Master Script -->
	<script>
// Loading Overlay
loading = function() {
    // add the overlay with loading image to the page
    var over = '<div id="overlay"></div>';
    $(over).appendTo('body');
};
loading();

function initMap() 
{

	// 
	// Functions
	// 

	// Set land window
	function set_window(event) {
		lat = event.latLng.lat();
		lng = event.latLng.lng();
		lat = round_down(lat);
		lng = round_down(lng);
		var coord_key = lat + '|' + lng;
		land = get_single_land(coord_key, function(land){
			var claimed = land[0];
			var user_key = land[1];
			var land_name = land[2];
			var price = land[3];
			var content = land[4];
			// View
			var content_string = '<div class="land_window"><strong>' + land_name + '</strong><br>' + content + '<br>';
			if (log_check) {
				// 
				// Abstract to be shorter
				// 
				if (claimed === '0') {
					content_string += land_update_form('claim', 'btn-action', coord_key);
				} else if (user_key == user_id) {
					content_string += land_update_form('update', 'btn-info', coord_key);
				} else {
					content_string += land_update_form('buy', 'btn-success', coord_key);
				}
			}
			// content_string += 'Coord Key: ' + coord_key + '<br>Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() + '<br>';
			content_string += '</div>';
			// Set InfoWindow Interaction
			infoWindow.setContent(content_string);
			infoWindow.setPosition(event.latLng);
			infoWindow.open(map);
		});
	}

	// For claiming, updating, and buying land forms
	function land_update_form(form_type, button_class, coord_key) {
		return '<button class="' + form_type + '_land btn ' + button_class + '" coord_key="' + coord_key + '" type="button" '
		+ 'data-toggle="collapse" data-target="#' + form_type + '_collapse" aria-expanded="false" aria-controls="' + form_type + '_collapse">'
		  + '' + ucwords(form_type) + ' This Land'
		+ '</button><br>'
		+ '<div class="collapse" id="' + form_type + '_collapse">'
          + '<div class="form-group">'
            + '<label for="input_land_name">Land Name</label>'
            + '<input type="land_name" class="form-control" id="' + form_type + '_input_land_name" name="land_name" placeholder="Land Name">'
          + '</div>'
          + '<button type="submit" class="btn btn-primary form-control">' + ucwords(form_type) + '</button>'
		+ '</div>';
	}

	// Uppercase words
	function ucwords (str) {
	    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
	        return $1.toUpperCase();
	    });
	}

	// Get single land ajax
	function get_single_land(coord_key, callback) {
		$.ajax({
			url: "<?=base_url()?>get_single_land",
			type: "GET",
			data: { coord_key: coord_key },
			cache: false,
			success: function(html)
			{
				var lands = html.split('|');
				callback(lands);
				return true;
			}
		});
	}

	// Claim land
	function claim_land(coord_key, user_key) {
		$.ajax({
			url: "<?=base_url()?>claim_land",
			type: "GET",
			data: { 
				coord_key: coord_key,
				user_key: user_id
			},
			cache: false,
			success: function(html)
			{
				return true;
			}
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
	        return Math.floor(n/box_size) * box_size;
		}
	    else if ( n < 0) {return Math.ceil(n/box_size) * box_size;
	    }
	    else {
	        return box_size;
	    }
	}

	// 
	// Get Lands
	// 

	<?php
	$js_lands = json_encode($lands);
	echo "var lands = ". $js_lands . ";\n";
	?>

	// 
	// Pass Session Variables
	// 

	var username = '<?php echo $username; ?>';
	var user_id = <?php echo $user_id; ?>;
	var log_check = <?php echo $log_check; ?>;

	// 
	// Map options
	// 

	var map = new google.maps.Map(document.getElementById('map'), {
		// Starting center
		center: {lat: 20, lng: 0},

		// Default zoom and limits
		zoom: 3,
		minZoom: 3,
		maxZoom: 10,

		// Prevent panning and zooming
		// draggable: false,
		// scrollwheel: false,
		// panControl: false,

		// Map type
		mapTypeId: google.maps.MapTypeId.TERRAIN 
		// mapTypeId: google.maps.MapTypeId.HYBRID 
	});

	// Size of land box squares
	var box_size = 2;
	// Area covered defined with these limits
	// Must be evenly divisible by box_size
	// var x_limit = 180;
	// var y_limit = 84;
	// Box size 2 with lng limit of 180 and lat limit of 84 and box size of 2 creates 15120 land squares and covers the globe

	// 
	// Land loop
	// 

	var primary_lat = false;
	var primary_lng = false;
	var shape = false;

	<?php // No comments below because of performance ?>
	<?php foreach ($lands as $land) { ?> 
	    primary_lat = <?php echo $land['lat']; ?>;
	    primary_lng = <?php echo $land['lng']; ?>;

	    shape = [
	        {lat: primary_lat, lng: primary_lng},
	        {lat: primary_lat + box_size, lng: primary_lng},
	        {lat: primary_lat + box_size, lng: primary_lng - box_size},
	        {lat: primary_lat, lng: primary_lng - box_size}
	    ];

	    box = new google.maps.Polygon({
	      map: map,
	      paths: shape,
	      strokeWeight: 1,
	      strokeOpacity: 0.2,
	      <?php if ($land['claimed']) { ?>
	      fillOpacity: 0.3,
	      <?php } else { ?>
	      fillOpacity: 0,
	      <?php } ?>
	      fillColor: "#<?php echo $land['primary_color']; ?>",
	      strokeColor: "#<?php echo $land['secondary_color']; ?>"
	    });

	    box.setMap(map);
	    box.addListener('click', set_window);
	    infoWindow = new google.maps.InfoWindow;
	<?php } ?>

	// 
	// Map Styling
	// 

	// Optional Styling of map
	var styles = [
	  {
		featureType: "all",
		stylers: [
		  // { saturation: -80 }
		]
	  },{
		featureType: "road.arterial",
		elementType: "geometry",
		stylers: [
		  { hue: "#00ffee" },
		  // { saturation: 50 }
		]
	  },{
		featureType: "poi.business",
		elementType: "labels",
		stylers: [
		  { visibility: "off" }
		]
	  }
	];

	var styledMap = new google.maps.StyledMapType(styles,
	  {name: "Styled Map"});

	map.mapTypes.set('map_style', styledMap);
	map.setMapTypeId('map_style');

	// 
	// Game Controls
	// 

	// Claim Land
	// $('body').delegate('.claim_land_submit', 'click', function(){
		// var coord_key = $(this).attr('coord_key');
		// claim_land(coord_key, user_id);
	// });
}

// Remove loading overlay
setTimeout(function(){
	$('#overlay').fadeOut();
	$('#top_right_block').fadeIn();
}, 100);

// 
// User Controls
// 

$('.login_button').click(function(){
	$('.center_block').hide();
	$('#login_block').show();
});

$('.register_button').click(function(){
	$('.center_block').hide();
	$('#register_block').show();
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