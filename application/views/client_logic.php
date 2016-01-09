<!-- jQuery -->
<script src="<?=base_url()?>resources/jquery/jquery-1.11.1.min.js"></script>
<!-- Bootstrap -->
<script src="<?=base_url()?>resources/bootstrap/js/bootstrap.min.js"></script>

<!-- Loading Overlay -->
<script>
  loading = function() {
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

// Set maps variables
var infoWindow = false;
var boxes = [];

function initMap() 
{
  // 
  // Map options
  // 

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

	// 
	// Minor Functions
	// 

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

  // Declare square called by performance sensitive loop
  function declare_square(land_key, land_lat, land_lng, stroke_weight, stroke_color, fill_color, fill_opacity) {
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
      boxes[land_key] = box;
  }

	// Set land window
	function set_window(event) {
  	// Set Parameters
    // Not sure why subtracting land_size on lat makes this work, but results in correct behavior
		var lat = round_down(event.latLng.lat()) - land_size;
		var lng = round_down(event.latLng.lng());
		var coord_slug = lat + ',' + lng;
    // console.log(event.latLng.lat() + ',' + event.latLng.lng());

    // 
		// Create land infoWindow
    // 

		land = get_single_land(coord_slug, world_key, function(land){
      // Get land
      // console.log(land);
  		land_data = JSON.parse(land);
      // Handle error
      if (land_data['error']) {
        alert(land_data['error']);
        return false;
      }
      // Create string
      var window_string = '<div class="land_window">';

      // Unclaimed land
			if (land_data['claimed'] === '0') {
        // Land name
				window_string += '<strong class="land_name">Unclaimed</strong><br>';
        // Coord
        window_string += 'Coord: <strong class="pull-right"><a href="<?=base_url()?>world/' + world_key + '?land=' + coord_slug + '">' + coord_slug + '</a></strong><br>';

      // Claimed land
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

        // Land name
        if (land_data['land_name'] != '') {
          window_string += '<strong class="land_name">' + land_data['land_name'] + '</strong>';
        }
        // Content
        if (land_data['content'] != '') {
          window_string += '<div class="land_content_div">' + land_data['content'] + '</div><br>';
        }
        // Owner
        window_string += 'Owner: <strong class="pull-right">' + land_data['username'] + '</strong><br>';
        // Coord
        window_string += 'Coord: <strong class="pull-right"><a href="<?=base_url()?>world/' + world_key + '?land=' + coord_slug + '">' + coord_slug + '</a></strong><br>';
        // Income
        window_string += 'Income: <strong class="' + income_class + ' pull-right">'  + income_prefix + '$' + money_format(income) + '/Hour</strong><br>';
			}
      window_string += '<br>';

      // Unregistered users
      if (! log_check) {
        if (land_data['claimed'] === '0') {
          window_string += '<a class="register_to_play btn btn-default" href="<?=base_url()?>world/' + world_key 
          + '?register">Join to Claim!</a><br>';
        } else {
          window_string += '<a class="register_to_play btn btn-default" href="<?=base_url()?>world/' + world_key 
          + '?register">Join to Buy! (' + money_format(land_data['price']) + ')</a><br>';
        }
      }

      // Interaction buttons
			if (log_check) {
        // Claim
				if (land_data['claimed'] === '0') {
					window_string += land_update_form('claim', 'btn-action', land_data);
        // Update
				} else if (land_data['account_key'] == account_id) {
					window_string += land_update_form('update', 'btn-info', land_data);
        // Buy
				} else {
          // Enough cash to buy
					if (land_data['price'] < cash) {
						window_string += land_update_form('buy', 'btn-success', land_data);
          // Not enough cash
					} else {
						window_string += '<button class="btn btn-default" disabled="disabled">Not enough cash (' + money_format(land_data['price']) + ')</button>';
					}
				}
      }
      // debug coord_slug
      // window_string += 'Coord Key: ' + land_data['coord_slug'] + ' | ' + coord_slug +
            // '<br>Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() + '<br>';
      // End div
      window_string += '</div>';

      // 
      // Set InfoWindow Interaction
      // 

      // Close window if one is open
      if (infoWindow) {
          infoWindow.close();
      }
      // Set new infoWindow
      infoWindow = new google.maps.InfoWindow;
      infoWindow.setContent(window_string);
      infoWindow.setPosition(event.latLng);
      infoWindow.open(map);

      // 
      // infoWindow script
      // 

      google.maps.event.addListener(infoWindow,'domready',function(){
        // Focus on land name on expanding form, timeout to prevent collapse conflict
        $('.expand_land_form').click(function(){
          setTimeout(function(){
            $('#input_land_name').focus();
          }, 200);
        });

        // 
        // Submit form ajax
        // 
        $('#submit_land_form').click(function() {

          // Serialize form into post data
          var post_data = $('#land_form').serialize();

          // Replace window with processing window
          $('.form_outer_cont').html('<br><div class="alert alert-wide alert-green"><strong>Success</strong></div>');

          // Submit form
          $.ajax({
            url: "<?=base_url()?>land_form",
            type: "POST",
            data: post_data,
            cache: false,
            success: function(data)
            {
              // console.log(data);
              // Return data
              response = JSON.parse(data);

              // If success, close
              if (response['status'] === 'success') {
                infoWindow.close();
                // $('.land_window').html('<br><div class="alert alert-wide alert-green"><strong>success</strong></div>');
                // setTimeout(function(){
                // infoWindow.close();
                // }, 800);

                // Update box to reflect user ownership
                boxes[land_data['id']].setOptions({
                  strokeWeight: 5, 
                  strokeColor: '#428BCA',
                  fillColor: '#<?php echo $account["primary_color"]; ?>',
                  fillOpacity: 0.4
                });
                // console.log(boxes);
                return true;

              // If error, display error message
              } else {
                $('.land_window').html('<br><div class="alert alert-wide alert-danger"><strong>' + response['message'] + '</strong></div>');
                return false;
              }
            }
          });
        }); // End land form ajax

      }); // End infoWindow script domready listener

    }); // End get_single_land callback

	} // End set_window

	// For claiming, updating, and buying land forms
	function land_update_form(form_type, button_class, d) {
		result = '<div class="form_outer_cont"><form id="land_form' + '" action="<?=base_url()?>land_form" method="post">'
    + '<button class="expand_land_form btn ' + button_class + '" type="button" '
		+ 'data-toggle="collapse" data-target="#land_form_dropdown" aria-expanded="false" aria-controls="land_form_dropdown">'
		  + '' + ucwords(form_type) + ' This Land ($' + money_format(d['price']) + ')'
		  + ' <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button><br><br>'
		    + '<div id="land_form_dropdown" class="collapse">'
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
          + '<button type="button" id="submit_land_form" class="btn btn-primary form-control">' + ucwords(form_type) + '</button>'
		+ '</div></form></div>';
		return result;
	}

	// 
	// Land loop
	// 

	<?php // This foreach loop runs between 400 to 60,000 times, so it's as dry as possible here, no comments
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
            $land['id'] . ',' .
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

  // Apply map styling
	var styled_map = new google.maps.StyledMapType(styles,
	  {name: "Styled Map"});
	map.mapTypes.set('map_style', styled_map);
	map.setMapTypeId('map_style');

  // 
  // Remove overlay
  // 

  // Remove loading overlay based on tiles loaded status
  google.maps.event.addListenerOnce(map, 'tilesloaded', function(){
      $('#overlay').fadeOut();
  });
  // Remove loading overlay based on idle status
  // google.maps.event.addListenerOnce(map, 'idle', function(){
  // });
}

// 
// Interface functions
// 

// Error reporting
<?php if ($failed_form === 'error_block') { ?>
	$('#error_block').show();
<?php } ?>

// Show how to play after registering
<?php if ($just_registered) { ?>
$('#how_to_play_block').show();
<?php } ?>

// Show register form if not logged in and not failed to log in
<?php if ($failed_form != 'login') { ?>
  if (!log_check) {
    $('#register_block').show();
  }
<?php } ?>

// Validation errors shown on page load if exist
<?php if ($failed_form === 'login') { ?>
$('#login_block').show();
<?php } else if ($failed_form === 'register') { ?> 
$('#register_block').show();
<?php } ?>

// Show register if server passes register to url
if (window.location.href.indexOf('register') >= 0) {
    $('#register_block').show();
}

// Stop dropdown closing when clicking color input
$('#account_input_primary_color').click(function(e) {
    e.stopPropagation();
});

// 
// Center block hide and show logic
// 

$('.exit_center_block').click(function(){
  $('.center_block').hide();
});
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
$('.login_button').click(function(){
	$('#login_input_username').focus();
});
$('.register_button').click(function(){
	$('#register_input_username').focus();
});
$('.sold_lands_button').click(function(){
  $('.center_block').hide();
  $('#recently_sold_lands_block').show();
});
$('.market_order_button').click(function(){
  $('.center_block').hide();
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

// 
// Preset Logic
// 

function set_market_order_preset(latmin, latmax, lngmin, lngmax) {
  $('#min_lat_input').val(latmin);
  $('#max_lat_input').val(latmax);
  $('#min_lng_input').val(lngmin);
  $('#max_lng_input').val(lngmax);
}

$('#north_america_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#south_america_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#europe_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#africa_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#russia_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#asia_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#middle_east_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#australia_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});

</script>

<!-- Google Maps Script -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD_lT8RkN6KffGEfJ3xBcBgn2VZga-a05I&callback=initMap&signed_in=true" async defer>
</script>

<!-- Footer -->
  </body>
</html>