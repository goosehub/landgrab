<!-- Map Script -->
<script>
// 
// Constants
// 

land_dictionary = new Array();
land_dictionary[1] = 'unclaimed';
land_dictionary[2] = 'village';
land_dictionary[3] = 'town';
land_dictionary[4] = 'city';
land_dictionary[5] = 'metropolis';
land_dictionary[6] = 'fortification';
land_dictionary[10] = 'capitol';

land_type_key_dictionary = new Array();
land_type_key_dictionary['unclaimed'] = <?php echo $land_type_key_dictionary['unclaimed']; ?>;
land_type_key_dictionary['village'] = <?php echo $land_type_key_dictionary['village']; ?>;
land_type_key_dictionary['town'] = <?php echo $land_type_key_dictionary['town']; ?>;
land_type_key_dictionary['city'] = <?php echo $land_type_key_dictionary['city']; ?>;
land_type_key_dictionary['metropolis'] = <?php echo $land_type_key_dictionary['metropolis']; ?>;
land_type_key_dictionary['fortification'] = <?php echo $land_type_key_dictionary['fortification']; ?>;
land_type_key_dictionary['capitol'] = <?php echo $land_type_key_dictionary['capitol']; ?>;

government_dictionary = new Array();
government_dictionary[0] = 'Anarchy';
government_dictionary[1] = 'Democracy';
government_dictionary[2] = 'Oligarchy';
government_dictionary[3] = 'Autocracy';

// Set World
var world_key = <?php echo $world['id']; ?>;
var land_size = <?php echo $world['land_size'] ?>;

// Set user variables
var log_check = false;
var account = false;
<?php if ($log_check) { ?>
var log_check = true;
var user_id = <?php echo $user_id + ''; ?>;
var account_id = <?php echo $account['id'] + ''; ?>;
var username = "<?php echo $user['username']; ?>";
var account = JSON.parse('<?php echo addslashes(json_encode($account)); ?>');
var player_land_count = <?php echo $account['stats']['land_count']; ?>;
<?php } ?>

// Set maps variables
var map_update_interval = <?php echo $update_timespan; ?>;
var building_minimum = <?php echo $building_minimum; ?>;
var leaderboard_update_interval = <?php echo $leaderboard_update_interval_minutes; ?> * 60 * 1000;
var infoWindow = false;
var boxes = [];

// Key Press Tracker
var attack_key_pressed = false;
var keys = new Array();
keys['a'] = 65;
keys['t'] = 84;
$(document).keydown(function(event) {
  // Attack shortcut
  if (event.which == keys['a']) {
    attack_key_pressed = true;
  }
  // Disabled because of bugs
/*  // Chat shortcut
  if (event.which == keys['t']) {
    if (!$('#chat_input').is(':focus')) {
      $('#chat_input').focus();
      event.preventDefault();
    }
  }*/
});
$(document).keyup(function(event) {
  if (event.which == keys['a']) {
    attack_key_pressed = false;
  }
});

