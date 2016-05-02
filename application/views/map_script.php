<!-- jQuery -->
<script src="<?=base_url()?>resources/jquery/jquery-1.11.1.min.js"></script>
<!-- Bootstrap -->
<script src="<?=base_url()?>resources/bootstrap/js/bootstrap.min.js"></script>
<!-- Map Script -->
<script>

// 
// Constants
// 

// Set World
var world_key = <?php echo $world['id']; ?>;
var land_size = <?php echo $world['land_size'] ?>;

// Set user variables
<?php if ($log_check) { ?>
    var log_check = true;
    var user_id = <?php echo $user_id + ''; ?>;
    var account_id = <?php echo $account['id'] + ''; ?>;
    var username = "<?php echo $user['username']; ?>";
    var account_color = '<?php echo $account["color"]; ?>';
    var active_army = <?php echo $account['active_army'] + ''; ?>;
    var passive_army = <?php echo $account['passive_army'] + ''; ?>;
    var player_land_count = <?php echo $account['land_count']; ?>;
<?php } else { ?>
    var log_check = false;
<?php } ?>

// Set defense dictionary
defense_dictionary = new Array();
defense_dictionary['unclaimed'] = 0;
defense_dictionary['village'] = 10;
defense_dictionary['wall'] = 50;
defense_dictionary['tower'] = 100;
defense_dictionary['fort'] = 1000;
defense_dictionary['castle'] = 10000;
defense_dictionary['city'] = 10000;

// Set land type cost dictionary
land_type_dictionary = new Array();
land_type_dictionary['village'] = 0;
land_type_dictionary['wall'] = 1;
land_type_dictionary['tower'] = 2;
land_type_dictionary['fort'] = 10;
land_type_dictionary['castle'] = 50;

// Set maps variables
var map_update_interval = 120 * 1000;
if (document.location.hostname == "localhost") {
  map_update_interval = 10 * 1000;
}
var infoWindow = false;
var boxes = [];

