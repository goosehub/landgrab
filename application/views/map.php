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
	  	display: none;
	  	position: absolute;
		top: 0.5em;
		right: 6em;
		opacity: 0.9;
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
	  .land_form textarea {
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
    	<button class="cash_display btn btn-default">$<?php echo number_format($cash); ?>.00</button>
		<button class="user_button btn btn-primary dropdown-toggle" type="button" id="user_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<?php echo $username; ?>
		  <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="user_dropdown">
		  <!-- <li><a href="#">Profile</a></li> -->
		  <li><a href="#">How To Play</a></li>
		  <li><a href="#">About The Site</a></li>
		  <li><a href="#">Report Bugs</a></li>
		  <li role="separator" class="divider"></li>
		  <li><a class="logout_button btn btn-default" href="<?=base_url()?>user/logout">Log Out</a></li>
		</ul>
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
		// Set Parameters
		lat = event.latLng.lat();
		lng = event.latLng.lng();
		lat = round_down(lat);
		lng = round_down(lng);
		var coord_key = lat + '|' + lng;
		// Get land_data
		land = get_single_land(coord_key, function(land){
			land_data = JSON.parse(land);
			// View
			// Create string

			if (land_data['claimed'] === '0') {
				content_string = '<div class="land_window"><strong>Unclaimed</strong><br>';
			} else  {
				var content_string = '<div class="land_window"><strong>' + land_data['land_name'] + '</strong><br>' + land_data['content'] + '<br>';
			}
			if (log_check) {
				// 
				// Abstract to be shorter
				// 
				if (land_data['claimed'] === '0') {
					content_string += land_update_form('claim', 'btn-action', land_data);
				} else if (land_data['user_key'] == user_id) {
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
		return '<button class="' + form_type + '_land btn ' + button_class + '" type="button" '
		+ 'data-toggle="collapse" data-target="#' + form_type + '_collapse" aria-expanded="false" aria-controls="' + form_type + '_collapse">'
		  + '' + ucwords(form_type) + ' This Land'
		+ '</button><br><br>'
		+ '<div class="land_form collapse" id="' + form_type + '_collapse">'
          + '<div class="form-group">'
            + '<input type="hidden" id="' + form_type + '_input_form_type" name="form_type_input" value="' + form_type + '">'
            + '<input type="hidden" id="' + form_type + '_input_coord_key" name="coord_key_input" value="' + d['coord_key'] + '">'
            + '<input type="hidden" id="' + form_type + '_input_lng" name="lng_input" value="' + d['lng'] + '">'
            + '<input type="hidden" id="' + form_type + '_input_lat" name="lat_input" value="' + d['lat'] + '">'
            + '<input type="text" class="form-control" id="' + form_type + '_input_land_name" name="land_name" placeholder="Land Name" value="' + d['land_name'] + '">'
            + '<input type="text" class="form-control" id="' + form_type + '_input_price" name="price" value="Price: $' + d['price'] + '">'
            + '<textarea class="form-control" id="' + form_type + '_input_content" name="content" placeholder="Description">' + d['content'] + '</textarea>'
            + '<strong>Color:</strong> <input type="color" class="" id="' + form_type + '_input_primary_color" name="primary_color" value="' + d['primary_color'] + '">'
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

	var log_check = <?php echo $log_check; ?>;
	var user_id = <?php echo $user_id; ?>;
	var username = '<?php echo $username; ?>';
	var cash = <?php echo $cash; ?>;

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