// Start initMap callback called from google maps script
function initMap() {
  // 
  // Map options
  // 

  var map = new google.maps.Map(document.getElementById('map'), {
    // Zoom on land if set as parameter
    <?php if ( isset($_GET['land']) ) { $land_coords_split = explode(',', $_GET['land']); ?>

    // Logic to center isn't understood, but results in correct behavior in all 4 corners
    center: {
      lat: <?php echo $land_coords_split[0] + ($world['land_size'] / 2); ?>,
      lng: <?php echo $land_coords_split[1] - ($world['land_size'] / 2); ?>
    },

    // Zoom should be adjusted based on box size
    zoom: 6,
    <?php } else { ?>

    // Map center is slightly north centric
    center: {
      lat: 20,
      lng: 0
    },
    // Zoom shows whole world but no repetition
    zoom: 3,
    <?php } ?>
    // Prevent seeing north and south edge
    minZoom: 2,
    // Prevent excesssive zoom
    // maxZoom: 10,
    // Map type
    // mapTypeId: google.maps.MapTypeId.TERRAIN
    // mapTypeId: google.maps.MapTypeId.HYBRID
    mapTypeId: google.maps.MapTypeId.SATELLITE
  });

  // Map Styling
  var styles = [{
    featureType: "poi.business",
    elementType: "labels",
    stylers: [{
      visibility: "off"
    }]
  }];

  // Apply map styling
  var styled_map = new google.maps.StyledMapType(styles, {
    name: "Styled Map"
  });
  map.mapTypes.set('map_style', styled_map);
  map.setMapTypeId('map_style');

  // 
  // Minor Functions
  // 

  // For rounding land coords
  function round_down(n) {
    if (n > 0) {
      return Math.ceil(n / land_size) * land_size;
    } else if (n < 0) {
      return Math.ceil(n / land_size) * land_size;
    } else {
      return 0;
    }
  }

  // Uppercase words
  function ucwords(str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function($1) {
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

  // Declare square called by performance sensitive loop
  function z(land_key, land_lat, land_lng, stroke_weight, stroke_color, fill_color, fill_opacity) {
    shape = [{
        lat: land_lat,
        lng: land_lng
      },
      {
        lat: land_lat + land_size,
        lng: land_lng
      },
      {
        lat: land_lat + land_size,
        lng: land_lng - land_size
      },
      {
        lat: land_lat,
        lng: land_lng - land_size
      }
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

  // 
  // Apply new budget
  // 
  
  $('#pass_new_budget_button').click(function(event) {
    $.ajax({
      url: "<?=base_url()?>budget_form",
      type: 'POST',
      dataType: 'json',
      data: $('#budget_form').serialize(),
      success: function(data) {
        // Handle error
        if (data['error']) {
          alert(data['error']);
          return false;
        }
        if (account && account['tutorial'] < 3) {
          $('#tutorial_block').fadeIn();
          $('#tutorial_title').html('Manifest Destiny');
          $('#tutorial_text').html('Conquer the world. War Weariness will accumulate as you acquire territory and will go down over time.');
          account['tutorial'] = 3;
        }
        // Do update
        get_map_update(world_key);
      }
    });
  });

  // Set land window
  function set_window(event) {
    // Set Parameters
    // Not sure why subtracting land_size on lat makes this work, but results in correct behavior
    var lat = round_down(event.latLng.lat()) - land_size;
    var lng = round_down(event.latLng.lng());
    var coord_slug = lat + ',' + lng;

    // 
    // If ctrl is active, just try to take it
    // 

    if (attack_key_pressed) {
      blind_land_attack(coord_slug, world_key, 'attack', function(response) {
        // If attack didn't work, try claim
        d = JSON.parse(response);
        if (d['error']) {
          blind_claim_land(coord_slug, world_key, 'claim', function(response) {
          });
        }

      });
      return true;
    }

    // 
    // Create land infoWindow
    // 

    $('.center_block').fadeOut(100);

    land = get_single_land(coord_slug, world_key, function(land) {
      // Get land
      d = JSON.parse(land);
      // console.log(d);

      // Handle error
      if (d['error']) {
        alert(d['error']);
        return false;
      }

      prepare_land_form_view(coord_slug, world_key, d);
      prepare_land_form_more_info(coord_slug, world_key, d);
      prepare_land_form_data(coord_slug, world_key, d);

      // Unbind the last click handler from get_single_land 
      $('#land_form_submit_claim, #land_form_submit_claim_tutorial, #land_form_submit_attack, #land_form_submit_attack_tutorial, #land_form_submit_update, #land_form_submit_upgrade, #build_embassy, #remove_embassy, .remove_building').off('click');

      $('#land_form_submit_claim, #land_form_submit_claim_tutorial, #land_form_submit_attack, #land_form_submit_attack_tutorial, #land_form_submit_update, #land_form_submit_upgrade, #build_embassy, #remove_embassy, .remove_building').click(function() {

        // Submit land ajax
        var form_type = $(this).val();
        land_form_ajax(form_type);

        // Do tutorial progression
        land_form_tutorial($(this).val());
      });

      return true;

    });
  }

  function blind_land_attack(coord_slug, world_key, type, callback) {
    $.ajax({
      url: "<?=base_url()?>land_form",
      type: "POST",
      data: {
        coord_slug_input: coord_slug,
        world_key_input: world_key,
        form_type_input: type
      },
      cache: false,
      success: function(data) {
        callback(data);
        return true;
      }
    });
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
      success: function(data) {
        // console.log(data);
        callback(data);
        return true;
      }
    });
  }

  // Prepare land form view
  function prepare_land_form_view(coord_slug, world_key, d) {
    $('#land_block').fadeIn(100);

    // Scroll to top and close open dropdowns
    $('#land_block').scrollTop(0);
    $('.in,.open').removeClass('in open');

    // Start by hiding everything
    $('.land_block_toggle').hide();
    $('#embassy_info_dropdown_button').hide();
    $('#build_embassy').hide();
    $('#remove_embassy').hide();
    $('#embassy_list_dropdown_button').hide();
    $('#land_form_support_too_low').hide();

    // Not logged in
    if (!log_check) {
      $('#join_to_play_button').show();
    }

    // Unclaimed
    if (d['land_type'] == land_type_key_dictionary['unclaimed']) {
      $('#land_form_unclaimed_parent').show();
      // Claimed
    } else {
      $('#land_form_info_parent').show();
      $('#land_form_more_info_parent').show();
    }

    // Is a town or larger and has more than it's land type as a modifier
    if (d['land_type'] > land_type_key_dictionary['village'] && d['land_type'] != land_type_key_dictionary['fortification'] && typeof d['sum_modifiers'][1] != 'undefined') {
      $('#button_expand_info').show();
    }

    if (account && account['tutorial'] < 2) {
      $('.weariness_outer_span').hide();
    } else {
      $('.weariness_outer_span').show();
    }

    // Embassy List
    if (d['capitol'] === '1') {
      var player_has_embassy_here = false;
      var embassy_list_length = d['embassy_list'].length;
      $('#embassy_list').html('');
      for (var i = 0; i < embassy_list_length; i++) {
        $('#embassy_list_dropdown_button').show();
        $('#embassy_list').append('<p>Embassy of ' + d['embassy_list'][i].nation_name + ' - Consulate of ' + d['embassy_list'][i].username + '</p>');
        if (d['embassy_list'][i].account_key === account['id']) {
          player_has_embassy_here = true;
        }
        // Show embassy list
      }
    }

    // Own
    if (log_check && d['account_key'] === account['id']) {
      $('#land_form_update_parent').show()
      $('#land_form_submit_update').show();
      $('#land_form_upgrade_parent').show();
      $('#button_expand_upgrade').show();

      // Logic for which upgrades to show
      $('.effect_info_item').show();
      $('#village_info_parent, #town_info_parent, #city_info_parent, #metropolis_info_parent, #fortification_info_parent').hide();

      $('#village_info_parent .upgrade_submit').removeClass('btn-danger').removeClass('btn-action').addClass('btn-action')
      $('#town_info_parent .upgrade_submit').removeClass('btn-danger').removeClass('btn-action').addClass('btn-action')
      $('#city_info_parent .upgrade_submit').removeClass('btn-danger').removeClass('btn-action').addClass('btn-action')

      // Village
      if (d['land_type'] == land_type_key_dictionary['village']) {
        $('.effect_info_item').hide();
        $('#fortification_info_parent').show();
        if (d['valid_upgrades']['town'] >= 0) {
          $('#town_info_parent').show();
        }
      }

      // Forticiation
      if (d['land_type'] == land_type_key_dictionary['fortification']) {
        $('.effect_info_item').hide();
        $('#village_info_parent').show();
        $('#village_info_parent .upgrade_submit').removeClass('btn-action').addClass('btn-danger')
      }

      // Town
      if (d['land_type'] == land_type_key_dictionary['town']) {
        $('#village_info_parent').show();
        $('#village_info_parent .upgrade_submit').removeClass('btn-action').addClass('btn-danger')
        if (d['valid_upgrades']['city'] >= 0) {
          $('#city_info_parent').show();
        }
      }

      // City
      if (d['land_type'] == land_type_key_dictionary['city']) {
        $('#village_info_parent').show();
        $('#village_info_parent .upgrade_submit').removeClass('btn-action').addClass('btn-danger')
        $('#town_info_parent').show();
        $('#town_info_parent .upgrade_submit').removeClass('btn-action').addClass('btn-danger')
        if (d['valid_upgrades']['metropolis'] >= 0) {
          $('#metropolis_info_parent').show();
        }
      }

      // Metropolis
      if (d['land_type'] == land_type_key_dictionary['metropolis']) {
        $('#village_info_parent').show();
        $('#village_info_parent .upgrade_submit').removeClass('btn-action').addClass('btn-danger')
        $('#town_info_parent').show();
        $('#town_info_parent .upgrade_submit').removeClass('btn-action').addClass('btn-danger')
        $('#city_info_parent').show();
        $('#city_info_parent .upgrade_submit').removeClass('btn-action').addClass('btn-danger')
      }

      // Need more message
      if (d['land_type'] == land_type_key_dictionary['village'] && d['valid_upgrades']['town'] < 0) {
        $('#lands_needed_for_upgrade').html('You need <strong class="text-action">&nbsp;' +
          Math.abs(d['valid_upgrades']['town']) +
          '</strong>&nbsp; more Villages to upgrade to a Town');
        $('#lands_needed_for_upgrade').show();
      } else if (d['land_type'] == land_type_key_dictionary['town'] && d['valid_upgrades']['city'] < 0) {
        $('#lands_needed_for_upgrade').html('You need <strong class="text-action">&nbsp;' +
          Math.abs(d['valid_upgrades']['city']) +
          '</strong>&nbsp; more Towns to upgrade to a City');
        $('#lands_needed_for_upgrade').show();
      } else if (d['land_type'] == land_type_key_dictionary['city'] && d['valid_upgrades']['metropolis'] < 0) {
        $('#lands_needed_for_upgrade').html('You need <strong class="text-action">&nbsp;' +
          Math.abs(d['valid_upgrades']['metropolis']) +
          '</strong>&nbsp; more Cities to upgrade to a Metropolis');
        $('#lands_needed_for_upgrade').show();
      }

      if (d['account']['stats'].treasury_after < building_minimum) {
        $('#land_form_low_treasury').show();
        $('.effect_info_item').hide();
        if (d['land_type'] == land_type_key_dictionary['village']) {
          $('#button_expand_upgrade').hide();
        } else {
          $('#village_info_parent').show();
        }
      }
      // Do not own
    } else if (log_check) {

      // Embassy Logic
      if (d['capitol'] === '1') {
        $('#embassy_info_dropdown_button').show();
        if (player_has_embassy_here) {
          $('#remove_embassy').show();
        }
        else {
          $('#build_embassy').show();
        }
      }

      // In range
      if (d['in_range']) {
        // And unclaimed
        if (d['land_type'] == land_type_key_dictionary['unclaimed']) {
          if (account['tutorial'] < 2) {
            $('#land_form_submit_claim_tutorial').show();
          }
          else if (account['stats']['support'] <= 0) {
            $('#land_form_support_too_low').show();
          }
          else {
            $('#land_form_submit_claim').show();
          }
        }
        // And claimed
        else {
          if (account['tutorial'] < 2) {
            $('#land_form_submit_attack_tutorial').show();
          }
          else if (account['stats']['support'] <= 0) {
            $('#land_form_support_too_low').show();
          }
          else {
            $('#land_form_submit_attack').show();
          }
        }
        // Not in range
      } else {
        $('#not_in_range').show();
      }
    }

    // Capitol
    if (d['capitol'] === '1') {
      $('#capitol_info').show();
      $('.land_leader_portrait_image').prop('src', '<?=base_url()?>uploads/' + d['account']['leader_portrait']);
      $('.land_leader_portrait_image_link').prop('href', '<?=base_url()?>uploads/' + d['account']['leader_portrait']);
      $('.land_nation_flag_image').prop('src', '<?=base_url()?>uploads/' + d['account']['nation_flag']);
      $('.land_nation_flag_image_link').prop('href', '<?=base_url()?>uploads/' + d['account']['nation_flag']);
      $('#capitol_info_parent').hide();
    }

  }

  // Prepare land form data
  function prepare_land_form_data(coord_slug, world_key, d) {
    $('#input_id').val(d['id']);
    $('#input_coord_slug').val(d['coord_slug']);
    if (d['land_name'] != '') {
      $('#land_name_label').html(d['land_name']);
    } else if (d['land_name']['capitol']) {
      $('#land_name_label').html('Unnamed Capitol');
    } else {
      $('#land_name_label').html('Unnamed ' + ucwords(land_dictionary[d['land_type']]));
    }
    $('#land_content_label').html(d['content']);
    $('#land_gdp_label').html(number_format(d['sum_effects']['gdp']));
    $('#land_population_label').html(number_format(d['sum_effects']['population']));

    $('#coord_link').prop('href', '<?=base_url()?>world/' + world_key + '?land=' + coord_slug);
    $('#coord_link').html(coord_slug);

    // $('#input_land_content').addClass('input_to_label');
    // $('#input_land_name').addClass('input_to_label');

    // If claimed
    if (d['land_type'] != land_type_key_dictionary['unclaimed']) {
      $('#government_label').html(government_dictionary[d['account']['government']]);
      $('#land_type_label').html(ucwords(land_dictionary[d['land_type']]));
      if (d['account']['nation_name']) {
        $('#nation_label').html(d['account']['nation_name']);
      } else {
        $('#nation_label').html('Anonymous #' + d['account']['id']);
      }
      $('#username_label').html(d['account']['username']);
    }

    $('#input_land_name').val(d['land_name']);
    $('#input_content').val(d['content']);

    $('.weariness_attack_span').html(d['weariness']);
  }

  function prepare_land_form_more_info(coord_slug, world_key, d) {
    $('#land_info_div').html();
    var more_info_string = '';
    var building_line = '';
    var building_label = '';
    var building_count = '';
    var removeLink = '';
    var removeGlyphicon = '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
    for (var i = 0; i < d['sum_modifiers'].length; i++) {
      // Skip if land type
      if (d['sum_modifiers'][i]['id'] <= 10 && d['sum_modifiers'][i]['id'] != land_type_key_dictionary['capitol']) {
        continue;
      }

      // Format names
      var name_with_s = d['sum_modifiers'][i]['name'] + 's';
      var modifier_name = ucwords(name_with_s.replace(/ys/g, 'ies').replace(/_/g, ' '));
      // Simple hardcoded fix for ys in middle of this word
      if (modifier_name === 'Skiescrapers') {
        modifier_name = 'Skyscrapers';
      }

      // Remove link
      removeLink = '';
      if (log_check && d['sum_modifiers'][i]['name'] != 'capitol' && d['sum_modifiers'][i]['name'] != 'embassy' && d['account_key'] == account_id) {
        removeLink = ' ' + '<button type="button" class="remove_building btn btn-sm btn-danger" value="' + '-' + d['sum_modifiers'][i]['id'] + '">' + removeGlyphicon + '</button>';
      }

      // Build HTML
      building_label = '<span class="building_name">' + modifier_name + '</span>';
      building_count = '<span>' + d['sum_modifiers'][i]['count'] + removeLink + '</span>';
      building_line = '<div class="building_item">' + building_label + ': ' + building_count + '</div><br>';
      more_info_string = more_info_string + building_line;
    }
    $('#land_info_div').html(more_info_string);
  }

  function land_form_ajax(form_type) {
    // Serialize form into post data
    $('#form_type_input').val(form_type);
    var post_data = $('#land_form').serialize();

    // Submit form
    $.ajax({
      url: "<?=base_url()?>land_form",
      type: "POST",
      data: post_data,
      cache: false,
      success: function(data) {
        // Return data
        // console.log(data);
        response = JSON.parse(data);
        land_form_response(response, form_type);
      }
    });
  }

  function land_form_response(response, form_type) {
    if (response['error']) {
      // Bug here
      // $('#land_form_result').show();
      // $('#land_form_result_message').html(response['error']);
      alert(response['error']);
      return false;
    }
    if (response['status'] === 'fail') {
      // Bug here
      // $('#land_form_result').show();
      // $('#land_form_result_message').html(response['message']);
      alert(response['message']);
      return false;
    }

    // If success
    if (response['status'] === 'success') {
      // Pass information to user
      // $('#land_form_result_message').html(response['message']);
      $('.center_block').fadeOut(300);

      // Update player variables and displays
      // player_land_count = player_land_count + 1;
      // $('#owned_lands_span').html( number_format(player_land_count) );

      // Update Land Style
      if (form_type === 'build_embassy' || form_type === 'remove_embassy') {
        return;
      }

      // Claim or attack
      if (form_type === 'claim' || form_type === 'attack') {
        boxes[d['id']].setOptions({
          strokeWeight: 3,
          strokeColor: '<?php echo $stroke_color_dictionary['village']; ?>',
          fillColor: account['color'],
          fillOpacity: 0.4
        });
      }
      // Capitol
      else if (form_type == land_type_key_dictionary['capitol']) {
        boxes[d['id']].setOptions({
          strokeWeight: 3,
          strokeColor: '<?php echo $stroke_color_dictionary['capitol']; ?>',
          fillOpacity: 0.8
        });
      }
      // Town
      else if (form_type == land_type_key_dictionary['town']) {
        boxes[d['id']].setOptions({
          strokeWeight: 3,
          strokeColor: '<?php echo $stroke_color_dictionary['town']; ?>',
          fillOpacity: 0.4
        });
      }
      // City
      else if (form_type == land_type_key_dictionary['city']) {
        boxes[d['id']].setOptions({
          strokeWeight: 3,
          strokeColor: '<?php echo $stroke_color_dictionary['city']; ?>',
          fillOpacity: 0.4
        });
      }
      // Metroplis
      else if (form_type == land_type_key_dictionary['metropolis']) {
        boxes[d['id']].setOptions({
          strokeWeight: 3,
          strokeColor: '<?php echo $stroke_color_dictionary['metropolis']; ?>',
          fillOpacity: 0.4
        });
      }
      // Fortification
      else if (form_type == land_type_key_dictionary['fortification']) {
        boxes[d['id']].setOptions({
          strokeWeight: 3,
          strokeColor: '<?php echo $stroke_color_dictionary['fortification']; ?>',
          fillOpacity: 0.4
        });
      }

      // Tutorial Rule
      if (account && account['tutorial'] < 2) {
        $('#tutorial_block').fadeOut(1000, function() {
          $('#tutorial_block').fadeIn();
          $('#tutorial_title').html('We The People');
          $('#tutorial_text').html('Pick a form of Government, set a tax rate, and balance your budget. Read the useful info section to learn gameplay mechanics.');
          $('.stat_dropdown').click();
          account['tutorial'] = 2;
        });
      }

      return true;
    }
  }

  function land_form_tutorial(land_form_type) {
    if (!account) {
      return false;
    }
    // Tutorial
    if (account['tutorial'] === '3' && (land_form_type === 'update' || $.isNumeric(land_form_type))) {
      account['tutorial'] = 4;
      $('#tutorial_block').fadeOut(1000, function() {
        $('#tutorial_block').fadeIn();
        $('#tutorial_title').html('Manifest Destiny');
        $('#tutorial_text').html('Conquer the world. War Weariness will accumulate as you acquire territory and will go down over time.');
      });
    }
    if (account['tutorial'] === '4' && (land_form_type === 'attack' || land_form_type === 'claim')) {
      account['tutorial'] = 5;
      $('#tutorial_block').fadeOut(1000)
    }
  }

  // 
  // Land loop
  // 

  <?php 
  // This foreach loop runs 15,000 times, so performance and bandwidth is key
  // Because of this, some unconventional code may be used
  foreach ($lands as $land) {
      $stroke_weight = 0.2; 
      $stroke_color = '#222222';
      $fill_color = "#FFFFFF";
      $fill_opacity = '0';
      // Unclaimed
      if ($land['land_type'] > 1) {
        $fill_color = $land['color'];
        $fill_opacity = '0.4';
      }
      if ($log_check && $land['account_key'] == $account['id']) {
        $stroke_color = $stroke_color_dictionary['village'];
        $stroke_weight = 3;
      }
      if ($land['capitol'] == '1') {
        $stroke_weight = 2;
        $fill_opacity = '0.8';
        $stroke_color = $stroke_color_dictionary['capitol'];
      } 
      // Town
      else if ($land['land_type'] == $land_type_key_dictionary['town']) {
        $stroke_weight = 2;
        $stroke_color = $stroke_color_dictionary['town'];
      } 
      // City
      else if ($land['land_type'] == $land_type_key_dictionary['city']) {
        $stroke_weight = 2;
        $stroke_color = $stroke_color_dictionary['city'];
      } 
      // Metropolis
      else if ($land['land_type'] == $land_type_key_dictionary['metropolis']) {
        $stroke_weight = 2;
        $stroke_color = $stroke_color_dictionary['metropolis'];
      }
      // Fortification
      else if ($land['land_type'] == $land_type_key_dictionary['fortification']) {
        $stroke_weight = 2;
        $stroke_color = $stroke_color_dictionary['fortification'];
      }
      ?> z(<?php echo
          $land['id'] . ',' .
          $land['lat'] . ',' .
          $land['lng'] . ',' .
          $stroke_weight . ',' .
          '"' . $stroke_color . '"' . ',' .
          '"' . $fill_color . '"' . ',' .
          $fill_opacity; ?>); <?php // Open and close immediately to avoid whitespace eating bandwidth
  } ?>

  // 
  // Update map data
  // 

  // Get map update
  setInterval(function() {
    get_map_update(world_key);
  }, map_update_interval);

  leaderboard_bonus_text();
  setInterval(function() {
    update_leaderboard_call();
  }, leaderboard_update_interval);

  function leaderboard_bonus_text() {
    // Population defensive bonus
    var pop_column = 5;
    var highest_pop = 0;
    var pop_leader = false;
    var this_value = 0;
    $('#leaderboard_table tbody tr td:nth-child(' + pop_column + ')').each(function(){
      this_value = parseInt($(this).text().replace(/,/g, ''));
      if (this_value >= highest_pop) {
        highest_pop = this_value;
        pop_leader = $(this);
      }
    });
    if (pop_leader) {
      pop_leader.html(pop_leader.html() + '<br><small class="text-info">50% Defensive Bonus</small>');
    }

    // Culture Attacking bonus
    var culture_column = 6;
    var highest_culture = 0;
    var culture_leader = false;
    var this_value = 0;
    $('#leaderboard_table tbody tr td:nth-child(' + culture_column + ')').each(function(){
      this_value = parseInt($(this).text().replace(/,/g, ''));
      if (this_value >= highest_culture) {
        highest_culture = this_value;
        culture_leader = $(this);
      }
    });
    if (culture_leader) {
      culture_leader.html(culture_leader.html() + '<br><small class="text-purple">50% Attacking Bonus</small>');
    }
  }

  // Get single land ajax
  function get_map_update(world_key) {
    $.ajax({
      url: "<?=base_url()?>world/" + world_key,
      type: "GET",
      data: {
        json: "true"
      },
      cache: false,
      success: function(data) {
        // console.log(data);
        data = JSON.parse(data);

        // Check for refresh signal from server 
        if (data['refresh']) {
          alert('The game is being updated, and we need to refresh your screen. This page will refresh after you press ok');
          window.location.reload();
        }

        if (log_check && !data['account']) {
          alert('You were away too long and you\'re session has expired, please log back in.');
          window.location.href = '<?php echo base_url(); ?>world/' + world_key + '?login';
          return false;
        }

        update_lands(data['lands']);
        if (log_check) {
          account = data['account'];
          update_stats(data['account']);
        }

        console.log('update');
      }
    });
  }

  function update_leaderboard_call() {
    $.ajax({
      url: "<?=base_url()?>world/leaderboards/" + world_key,
      type: "GET",
      data: {
        json: "true"
      },
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        update_leaderboards(data);
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
      if (land['land_type'] > land_type_key_dictionary['unclaimed']) {
        fill_color = land['color'];
        fill_opacity = 0.4;
      }
      if (log_check && land['account_key'] == account_id) {
        stroke_color = '<?php echo $stroke_color_dictionary['village']; ?>';
      }
      if (land['capitol'] == 1) {
        stroke_weight = 2;
        fill_opacity = '0.8';
        stroke_color = '<?php echo $stroke_color_dictionary['capitol']; ?>';
      } else if (land['land_type'] == land_type_key_dictionary['town']) {
        stroke_weight = 2;
        stroke_color = '<?php echo $stroke_color_dictionary['town']; ?>';
      } else if (land['land_type'] == land_type_key_dictionary['city']) {
        stroke_weight = 2;
        stroke_color = '<?php echo $stroke_color_dictionary['city']; ?>';
      } else if (land['land_type'] == land_type_key_dictionary['metropolis']) {
        stroke_weight = 2;
        stroke_color = '<?php echo $stroke_color_dictionary['metropolis']; ?>';
      } else if (land['land_type'] == land_type_key_dictionary['fortification']) {
        stroke_weight = 2;
        stroke_color = '<?php echo $stroke_color_dictionary['fortification']; ?>';
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
    $('.land_count_span').html(number_format(account['stats']['land_count']));
    $('.tax_rate_span').html(account['tax_rate']);
    $('.tax_income_span').html(number_format(account['stats']['tax_income_total']));
    $('.corruption_rate_span').html(number_format(account['stats']['corruption_total']));
    $('.population_span').html(number_format(account['stats']['population']));
    $('.culture_span').html(number_format(account['stats']['culture']));
    $('.gdp_span').html(number_format(account['stats']['gdp']));
    $('.building_maintenance_span').html(number_format(account['stats']['building_maintenance']));
    $('.military_span').html(number_format(account['military']));
    $('.military_stats_span').html(number_format(account['stats']['military']));
    $('.military_spending_span').html(number_format(account['stats']['military_spending']));
    $('.military_total_span').html(number_format(account['stats']['military_total']));
    $('.entitlements_span').html(number_format(account['stats']['entitlements']));
    $('.treasury_span').html(number_format(account['stats']['treasury_after']));
    $('.weariness_span').html(account['stats']['weariness']);
    $('.political_support_span').html(account['stats']['support']);
    $('.government_span').html(government_dictionary[account['government']]);
    return true;
  }

  function update_leaderboards(leaderboards) {
    var rank = 1;
    var html = '';
    // Changes in this html should also be reflected in #leaderboard_table tbody
    $(leaderboards).each(function(key, value){
      html += '';
      html += '<tr>';
      html += '<td><strong>' + rank + '</strong></td>';
      html += '<td>';
      html += '<span class="glyphicon glyphicon-user" aria-hidden="true" style="color: ' + value.color + '"> </span>';
      html += '<strong class="leaderboard_username">' + value.username + '</strong>';
      html += '<br>';
      html += '<a href="<?=base_url()?>uploads/' + value.leader_portrait + '" target="_blank">';
      html += '<img class="leaderboard_leader_portrait" src="<?=base_url()?>uploads/' + value.leader_portrait + '">';
      html += '</a>';
      html += '</td>';
      html += '<td>';
      html += '<strong class="leaderboard_nation_name">' + value.nation_name + '</strong>';
      html += '<br>';
      html += '<a href="<?=base_url()?>uploads/' + value.nation_flag + '" target="_blank">';
      html += '<img class="leaderboard_nation_flag" src="<?=base_url()?>uploads/' + value.nation_flag + '">';
      html += '</a>';
      html += '</td>';
      html += '<td>';
      html += '<strong class="text-success">' + number_format(value.stats.land_count) + '</strong>';
      html += '</td>';
      html += '<td>';
      html += '<strong class="text-info">' + number_format(value.stats.population) + '</strong><span class="text-info">,000</span>';
      html += '</td>';
      html += '<td>';
      html += '<strong class="text-purple">' + number_format(value.stats.culture) + '</strong>';
      html += '</td>';
      html += '<td>';
      html += '<strong class="text-action">' + number_format(value.stats.gdp) + '</strong><span class="text-action">,000,000</span>';
      html += '</td>';
      html += '<td>';
      html += '<strong class="text-danger">' + number_format(value.stats.military_total) + '</strong><span class="text-danger">,000,000</span>';
      html += '</td>';
      rank++;
    });
    $('#leaderboard_table tbody').html(html);
    leaderboard_bonus_text();
    return true;
  }

  // 
  // Remove overlay
  // 

  // Remove loading overlay based on tiles loaded status
  google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
    $('#overlay').fadeOut();
  });
  // Remove loading overlay based on idle status
  // google.maps.event.addListenerOnce(map, 'idle', function(){
  // });
}
</script>