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
var land_tax_rate = <?php echo $world['land_tax_rate']; ?>;
var latest_rebate = <?php echo $world['latest_rebate']; ?>;
var world_claim_fee = <?php echo $world['claim_fee']; ?>;
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
		  + '' + ucwords(form_type) + ' This Land ($' + money_format(d['price']) + ')'
		  + ' <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button><br><br>'
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

<?php if ($failed_form != 'login') { ?>
  if (!log_check) {
    $('#register_block').show();
  }
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

$('.sold_lands_button').click(function(){
  $('#recently_sold_lands_block').show();
});

$('.market_order_button').click(function(){
  $('#market_order_block').show();
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
<!-- Footer -->
  </body>
</html>