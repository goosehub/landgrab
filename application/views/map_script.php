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
var account = JSON.parse('<?php echo json_encode($account); ?>');
var player_land_count = <?php echo $account['land_count']; ?>;
<?php } else { ?>
var log_check = false;
<?php } ?>

land_dictionary = new Array();
land_dictionary[0] = create_land_prototype('unclaimed', 0, 0, 0, 0, 0);
land_dictionary[1] = create_land_prototype('village', 1, 1, 0, 0, 0, 0);
land_dictionary[2] = create_land_prototype('town', 2, 10, 0, 0, 0, 0);
land_dictionary[3] = create_land_prototype('city', 3, 100, 0, 0, 0, 0);
land_dictionary[4] = create_land_prototype('metropolis', 4, 1000, 0, 0, 0, 0);
land_dictionary_length = Object.keys(land_dictionary).length;

function create_land_prototype(slug, defense, population, gdp, treasury, military, support) {
  var object = new Object();
  object.slug = slug,
  object.name = name,
  object.defense = defense,
  object.population = population,
  object.gdp = gdp,
  object.treasury = treasury,
  object.support = support
  return object;
}

// Set maps variables
var map_update_interval = <?php echo $update_timespan; ?>;
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
			if (land_data['land_type'] === '0') {
        // Land name
				window_string += '<strong class="land_name">Unclaimed</strong><br>';
        // Coord
        window_string += 'Coord: <strong class="pull-right"><a href="<?=base_url()?>world/' + world_key + '?land=' + coord_slug + '">' + coord_slug + '</a></strong><br>';

      // Claimed land
			} else  {

        // Land name
        if (land_data['land_name'] != '') {
          window_string += '<strong class="land_name h3">' + land_data['land_name'] + '</strong>';
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
        window_string += 'Land: <strong class="text-success pull-right">' + ucwords(land_dictionary[land_data['land_type']].slug) + '</strong><br>';
        // Population
        window_string += 'Population: <strong class="text-success pull-right">' + number_format(land_dictionary[land_data['land_type']].population * 1000) + '</strong><br>';
        // Defense
        window_string += 'Defense: <strong class="text-danger pull-right">' + number_format(land_dictionary[land_data['land_type']].defense) + '</strong>';

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
				if (land_data['land_type'] === '0') {
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
          $('.land_form_cont').hide();
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
                    fillColor: account['color'],
                    fillOpacity: 0.4
                  });

                  // Tutorial Rule
                  if (account['tutorial'] < 2) {
                    $('#tutorial_block').fadeOut(1000, function(){
                      $('#tutorial_block').fadeIn();
                      $('#tutorial_title').html('We The People');
                      $('#tutorial_text').html('Pick a form of Government, set a tax rate, and balance your budget');
                    });
                  }

                  return true;
                }
              }
            } // End land form ajax success
          }); // End land form ajax
        }); //End submit form

        $('.upgrade_submit').click(function(){
          // Serialize form into post data
          $('#land_upgrade_form').append('<input type="hidden" name="upgrade_type" value="' + $(this).val() + '"/>')
          var upgrade_type = $(this).val();
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

              if (response['error']) {
                $('#land_upgrade_form').html('<br><div class="alert alert-wide alert-danger"><strong>' + response['error'] + '</strong></div>');
                return false;
              }
              if (response['status'] != 'success') {
                $('#land_upgrade_form').html('<br><div class="alert alert-wide alert-danger"><strong>' + response['message'] + '</strong></div>');
                return false;
              }

              // If success
              if (response['status'] === 'success') {
                infoWindow.close();
              }

              if (upgrade_type === 'unclaimed') {
                  // Update box to reflect user ownership
                  boxes[land_data['id']].setOptions({
                    strokeWeight: 0, 
                    strokeColor: '#000000',
                    fillColor: '#000000',
                    fillOpacity: 0
                  });
              }

            }
          });
        });

      }); // End infoWindow script domready listener
    }); // End get_single_land callback
	} // End set_window

	// For claiming, updating, and buying land forms
	function land_window_form(form_type, button_class, d) {
    var hide_class = '';
    if (form_type != 'update') {
      hide_class = 'hidden';
    }
    var action_class = '';
    if (form_type === 'update') {
      action_class = 'btn-primary';
    } else if (form_type === 'attack') {
      action_class = 'btn-danger';
    } else {
      action_class = 'btn-info';
    }
		result = '<div class="form_outer_cont land_form_cont"><hr><form id="land_form' + '" action="<?=base_url()?>land_form" method="post">'
		  result += '<div id="land_form_dropdown">'
          + '<div class="form-group ' + hide_class + '">'
            + '<input type="hidden" id="input_form_type" name="form_type_input" value="' + form_type + '">'
            + '<input type="hidden" id="input_world_key" name="world_key_input" value="' + world_key + '">'
            + '<input type="hidden" id="input_id" name="id_input" value="' + d['id'] + '">'
            + '<input type="hidden" id="input_coord_slug" name="coord_slug_input" value="' + d['coord_slug'] + '">'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="input_land_name">Land Name</label>'
            + '</div><div class="col-md-8">'
            + '<input type="text" class="form-control" id="input_land_name" name="land_name" placeholder="Land Name" value="'+ d['land_name'] + '">'
            + '</div></div>'
            + '<div class="row"><div class="col-md-3">'
            + '<label for="input_content">Description</label>'
            + '</div><div class="col-md-8">'
            + '<textarea class="form-control" id="input_content" name="content" placeholder="Description">' + d['content'] + '</textarea>'
            + '</div></div>'
          + '</div>';

          // Language adjustment for Tutorial
          if (account['tutorial'] < 2) {
            form_type = 'Build Your Capitol';
          }

          result += '<button type="button" id="submit_land_form" class="btn + ' + action_class + ' form-control">' + ucwords(form_type) + '</button>';
		result += '</div></form></div>';
		return result;
	}

  function upgrade_form(d) {
    // Start form
    result = '';
    result += '<div class="form_outer_cont upgrade_parent"><form id="land_upgrade_form" action="<?=base_url()?>land_upgrade_form" method="post">'
      + '<input type="hidden" name="world_key_input" value="' + d.world_key + '"/>'
      + '<input type="hidden" name="coord_slug_input" value="' + d.coord_slug + '"/>';

    // Upgrade
    if (d.land_type === '1' || false) {
      upgrade_type = 'town';
    } else if (d.land_type === '2') {
      upgrade_type = 'city';
    } else if (d.land_type === '3') {
      upgrade_type = 'metropolis';
    }
    if (d.land_type != '4') {
      result += '<button type="button" name="upgrade_type" class="upgrade_submit btn btn-primary" '
        + 'value="' + d.land_type + '">Make Into ' + ucwords(upgrade_type) + '</button>';
    }

    // Capitol
    if (parseInt(d.land_type) > 1 && d.capitol === '0') {
      result += '<button type="button" name="upgrade_type" value="capitol" class="upgrade_submit btn btn-success">Make Into Nation Capitol</button>'
    }

    // Unclaim
    result += '<button type="button" name="upgrade_type" value="-1" class="upgrade_submit btn btn-danger" '
      + 'value="0">Unclaim this land</button>';

    // End form
    result += '</form></div>';

    return result;
  }

  function land_type_info(land_type) {
    result = '<div class="expand_land_type_info btn btn-info" type="button" '
    + 'data-toggle="collapse" data-target="#' + land_type + '_info_dropdown" aria-expanded="false" aria-controls="' + land_type + '_info_dropdown">'
      + 'Info'
      + ' <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></div>'
        + '<div id="' + land_type + '_info_dropdown" class="info_details_parent collapse">';
        result += 'Defense: <span class="pull-right"><strong class="text-primary">+' + land_dictionary[land_type].defense + '</strong></span><br>';
        result += 'Population: <span class="pull-right"><strong class="text-success">+' + land_dictionary[land_type].population + '</strong>'
        + ' / -<strong class="text-danger">' + land_dictionary[land_type].population + '</strong></span><br>';
    result += '</div>';
    return result;
  }

	// 
	// Land loop
	// 

	<?php // This foreach loop runs 15,000 times, so bandwidth is key
    foreach ($lands as $land) { 
        $stroke_weight = 0.2; 
        $stroke_color = '#222222';
        $fill_color = "#FFFFFF";
        $fill_opacity = '0';
        if ($land['land_type'] != 0) {
          $fill_color = $land['color'];
          $fill_opacity = '0.4';
        }
        if ($log_check && $land['account_key'] === $account['id']) {
            $stroke_color = '#428BCA';
        }
        if ($land['land_type'] === 'fortification') {
          $stroke_weight = 2;
          $stroke_color = '#585858';
        } else if ($land['land_type'] === 'stronghold') {
          $stroke_weight = 2;
          $stroke_color = '#F72525';
        } else if ($land['land_type'] === 'city') {
          $stroke_weight = 2;
          $stroke_color = '#2D882D';
        } else if ($land['land_type'] === 'town') {
          $stroke_weight = 2;
          $stroke_color = '#F7DB25';
        } else if ($land['land_type'] === 'market') {
          $stroke_weight = 2;
          $stroke_color = '#911BA2';
        }
        if ($log_check && $land['account_key'] === $account['id']) { 
            $stroke_weight = 3;
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
    // This loop may run up to 15,000 times, so focus is performance
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
      if (log_check && land['account_key'] == account_id) {
        stroke_color = '#428BCA';
      }
      if (land['land_type'] === 'fortification') {
        stroke_weight = 2;
        stroke_color = '#585858';
      } else if (land['land_type'] === 'stronghold') {
        stroke_weight = 2;
        stroke_color = '#F72525';
      } else if (land['land_type'] === 'city') {
        stroke_weight = 2;
        stroke_color = '#2D882D';
      } else if (land['land_type'] === 'town') {
        stroke_weight = 2;
        stroke_color = '#F7DB25';
      } else if (land['land_type'] === 'market') {
        stroke_weight = 2;
        stroke_color = '#911BA2';
      }
      if (log_check && land['account_key'] == account_id) {
        stroke_weight = 3;
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
  }

  function update_leaderboards(leaderboards) {
    // Set leaderboards
    leaderboard_land_owned = leaderboards['leaderboard_land_owned'];
    leaderboard_cities = leaderboards['leaderboard_cities'];
    leaderboard_strongholds = leaderboards['leaderboard_strongholds'];
    leaderboard_army = leaderboards['leaderboard_army'];
    leaderboard_population = leaderboards['leaderboard_population'];
    // Empty current leaderboards
    $('#leaderboard_land_owned_table').find('tr:gt(0)').remove();
    $('#leaderboard_cities_table').find('tr:gt(0)').remove();
    $('#leaderboard_strongholds_table').find('tr:gt(0)').remove();
    $('#leaderboard_army_table').find('tr:gt(0)').remove();
    $('#leaderboard_population_table').find('tr:gt(0)').remove();

    // 
    // Add updated rows to leaderboards
    // 

    // leaderboard_land_owned
    $.each(leaderboard_land_owned, function(index, leader) {
      var table_string = '<tr><td>' + leader['rank'] + '</td>'
            + '<td><span class="glyphicon glyphicon-user" aria-hidden="true" style="color: ' + leader['color'] + '"></span>'
            + '' + leader['user']['username'] + ' </td>'
            + '<td>' + leader['total'] + '</td>'
            + '<td>' + leader['land_mi'] + ' Mi&sup2; | ' + leader['land_km'] + ' KM&sup2;</td></tr>';

      // Add string to table
      $('#leaderboard_land_owned_table tr:last').after(table_string);
    });
    // leaderboard_cities
    $.each(leaderboard_cities, function(index, leader) {
      var table_string = '<tr><td>' + leader['rank'] + '</td>'
            + '<td><span class="glyphicon glyphicon-user" aria-hidden="true" style="color: ' + leader['color'] + '"></span>'
            + '' + leader['user']['username'] + ' </td>'
            + '<td>' + leader['total'] + '</td>'

      // Add string to table
      $('#leaderboard_cities_table tr:last').after(table_string);
    });
    // leaderboard_strongholds
    $.each(leaderboard_strongholds, function(index, leader) {
      var table_string = '<tr><td>' + leader['rank'] + '</td>'
            + '<td><span class="glyphicon glyphicon-user" aria-hidden="true" style="color: ' + leader['color'] + '"></span>'
            + '' + leader['user']['username'] + ' </td>'
            + '<td>' + leader['total'] + '</td>'

      // Add string to table
      $('#leaderboard_strongholds_table tr:last').after(table_string);
    });
    // leaderboard_army
    $.each(leaderboard_army, function(index, leader) {
      var table_string = '<tr><td>' + leader['rank'] + '</td>'
            + '<td><span class="glyphicon glyphicon-user" aria-hidden="true" style="color: ' + leader['color'] + '"></span>'
            + '' + leader['user']['username'] + ' </td>'
            + '<td>' + leader['army'] + '</td>'

      // Add string to table
      $('#leaderboard_army_table tr:last').after(table_string);
    });
    // leaderboard_population
    $.each(leaderboard_population, function(index, leader) {
      var table_string = '<tr><td>' + leader['rank'] + '</td>'
            + '<td><span class="glyphicon glyphicon-user" aria-hidden="true" style="color: ' + leader['color'] + '"></span>'
            + '' + leader['user']['username'] + ' </td>'
            + '<td>' + leader['population'] + '</td>'

      // Add string to table
      $('#leaderboard_population_table tr:last').after(table_string);
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