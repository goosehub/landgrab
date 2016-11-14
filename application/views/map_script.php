<!-- jQuery -->
<script src="<?=base_url()?>resources/jquery/jquery-1.11.1.min.js"></script>
<!-- Bootstrap -->
<script src="<?=base_url()?>resources/bootstrap/js/bootstrap.min.js"></script>
<!-- Map Script -->
<script>

// 
// Constants
// 

land_dictionary = new Array();
land_dictionary[0] = 'unclaimed';
land_dictionary[1] = 'village';
land_dictionary[2] = 'town';
land_dictionary[3] = 'city';
land_dictionary[4] = 'metropolis';

government_dictionary = new Array();
government_dictionary[0] = 'Anarchy';
government_dictionary[1] = 'Democracy';
government_dictionary[2] = 'Oligarchy';
government_dictionary[3] = 'Autocracy';

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

    $('.center_block').hide();

		land = get_single_land(coord_slug, world_key, function(land){
      // Get land
  		d = JSON.parse(land);
      // Handle error
      if (d['error']) {
        alert(d['error']);
        return false;
      }

      console.log('marco');

      $('#land_block').show();
      $('#land_form_result').hide();
      $('.land_form_subparent').hide();
      $('#land_form_submit_claim, #land_form_submit_attack').hide();

      if (!log_check) {
        $('#join_to_play_button').show();
      }

      if (d['land_type'] === '0') {
        $('#land_form_unclaimed_parent').show();
      } else {
        $('#land_form_info_parent').show();
      }

      // Own
      if (log_check && d['account_key'] === account['id']) {
        $('#land_form_update_parent').show()
        if (d['land_type'] > 1) {
          $('#land_form_upgrade_parent').show();
        }
      } else if (log_check) {
        // Attack
        $('#land_form_update_parent').hide()
        if (d['in_range']) {
          if (d['land_type'] === '0') {
            $('#land_form_submit_claim').show();
          }
          else {
            $('#land_form_submit_attack').show();
          }
        }
      }

      if (d['capitol'] === '1') {
        $('#capitol_info').show();
      } else {
        $('#capitol_info').hide();
      }
      $('#input_id').val(d['id']);
      $('#input_coord_slug').val(d['coord_slug']);
      if (d['land_name'] != '') {
        $('#land_name_label').html(d['land_name']);
      } else {
        $('#land_name_label').html('Unnamed ' + ucwords(land_dictionary[d['land_type']]));
      }
      $('#land_content_label').html(d['content']);
      $('#land_gdp_label').html(d['sum_effects']['gdp']);
      $('#land_population_label').html(d['sum_effects']['population']);
      $('#land_defense_label').html(d['sum_effects']['defense']);

      $('#coord_link').prop('href', '<?=base_url()?>world/' + world_key + '?land=' + coord_slug);
      $('#coord_link').html(coord_slug);
      

      // $('#input_land_content').addClass('input_to_label');
      // $('#input_land_name').addClass('input_to_label');

      $('#government_label').html( government_dictionary[d['account']['government']] );
      $('#land_type_label').html( ucwords(land_dictionary[d['land_type']]) );
      $('#nation_label').html(d['account']['nation_name']);
      $('#leader_name_label').html(d['account']['leader_name']);

      // $('#leader_name_label, #nation_label').css('color', d['color']);


      $('#input_land_name').val(d['land_name']);
      $('#input_content').val(d['content']);

      // Unbind last get_single_land click handler 
      $('#land_form_submit_claim, #land_form_submit_attack, #land_form_submit_update, #land_form_submit_upgrade').off('click');
      $('#land_form_submit_claim, #land_form_submit_attack, #land_form_submit_update, #land_form_submit_upgrade').click(function() {
        console.log('waldo');
        // Serialize form into post data
        $('#form_type_input').val( $(this).val() );
        var post_data = $('#land_form').serialize();

        // Tutorial
        if ( $(this).val() === 'update' || $.isNumeric( $(this).val() ) ) {
          console.log('next level of tutorial');
        }

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
              $('#land_form_result').show();
              $('#land_form_result_message').html(response['error']);
              return false;
            }

            // If success
            if (response['status'] === 'success') {

              // Pass information to user
              result_alert = 'alert-green';
              if (!response['result']) {
                result_alert = 'alert-danger';
              }
              $('#land_form_result_message').html(response['message']);
              $('.center_block').hide();

              if (input_form_type != 'update' && response['result']) {
                // Update player variables and displays
                // player_land_count = player_land_count + 1;
                // $('#owned_lands_span').html( number_format(player_land_count) );

                // Update box to reflect user ownership
                boxes[d['id']].setOptions({
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
      });

      

      return true;

      // 
      // infoWindow script
      // 
/*
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
*/

    }); // End get_single_land callback
	} // End set_window

	// 
	// Land loop
	// 

	<?php // This foreach loop runs 15,000 times, so bandwidth is key
    foreach ($lands as $land) { 
        $stroke_weight = 0.2; 
        $stroke_color = '#222222';
        $fill_color = "#FFFFFF";
        $fill_opacity = '0';
        if ($land['land_type'] > 0) {
          $fill_color = $land['color'];
          $fill_opacity = '0.4';
        }
        if ($log_check && $land['account_key'] === $account['id']) {
            $stroke_color = '#428BCA';
        }
        if ($land['capitol'] === '1') {
          $stroke_weight = 8;
          $fill_opacity = '0.8';
        } else if ($land['land_type'] === 1) {
          $stroke_weight = 2;
          $stroke_color = '#585858';
        } else if ($land['land_type'] === 2) {
          $stroke_weight = 2;
          $stroke_color = '#F72525';
        } else if ($land['land_type'] === 3) {
          $stroke_weight = 2;
          $stroke_color = '#2D882D';
        } else if ($land['land_type'] === 4) {
          $stroke_weight = 2;
          $stroke_color = '#F7DB25';
        } else if ($land['land_type'] === 5) {
          $stroke_weight = 2;
          $stroke_color = '#911BA2';
        }
        if ($log_check && $land['account_key'] === $account['id'] && $land['capitol'] != '1') { 
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
  // Awkward close to prevent unwanted white space

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
      if (land['land_type'] > 0) {
        fill_color = land['color'];
        fill_opacity = 0.4;
      }
      if (log_check && land['account_key'] == account_id) {
        stroke_color = '#428BCA';
      }
      if (land['land_type'] === 2) {
        stroke_weight = 2;
        stroke_color = '#2D882D';
      } else if (land['land_type'] === 3) {
        stroke_weight = 2;
        stroke_color = '#F7DB25';
      } else if (land['land_type'] === 4) {
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
    return true;
/*
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
*/
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