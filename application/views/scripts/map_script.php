<script>
  pass_new_laws();
  attack_key_listen();
  enlist_listen();
  map_toggle_listen();

  if (account) {
    get_account_update();
    setInterval(function() {
      get_account_update();
    }, account_update_interval_ms);
  }

  setInterval(function() {
    get_map_update();
  }, map_update_interval_ms);

  function initMap() {
    set_map();
    remove_overlay();
    generate_tiles();
  }

  function attack_key_listen() {
    $(document).keydown(function(event) {
      // Attack shortcut
      if (event.which == keys['a']) {
        attack_key_pressed = true;
      }
    });
    $(document).keyup(function(event) {
      if (event.which == keys['a']) {
        attack_key_pressed = false;
      }
    });
  }

  function map_toggle_listen() {
    $('#border_toggle').click(function(event) {
      $('#border_toggle').removeClass('active');
      border_toggle = !border_toggle;
      if (border_toggle) {
        $('#border_toggle').addClass('active');
      }
      if (border_toggle) {
        tiles_to_borders();
      }
      else {
        tiles_to_terrain();
      }
    });
    $('#settlement_toggle').click(function(event) {
      $('#settlement_toggle').removeClass('active');
      settlement_toggle = !settlement_toggle;
      if (settlement_toggle) {
        $('#settlement_toggle').addClass('active');
      }
      set_marker_set_visibility(settlement_markers, settlement_toggle);
    });
    $('#resource_toggle').click(function(event) {
      $('#resource_toggle').removeClass('active');
      resource_toggle = !resource_toggle;
      if (resource_toggle) {
        $('#resource_toggle').addClass('active');
      }
      set_marker_set_visibility(resource_markers, resource_toggle);
    });
    $('#unit_toggle').click(function(event) {
      $('#unit_toggle').removeClass('active');
      unit_toggle = !unit_toggle;
      if (unit_toggle) {
        $('#unit_toggle').addClass('active');
      }
      set_marker_set_visibility(unit_markers, unit_toggle);
    });
    $('#grid_toggle').click(function(event) {
      $('#grid_toggle').removeClass('active');
      grid_toggle = !grid_toggle;
      if (grid_toggle) {
        $('#grid_toggle').addClass('active');
        tiles_with_grid();
      }
      else {
        tiles_without_grid();
      }
    });
  }

  function enlist_listen() {

  }

  function tiles_to_terrain() {
    Object.keys(tiles).forEach(function(key) {
      tiles[key].setOptions({
        fillColor: tiles[key].terrain_fillColor,
      });
    });
  }

  function tiles_to_borders() {
    Object.keys(tiles).forEach(function(key) {
      tiles[key].setOptions({
        fillColor: tiles[key].borders_fillColor,
      });
    });
  }

  function tiles_without_grid() {
    Object.keys(tiles).forEach(function(key) {
      tiles[key].setOptions({
        strokeWeight: 0,
        strokeColor: 0,
      });
    });
  }

  function tiles_with_grid() {
    Object.keys(tiles).forEach(function(key) {
      tiles[key].setOptions({
        strokeWeight: <?= STROKE_WEIGHT; ?>,
        strokeColor: '<?= STROKE_COLOR; ?>',
      });
    });
  }

  function set_marker_set_visibility(marker_set, visible) {
    for (i = 0; i < marker_set.length; i++) {
      set_marker_visibility(marker_set[i], visible);
    }
  }

  function set_marker_visibility(marker, visible) {
    if (!marker) {
      return;
    }
    marker.setVisible(visible);
  }

  function update_visibility_of_markers() {
    set_marker_set_visibility(resource_markers, resource_toggle);
    set_marker_set_visibility(settlement_markers, settlement_toggle);
    set_marker_set_visibility(unit_markers, unit_toggle);
  }

  function set_map() {
    map = new google.maps.Map(document.getElementById('map'), {
      // Zoom on tile if set as parameter
      <?php if ( isset($_GET['lng']) ) { ?>
      // Logic to center isn't understood, but results in correct behavior in all 4 corners
      center: {
        lat: <?= $_GET['lat'] + ($world['tile_size'] / 2); ?>,
        lng: <?= $_GET['lng'] - ($world['tile_size'] / 2); ?>
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
      mapTypeControlOptions: {
        mapTypeIds: ['satellite', 'hybrid', 'terrain', 'paper_map']
      }
    });

    styled_map_type = new google.maps.StyledMapType(map_pirate, {name: 'Paper'});
    map.mapTypes.set('paper_map', styled_map_type);

    map.setMapTypeId('<?= DEFAULT_MAP; ?>');
  }

  function remove_overlay() {
    // Remove loading overlay based on tiles loaded status
    google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
      $('#overlay').fadeOut();
    });
  }

  function set_resource_icon(resource_id, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/natural_resources/${resource_id}.png`, lat, lng, false);
  }

  function set_industry_icon(industry_slug, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/industries/${industry_slug}.png`, lat, lng, false);
  }

  function set_settlement_icon(settlement_id, is_capitol, is_base, lat, lng) {
    if (parseInt(is_capitol)) {
      return set_industry_icon('capitol', lat, lng);
    }
    if (parseInt(is_base)) {
      return set_industry_icon('base', lat, lng);
    }
    return set_marker_icon(`${base_url}resources/icons/settlements/${settlement_id}.png`, lat, lng, false);
  }

  // Uses http://www.googlemapsmarkers.com/
  // http://www.googlemapsmarkers.com/v1/A/0099FF/FFFFFF/FF0000/
  // Becomes
  // https://chart.apis.google.com/chart?cht=d&chdp=mapsapi&chl=pin%27i%5c%27%5bA%27-2%27f%5chv%27a%5c%5dh%5c%5do%5c0099FF%27fC%5cFFFFFF%27tC%5cFF0000%27eC%5cLauto%27f%5c&ext=.png
  function set_unit_icon(unit_id, terrain_key, unit_owner_color, lat, lng) {
    unit_owner_color = unit_owner_color.replace('#', '');
    let character = unit_types[unit_id - 1].character;
    let unit_color = unit_types[unit_id - 1].color;
    if (parseInt(terrain_key) === ocean_key) {
      character = navy_character;
      unit_color = navy_color;
    }
    let path = get_googlemapsmarkers_path(character, unit_owner_color, unit_color, unit_color);
    unit = {
      unit_id: unit_id,
      character: character,
      unit_color: unit_color,
      unit_owner_color: unit_owner_color,
    }
    return set_marker_icon(path, lat, lng, unit);
  }

  function update_unit_icon(marker, tile) {
    let unit_owner_color = marker.unit.unit_owner_color;
    let character = unit_types[marker.unit.unit_id - 1].character;
    let unit_color = unit_types[marker.unit.unit_id - 1].color;
    if (parseInt(tile.terrain_key) === ocean_key) {
      character = navy_character;
      unit_color = navy_color;
    }
    let url = get_googlemapsmarkers_path(character, marker.unit.unit_owner_color, unit_color, unit_color);
    marker.setIcon(url);
  }

  function get_googlemapsmarkers_path(character, unit_owner_color, stroke_color, second_stroke_color) {
    stroke_color = '000000';
    // second_stroke_color = '000000';
    return `http://www.googlemapsmarkers.com/v1/${character}/${unit_owner_color}/${stroke_color}/${second_stroke_color}`;
  }

  function set_marker_icon(path, lat, lng, unit) {
    let draggable = false;
    let title = '';
    let this_icon = {
      url: path,
      scaledSize: new google.maps.Size(20, 20),
      origin: new google.maps.Point(0,0),
      anchor: new google.maps.Point(10,10)
    };
    if (unit) {
      draggable = true;
      title = unit_labels[unit.unit_id];
      lat = lat - (tile_size / 4);
      this_icon = {
        url: path,
      };
    }
    let myLatLng = {
      lat: lat + (tile_size / 2),
      lng: lng - (tile_size / 2)
    };
    let marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      draggable:draggable,
      icon: this_icon,
      unit: unit,
      title: title,
    });
    marker.setMap(map);
    if (draggable) {
      marker.addListener('dragstart', function(event){
        start_drag_unit(event, marker);
      });
      marker.addListener('dragend', function(event){
        end_drag_unit(event, marker);
      });
    }
    marker.addListener('click', open_tile);
    return marker;
  }

  function generate_tiles() {
    <?php 
    // This foreach loop runs 15,000 times, so performance and bandwidth is key
    // Because of this, some unconventional code may be used
    foreach ($tiles as $tile) {
      $terrain_color = $this->game_model->get_tile_terrain_color($tile);
      $border_color = $this->game_model->get_tile_border_color($tile);
      if ($tile['resource_key']) { ?>
        resource_markers.push(set_resource_icon(<?= $tile['resource_key']; ?>,<?= $tile['lat']; ?>, <?= $tile['lng']; ?>));
      <?php }
      if ($this->game_model->tile_is_incorporated($tile['settlement_key'])) { ?>
        settlement_markers.push(set_settlement_icon(<?= $tile['settlement_key']; ?>, <?= $tile['is_capitol'] ? '1' : '0'; ?>, <?= $tile['is_base'] ? '1' : '0'; ?>, <?= $tile['lat']; ?>, <?= $tile['lng']; ?>));
      <?php }
      if ($tile['unit_key']) { ?>
        unit_markers.push(set_unit_icon(<?= $tile['unit_key']; ?>, <?= $tile['terrain_key']; ?>, '<?= $tile['unit_owner_color']; ?>', <?= $tile['lat']; ?>, <?= $tile['lng']; ?>));
      <?php }
      ?>z(<?=
        $tile['id'] . ',' .
        $tile['lat'] . ',' .
        $tile['lng'] . ',' .
        '"' . $terrain_color . '",' .
        '"' . $border_color . '"'
      ; ?>);<?php // Open and close immediately to avoid whitespace eating bandwidth
    } ?>

    update_visibility_of_markers();
  }

  // Declare square called by performance sensitive loop
  function z(tile_key, tile_lat, tile_lng, terrain_color, border_color) {
    let current_fill_color = border_toggle ? border_color : terrain_color;
    let shape = [{
        lat: tile_lat,
        lng: tile_lng
      },
      {
        lat: tile_lat + tile_size,
        lng: tile_lng
      },
      {
        lat: tile_lat + tile_size,
        lng: tile_lng - tile_size
      },
      {
        lat: tile_lat,
        lng: tile_lng - tile_size
      }
    ];
    let polygon = new google.maps.Polygon({
      map: map,
      paths: shape,
      tile_key: tile_key,
      fillOpacity: <?= TILE_OPACITY; ?>,
      strokeWeight: <?= STROKE_WEIGHT; ?>,
      strokeColor: '<?= STROKE_COLOR; ?>',
      fillColor: current_fill_color,
      terrain_fillColor: terrain_color,
      borders_fillColor: border_color,
    });
    polygon.setMap(map);
    polygon.addListener('click', open_tile);
    tiles[tile_key] = polygon;
    tiles_by_coord[tile_lat + ',' + tile_lng] = tiles[tile_key];
  }

  function get_account_update() {
    ajax_get('game/get_this_full_account/' + world_key, function(response) {
      account = response;
      update_supplies(account.supplies);
    });
  }

  function update_supplies(supplies) {
    Object.keys(supplies).forEach(function(key) {
      let supply = supplies[key];
      $('#government_supply_' + supply['slug']).html(supply['amount']);
      $('#their_trade_supply_current_' + supply['slug']).html(supply['amount']);
      // $('#their_trade_supply_offer_' + supply['slug']).val(supply['amount']);
      $('#our_trade_supply_current_' + supply['slug']).html(supply['amount']);
      // $('#our_trade_supply_offer_' + supply['slug']).val(supply['amount']);
    });
  }

  function get_map_update() {
    ajax_get('game/update_world/' + world_key, function(response) {
      // Check for refresh signal from server 
      if (response['refresh']) {
        alert('The game is being updated, and we need to refresh your screen. This page will refresh after you press ok');
        window.location.reload();
      }

      if (account && !response['account']) {
        alert('You were away too long and you\'re session has expired, please log back in.');
        window.location.href = '<?= base_url(); ?>world/' + world_key + '?login';
        return false;
      }

      update_tiles(response['tiles']);

      if ($('#tile_block').is(':visible')) {
        highlight_single_square(current_tile.id);
      }
    });
  }

  function pass_new_laws() {
    $('#pass_new_laws_button').click(function(event) {
      ajax_get('game/laws_form', function(response) {
        // Do update, don't think this is needed though?
        // get_map_update();
      });
    });
  }

  function update_tiles(new_tiles) {
    // This loop may rarely run up to 15,000 times, so focus is a performance
    number_of_tiles = new_tiles.length;
    for (i = 0; i < number_of_tiles; i++) {
      let new_tile = new_tiles[i];
      new_tile.lat = parseInt(new_tile.lat);
      new_tile.lng = parseInt(new_tile.lng);
      new_tile.is_capitol = parseInt(new_tile.is_capitol);
      new_tile.resource_key = parseInt(new_tile.resource_key);
      new_tile.settlement_key = parseInt(new_tile.settlement_key);
      new_tile.unit_key = parseInt(new_tile.unit_key);
      border_color = get_tile_border_color(new_tile);
      fill_color = border_toggle ? border_color : tiles[new_tile['id']].fillColor;
      // Update settlement markers
      // Update unit markers
      tiles[new_tile['id']].setOptions({
        fillColor: fill_color,
        borders_fillColor: border_color,
      });
      if (new_tile.resource_key) {
        resource_markers.push(set_resource_icon(new_tile.resource_key, new_tile.lat, new_tile.lng));
      }
      // @TODO Remove settlement_markers logic needed
      if (new_tile.settlement_key) {
        settlement_markers.push(set_settlement_icon(new_tile.settlement_key, new_tile.is_capitol, new_tile.is_base, new_tile.lat, new_tile.lng));
      }
      if (new_tile.is_capitol){
        settlement_markers.push(set_settlement_icon(new_tile.settlement_key, new_tile.is_capitol, new_tile.is_base, new_tile.lat, new_tile.lng));
      }
      if (new_tile.unit_key) {
        // Re-enable when removing markers on update exists
        // unit_markers.push(set_unit_icon(new_tile.unit_key, new_tile.terrain_key.unit_key, account.color, new_tile.lat, new_tile.lng));
      }
    }
    update_visibility_of_markers();
    return true;
  }

  function update_tile_terrain(lng, lat, world_key, type, callback) {
    let data = {
      world_key: world_key,
      lng: lng,
      lat: lat,
    };
    ajax_post('game/tile_form', data, function(response) {
      callback(response);
    });
  }

  function start_drag_unit(event, marker) {
    $('.center_block').hide();
    start_lat = round_down(event.latLng.lat()) - tile_size;
    start_lng = round_down(event.latLng.lng());
    highlight_valid_squares();
  }

  function end_drag_unit(event, marker) {
    unhighlight_all_squares(start_lat, start_lng);
    let end_lat = round_down(event.latLng.lat()) - tile_size;
    let end_lng = round_down(event.latLng.lng());
    end_lng = correct_lng(end_lng);
    let moved = move_unit_to_new_position(marker, start_lat, start_lng, end_lat, end_lng);
    if (!moved) {
      return;
    }
    highlighted_tiles = [];
    request_unit_attack(marker, start_lat, start_lng, end_lat, end_lng, function(response){
      update_unit_icon(marker, response.tile);
      get_map_update();
    });
  }

  function request_unit_attack(marker, start_lat, start_lng, end_lat, end_lng, callback) {
    let data = {
      world_key: world_key,
      start_lat: start_lat,
      start_lng: start_lng,
      end_lat: end_lat,
      end_lng: end_lng,
    };
    ajax_post('game/unit_move_to_land', data, function(tile) {
      callback(tile);
    });
  }

  function highlight_valid_squares() {
    highlighted_tiles = [];
    highlighted_tiles.push(tiles_by_coord['' + (start_lat + tile_size) + ',' + (start_lng)]);
    highlighted_tiles.push(tiles_by_coord['' + (start_lat) + ',' + (correct_lng(start_lng + tile_size))]);
    highlighted_tiles.push(tiles_by_coord['' + (start_lat - tile_size) + ',' + (start_lng)]);
    highlighted_tiles.push(tiles_by_coord['' + (start_lat) + ',' + (correct_lng(start_lng - tile_size))]);
    for (let i = 0; i < highlighted_tiles.length; i++) {
      tiles[highlighted_tiles[i].tile_key].setOptions({
        fillColor: unit_valid_square_color,
      });;
    }
  }

  function highlight_single_square(tile_id) {
    tiles[tile_id].setOptions({
      fillColor: selected_square_color,
    });;
  }

  function unhighlight_all_squares() {
    if (border_toggle) {
      tiles_to_borders();
    }
    else {
      tiles_to_terrain();
    }
  }

  function move_unit_to_new_position(marker, start_lat, start_lng, end_lat, end_lng) {
    let allowed_move_to_new_position = false;
    let lat = position_lat_lng_lower(end_lat, end_lng)[0];
    let lng = position_lat_lng_lower(end_lat, end_lng)[1];
    if (tiles_are_adjacent(start_lat, start_lng, end_lat, end_lng) && no_marker_at_square(lat, lng)) {
      allowed_move_to_new_position = true;
    }
    else {
      lat = position_lat_lng_lower(start_lat, start_lng)[0];
      lng = position_lat_lng_lower(start_lat, start_lng)[1];
    }
    let position = new google.maps.LatLng(lat, lng);
    marker.setPosition(position);
    start_lat = start_lng = null;
    return allowed_move_to_new_position;
  }

  function position_lat_lng_lower(lat, lng) {
    lat = lat - (tile_size / 4);
    lat = lat + (tile_size / 2);
    lng = lng - (tile_size / 2);
    return [lat, lng];
  }

  function no_marker_at_square(lat, lng) {
    for (let i = 0; i < unit_markers.length; i++) {
      if (unit_markers[i].getPosition().lat() == lat && unit_markers[i].getPosition().lng() == lng) {
        return false;
      }
    }
    return true;
  }

  function is_location_free(search) {
    for (var i = 0, l = lookup.length; i < l; i++) {
      if (lookup[i][0] === search[0] && lookup[i][1] === search[1]) {
        return false;
      }
    }
    return true;
  }

  function open_tile(event) {
    var lat = round_down(event.latLng.lat()) - tile_size;
    var lng = round_down(event.latLng.lng());
    lng = correct_lng(lng);

    if (attack_key_pressed) {
      update_tile_terrain(lng, lat, world_key, 'attack', function(response) {
        get_map_update();
      });
      return true;
    }

    $('.center_block').hide();

    unhighlight_all_squares();
    render_tile(lat, lng);
  }

</script>