// Start initMap callback called from google maps script
function initMap() 
{
  // 
  // Map options
  // 

  var map = new google.maps.Map(document.getElementById('map'), {
      // Zoom on land if set as parameter
      <?php if ( isset($_GET['land']) ) { 
        $land_coords_split = explode(',', $_GET['land']); ?>

        // Logic to center isn't  understand, but results in correct behavior in all 4 corners
        center: {lat: <?php echo $land_coords_split[0] + ($world['land_size'] / 2); ?>, lng: <?php echo $land_coords_split[1] - ($world['land_size'] / 2); ?>},

        // Zoom should be adjusted based on box size
        zoom: 7,
      <?php } else { ?>

      // Map center is slightly north centric
      center: {lat: 20, lng: 0},
      // Zoom shows whole world but no repetition
      zoom: 3,
      <?php } ?>
      // Prevent seeing more than needed
      minZoom: 3,
      // Prevent excesssive zoom
      maxZoom: 10,
      // Map type
      mapTypeId: google.maps.MapTypeId.TERRAIN
      // mapTypeId: google.maps.MapTypeId.HYBRID
      // mapTypeId: google.maps.MapTypeId.SATELLITE
  });

	// 
	// Minor Functions
	// 

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

  // Uppercase words
  function ucwords (str) {
      return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
          return $1.toUpperCase();
      });
  }

  // For number formatting
  function number_format(nStr) {
      if (!nStr) {
        return 0;
      }
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
      success: function(data)
      {
        // console.log(data)
        callback(data);
        return true;
      }
    });
  }

  // Declare square called by performance sensitive loop
  function z(land_key, land_lat, land_lng, stroke_weight, stroke_color, fill_color, fill_opacity) {
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

        // Land name
        if (land_data['land_name'] != '') {
          window_string += '<strong class="land_name">' + land_data['land_name'] + '</strong><br>';
        }
        window_string += '<div class="land_info">';
        // Content
        if (land_data['content'] != '') {
          window_string += '<div class="land_content_div">' + land_data['content'] + '</div><br>';
        }
        // Owner
        window_string += 'Owner: <strong class="pull-right">' + land_data['username'] + '</strong><br>';
        // Coord
        window_string += 'Coord: <strong class="pull-right"><a href="<?=base_url()?>world/' + world_key + '?land=' + coord_slug + '">' + coord_slug + '</a></strong><br>';
        // Land Type
        window_string += 'Land: <strong class="pull-right">' + ucwords(land_data['land_type']) + '</strong><br>';
        // Defense
        window_string += 'Defense: <strong class="pull-right">' + number_format(defense_dictionary[land_data['land_type']]) + '</strong>';

        window_string += '</div>';
			}

      // Unregistered users
      if (! log_check) {
          window_string += '<a class="register_to_play btn btn-default" href="<?=base_url()?>world/' + world_key 
          + '?register">Join to Play!</a>';
      }

      // Interaction buttons
			if (land_data['in_range'] && log_check) {
        // Claim
				if (land_data['claimed'] === '0') {
					window_string += land_window_form('claim', 'btn-action', land_data);
        // Update
				} else if (land_data['account_key'] == account_id) {
          window_string += land_window_form('update', 'btn-info', land_data);
          window_string += '<br>';
          window_string += upgrade_form(land_data);
        // Buy
				} else {
          window_string += land_window_form('attack', 'btn-success', land_data);
				}
      }
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
        // When expanding form, hide expand button and Focus on land name, with timeout to prevent collapse conflict
        $('.expand_land_form').click(function(){
          $('.expand_land_form').hide();
          $('.land_info').hide();
          setTimeout(function(){
            $('#input_land_name').focus();
          }, 200);
        });

        // 
        // Submit land form ajax
        // 
        $('#submit_land_form').click(function() {

          // Serialize form into post data
          var input_form_type = $('#input_form_type').val();
          var post_data = $('#land_form').serialize();

          // Replace window with processing window
          $('#land_form').html('<br><div class="alert alert-wide alert-success"><strong>...</strong></div>');

          // Submit form
          $.ajax({
            url: "<?=base_url()?>land_form",
            type: "POST",
            data: post_data,
            cache: false,
            success: function(data)
            {
              // Return data
              response = JSON.parse(data);

              if (response['error'] || response['status'] != 'success') {
                $('#land_form').html('<br><div class="alert alert-wide alert-danger"><strong>' + response['error'] + '</strong></div>');
                return false;
              }

              // If success
              if (response['status'] === 'success') {

                // Pass information to user
                result_alert = 'alert-green';
                if (!response['result']) {
                  result_alert = 'alert-danger';
                  $('#active_army_display_span').html(0);
                  active_army = 0;
                }
                $('#land_form').html('<br><div class="alert alert-wide ' + result_alert + '"><strong>' + response['message'] + '</strong></div>');
                setTimeout(function(){
                  infoWindow.close();
                }, 1 * 500);

                if (input_form_type != 'update' && response['result']) {
                  // Update player variables and displays
                  player_land_count = player_land_count + 1;
                  $('#owned_lands_span').html( number_format(player_land_count) );

                  // Update box to reflect user ownership
                  boxes[land_data['id']].setOptions({
                    strokeWeight: 3, 
                    strokeColor: '#428BCA',
                    fillColor: account_color,
                    fillOpacity: 0.4
                  });
                  return true;
                }
              }
            } // End land form ajax success
          }); // End land form ajax
        }); //End submit form

        $('.upgrade_submit').click(function(){
          // Serialize form into post data
          $('#land_upgrade_form').append('<input type="hidden" name="upgrade_type" value="' + $(this).val() + '"/>')
          var post_data = $('#land_upgrade_form').serialize();

          // Replace window with processing window
          $('#land_upgrade_form').html('<br><div class="alert alert-wide alert-green"><strong>Upgrading</strong></div>');

          // Submit form
          $.ajax({
            url: "<?=base_url()?>land_upgrade_form",
            type: "POST",
            data: post_data,
            cache: false,
            success: function(data)
            {
              // Return data
              response = JSON.parse(data);

              if (response['error'] || response['status'] != 'success') {
                $('#land_upgrade_form').html('<br><div class="alert alert-wide alert-danger"><strong>' + response['error'] + '</strong></div>');
                return false;
              }

              // If success
              if (response['status'] === 'success') {
                infoWindow.close();
              }
            }
          });
        });

      }); // End infoWindow script domready listener
    }); // End get_single_land callback
	} // End set_window

	// For claiming, updating, and buying land forms
	function land_window_form(form_type, button_class, d) {
		result = '<div class="form_outer_cont"><form id="land_form' + '" action="<?=base_url()?>land_form" method="post">'
    + '<button class="expand_land_form expand_trade btn ' + button_class + ' form-control" type="button" '
		+ 'data-toggle="collapse" data-target="#land_form_dropdown" aria-expanded="false" aria-controls="land_form_dropdown">'
		  + '' + ucwords(form_type) + ' This Land';
		  result += ' <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>'
		    + '<div id="land_form_dropdown" class="collapse">'
          + '<div class="form-group">'
            + '<input type="hidden" id="input_form_type" name="form_type_input" value="' + form_type + '">'
            + '<input type="hidden" id="input_world_key" name="world_key_input" value="' + world_key + '">'
            + '<input type="hidden" id="input_id" name="id_input" value="' + d['id'] + '">'
            + '<input type="hidden" id="input_coord_slug" name="coord_slug_input" value="' + d['coord_slug'] + '">'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="input_land_name">Land Name</label>'
            + '</div><div class="col-md-8">'
            + '<input type="text" class="form-control" id="input_land_name" name="land_name" placeholder="Land Name" value="' + d['land_name'] + '">'
            + '</div></div>'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="input_content">Description</label>'
            + '</div><div class="col-md-8">'
            + '<textarea class="form-control" id="input_content" name="content" placeholder="Description">' + d['content'] + '</textarea>'
            + '</div></div>'
          + '</div>';
          result += '<button type="button" id="submit_land_form" class="btn btn-primary form-control">' + ucwords(form_type) + '</button>';
		result += '</div></form></div>';
		return result;
	}

  function upgrade_form(d) {
    result = '<div class="form_outer_cont upgrade_parent"><form id="land_upgrade_form" action="<?=base_url()?>land_upgrade_form" method="post">'
    + '<button class="expand_land_form expand_trade btn btn-success form-control" type="button" '
    + 'data-toggle="collapse" data-target="#upgrade_dropdown" aria-expanded="false" aria-controls="upgrade_dropdown">'
      + 'Upgrade This Land'
      + ' <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>'
        + '<div id="upgrade_dropdown" class="collapse">'
          + '<div class="form-group">'
            + '<strong class="h4">Avilable Upgrades</strong><br>'
            + '<input type="hidden" id="input_world_key" name="world_key_input" value="' + world_key + '">'
            + '<input type="hidden" id="input_id" name="id_input" value="' + d['id'] + '">'
            + '<input type="hidden" id="input_coord_slug" name="coord_slug_input" value="' + d['coord_slug'] + '">';
            if (active_army >= land_type_dictionary['village'] 
              && player_land_count >= parseInt(passive_army) + land_type_dictionary['village']
              ) {
              result += '<button type="button" class="upgrade_submit btn btn-default form-control" value="village">Village (0 Mobilized Army for 10 Defense)</button>';
            }
            if (active_army >= land_type_dictionary['wall'] 
              && player_land_count >= parseInt(passive_army) + land_type_dictionary['wall']
              ) {
              result += '<button type="button" class="upgrade_submit btn btn-default form-control" value="wall">Wall (1 Mobilized Army for  50 Defense)</button>';
            }
            if (active_army >= land_type_dictionary['tower'] 
              && player_land_count >= parseInt(passive_army) + land_type_dictionary['tower']
              ) {
            result += '<button type="button" class="upgrade_submit btn btn-default form-control" value="tower">Tower (2 Mobilized Army for 100 Defense)</button>';
            }
            if (active_army >= land_type_dictionary['fort'] 
              && player_land_count >= parseInt(passive_army) + land_type_dictionary['fort']
              ) {
            result += '<button type="button" class="upgrade_submit btn btn-default form-control" value="fort">Fort (10 Mobilized  Army for 1000 Defense)</button>';
            }
            if (active_army >= land_type_dictionary['castle'] 
              && player_land_count >= parseInt(passive_army) + land_type_dictionary['castle']
              ) {
            result += '<button type="button" class="upgrade_submit btn btn-default form-control" value="castle">Castle (50 Mobilized  Army for 10000 Defense)</button>';
            }
            // if (active_army >= land_type_dictionary['city'] 
            // && player_land_count >= parseInt(passive_army) + land_type_dictionary['city']
            // ) {
            // result += '<button type="button" class="upgrade_submit btn btn-default form-control" value="city">City</button>';
            // }
          result +=  '</div>';
    result += '</div></form></div>';
    return result;
  }

	// 
	// Land loop
	// 

	<?php // This foreach loop runs between 400 to 15,000 times, so it's as dry as possible here, no comments
    foreach ($lands as $land) { 
        $stroke_weight = 0.2; 
        $stroke_color = '#222222';
        $fill_color = "#FFFFFF";
        $fill_opacity = '0';
        if ($land['claimed']) {
          $fill_color = $land['color'];
          $fill_opacity = '0.4';
        }
        if ($land['land_type'] === 'wall') {
          $stroke_weight = 2;
          $stroke_color = '#613B1A';
        } else if ($land['land_type'] === 'tower') {
          $stroke_weight = 2;
          $stroke_color = '#2D882D';
        } else if ($land['land_type'] === 'fort') {
          $stroke_weight = 2;
          $stroke_color = '#AA9739';
        } else if ($land['land_type'] === 'castle') {
          $stroke_weight = 2;
          $stroke_color = '#AA3939';
        }
        if ($log_check && $land['account_key'] === $account['id']) { 
            $stroke_weight = 3; 
            $stroke_color = '#428BCA';
        }
        ?>z(<?php echo 
            $land['id'] . ',' .
            $land['lat'] . ',' .
            $land['lng'] . ',' .
            $stroke_weight . ',' .
            '"' . $stroke_color . '"' . ',' .
            '"' . $fill_color . '"' . ',' .
            $fill_opacity; ?>);<?php } ?>

	// 
	// Map Styling
	// 

	// Styling of map
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
  // Update army data
  // 

  // Get map update
  if (log_check) {
    setInterval(function(){
      get_army_update(account_id);
    }, 5 * 1000);
  }

  function get_army_update(account_id) {
    $.ajax({
      url: "<?=base_url()?>get_army_update",
      type: "GET",
      data: {
                account_id: account_id
            },
      cache: false,
      success: function(data)
      {
        $('#active_army_display_span').html(data);
        $('#active_army_span').html(data);
        active_army = parseInt(data);
      }
    });
  }

  // 
  // Update map data
  // 

  // Get map update
  setInterval(function(){
    get_map_update(world_key);
  }, map_update_interval);

  // Get single land ajax
  function get_map_update(world_key) {
    $.ajax({
      url: "<?=base_url()?>world/" + world_key,
      type: "GET",
      data: { 
                json: "true"
            },
      cache: false,
      success: function(data)
      {
        // console.log(data);
        data = JSON.parse(data);

        // Check for refresh signal from server 
        if (data['refresh']) {
          alert('The game is being updated, and we need to refresh your screen. This page will refresh after you press ok');
          window.location.reload();
        }

        update_lands(data['lands']);
        update_leaderboards(data['leaderboards']);

        if (log_check) {
          update_stats(data['account']);
        }

        console.log('update');

      }
    });
  }

  function update_lands(lands) {
    // Loop through lands
    // This loop may run as many as 15,000 times, so be performant
    number_of_lands = lands.length;
    for (i = 0; i < number_of_lands; i++) {

      // Set variables
      land = lands[i];
      stroke_weight = 0.2; 
      stroke_color = '#222222';
      fill_color = "#0000ff";
      fill_opacity = 0;
      if (land['claimed'] == 1) {
        fill_color = land['color'];
        fill_opacity = 0.4;
      }
      if (land['land_type'] === 'wall') {
        stroke_weight = 2;
        stroke_color = '#613B1A';
      } else if (land['land_type'] === 'tower') {
        stroke_weight = 2;
        stroke_color = '#2D882D';
      } else if (land['land_type'] === 'fort') {
        stroke_weight = 2;
        stroke_color = '#AA9739';
      } else if (land['land_type'] === 'castle') {
        stroke_weight = 2;
        stroke_color = '#AA3939';
      }
      if (log_check && land['account_key'] == account_id) {
        stroke_weight = 3;
        stroke_color = '#428BCA';
      }

      // Apply variables to box
      boxes[land['id']].setOptions({
        strokeWeight: stroke_weight, 
        strokeColor: stroke_color,
        fillColor: fill_color,
        fillOpacity: fill_opacity
      });

    }

    return true;
  }

  function update_stats(account) {
    $('#active_army_display_span').html(account['active_army']);
    $('#active_army_span').html(account['active_army']);
    $('#passive_army_span').html(account['passive_army']);
    $('#owned_lands_span').html(account['land_count']);
    active_army = account['active_army'];
    passive_army = account['passive_army'];
  }

  function update_leaderboards(leaderboards) {
    // Set leaderboards
    leaderboard_land_owned = leaderboards['leaderboard_land_owned'];
    // Empty current leaderboards
    $('#leaderboard_land_owned_table').find('tr:gt(0)').remove();

    // 
    // Add updated rows to leaderboards
    // 

    // leaderboard_land_owned
    $.each(leaderboard_land_owned, function(index, leader) {
      var table_string = '<tr><td>' + leader['rank'] + '</td>'
            + '<td><span class="glyphicon glyphicon-user" aria-hidden="true" style="color: ' + leader['color'] + '"></span>'
            + '' + leader['user']['username'] + ' </td>'
            + '<td>' + leader['COUNT(*)'] + '</td>'
            + '<td>' + leader['land_mi'] + ' Mi&sup2; | ' + leader['land_km'] + ' KM&sup2;</td></tr>';

      // Add string to table
      $('#leaderboard_land_owned_table tr:last').after(table_string);
    });

    return true;
  }

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

</script>