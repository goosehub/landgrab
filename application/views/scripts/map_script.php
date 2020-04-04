<script>
  use_toggle_cookies();
  pass_new_laws();
  attack_key_listen();
  map_toggle_listen();

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
      $('#border_toggle').removeClass('btn-success').addClass('btn-warning');
      border_toggle = !border_toggle;
      setCookie('border_toggle', border_toggle);
      if (border_toggle) {
        $('#border_toggle').removeClass('btn-warning').addClass('btn-success');
        tiles_to_borders();
      }
      else {
        tiles_to_terrain();
      }
    });
    $('#settlement_toggle').click(function(event) {
      $('#settlement_toggle').removeClass('btn-success').addClass('btn-primary');
      settlement_toggle = !settlement_toggle;
      setCookie('settlement_toggle', settlement_toggle);
      if (settlement_toggle) {
        $('#settlement_toggle').removeClass('btn-primary').addClass('btn-success');
      }
      update_visibility_of_markers();
    });
    $('#township_industry_toggle').click(function(event) {
      township_and_industry_toggle++
      if (township_and_industry_toggle == 3) {
        township_and_industry_toggle = 0;
      }
      if (township_and_industry_toggle == 0) {
        $('#township_industry_toggle').removeClass('btn-success').removeClass('btn-warning').addClass('btn-primary');
      }
      if (township_and_industry_toggle == 1) {
        $('#township_industry_toggle').removeClass('btn-primary').removeClass('btn-warning').addClass('btn-success');
      }
      if (township_and_industry_toggle == 2) {
        $('#township_industry_toggle').removeClass('btn-primary').removeClass('btn-success').addClass('btn-warning');
      }
      update_visibility_of_markers();
      setCookie('township_and_industry_toggle', township_and_industry_toggle);
    });
    $('#resource_toggle').click(function(event) {
      $('#resource_toggle').removeClass('btn-success').addClass('btn-primary');
      resource_toggle = !resource_toggle;
      setCookie('resource_toggle', resource_toggle);
      if (resource_toggle) {
        $('#resource_toggle').removeClass('btn-primary').addClass('btn-success');
      }
      update_visibility_of_markers();
    });
    $('#unit_toggle').click(function(event) {
      $('#unit_toggle').removeClass('btn-success').addClass('btn-primary');
      unit_toggle = !unit_toggle;
      setCookie('unit_toggle', unit_toggle);
      if (unit_toggle) {
        $('#unit_toggle').removeClass('btn-primary').addClass('btn-success');
      }
      update_visibility_of_markers();
    });
    $('#grid_toggle').click(function(event) {
      $('#grid_toggle').removeClass('btn-success').addClass('btn-primary');
      grid_toggle = !grid_toggle;
      setCookie('grid_toggle', grid_toggle);
      if (grid_toggle) {
        $('#grid_toggle').removeClass('btn-primary').addClass('btn-success');
        tiles_with_grid();
      }
      else {
        tiles_without_grid();
      }
    });
  }

  function use_toggle_cookies() {
    if (getCookie('border_toggle') != null) {
      border_toggle = getCookie('border_toggle') === 'true' ? true : false;
      if (border_toggle) {
        $('#border_toggle').removeClass('btn-warning').addClass('btn-success');
      }
      else {
        $('#border_toggle').removeClass('btn-success').addClass('btn-warning');
      }
    }
    if (getCookie('resource_toggle') != null) {
      resource_toggle = getCookie('resource_toggle') === 'true' ? true : false;
      if (resource_toggle) {
        $('#resource_toggle').removeClass('btn-primary').addClass('btn-success');
      }
      else {
        $('#resource_toggle').removeClass('btn-success').addClass('btn-primary');
      }
    }
    if (getCookie('settlement_toggle') != null) {
      settlement_toggle = getCookie('settlement_toggle') === 'true' ? true : false;
      if (settlement_toggle) {
        $('#settlement_toggle').removeClass('btn-primary').addClass('btn-success');
      }
      else {
        $('#settlement_toggle').removeClass('btn-success').addClass('btn-primary');
      }
    }
    if (getCookie('township_and_industry_toggle') != null) {
      township_and_industry_toggle = getCookie('township_and_industry_toggle');
      if (township_and_industry_toggle) {
        township_and_industry_toggle = parseInt(township_and_industry_toggle);
      }
      else {
        township_and_industry_toggle = 0;
      }
      if (township_and_industry_toggle == 0) {
        $('#township_industry_toggle').removeClass('btn-warning').removeClass('btn-success').addClass('btn-primary');
      }
      if (township_and_industry_toggle == 1) {
        $('#township_industry_toggle').removeClass('btn-primary').removeClass('btn-warning').addClass('btn-success');
      }
      if (township_and_industry_toggle == 2) {
        $('#township_industry_toggle').removeClass('btn-primary').removeClass('btn-success').addClass('btn-warning');
      }
    }
    if (getCookie('unit_toggle') != null) {
      unit_toggle = getCookie('unit_toggle') === 'true' ? true : false;
      if (unit_toggle) {
        $('#unit_toggle').removeClass('btn-primary').addClass('btn-success');
      }
      else {
        $('#unit_toggle').removeClass('btn-success').addClass('btn-primary');
      }
    }
    if (getCookie('grid_toggle') != null) {
      grid_toggle = getCookie('grid_toggle') === 'true' ? true : false;
      if (grid_toggle) {
        $('#grid_toggle').removeClass('btn-primary').addClass('btn-success');
      }
      else {
        $('#grid_toggle').removeClass('btn-success').addClass('btn-primary');
      }
    }
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
    for (var i in marker_set) {
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
    if (township_and_industry_toggle == 0) {
      set_marker_set_visibility(township_markers, false);
      set_marker_set_visibility(industry_markers, false);
    }
    if (township_and_industry_toggle == 1) {
      set_marker_set_visibility(township_markers, true);
      set_marker_set_visibility(industry_markers, false);
    }
    if (township_and_industry_toggle == 2) {
      set_marker_set_visibility(township_markers, false);
      set_marker_set_visibility(industry_markers, true);
    }
    set_marker_set_visibility(unit_markers, unit_toggle);
    if (grid_toggle) {
      tiles_with_grid();
    }
    else {
      tiles_without_grid();
    }
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
      zoom: focus_zoom,
      <?php } else { ?>

      // Map center is slightly north centric
      center: {
        lat: 20,
        lng: 0
      },
      // Zoom shows whole world but no repetition
      zoom: default_zoom,
      <?php } ?>
      // Prevent seeing north and south edge
      minZoom: max_zoom,
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

  function set_resource_icon(resource_id, tile_id, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/natural_resources/${resource_id}.png`, tile_id, lat, lng, false);
  }

  function set_industry_icon(industry_id, tile_id, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/industries/${industry_id}.png`, tile_id, lat, lng, false);
  }

  function set_township_icon(settlement_id, tile_id, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/settlements/${settlement_id}.png`, tile_id, lat, lng, false);
  }

  function set_settlement_icon(settlement_id, tile_id, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/settlements/${settlement_id}.png`, tile_id, lat, lng, false);
  }

  // Uses http://www.googlemapsmarkers.com/
  // http://www.googlemapsmarkers.com/v1/A/0099FF/FFFFFF/FF0000/
  // Becomes
  // https://chart.apis.google.com/chart?cht=d&chdp=mapsapi&chl=pin%27i%5c%27%5bA%27-2%27f%5chv%27a%5c%5dh%5c%5do%5c0099FF%27fC%5cFFFFFF%27tC%5cFF0000%27eC%5cLauto%27f%5c&ext=.png
  function set_unit_icon(unit_id, tile_id, terrain_key, unit_owner_key, unit_owner_color, lat, lng) {
    unit_owner_color = unit_owner_color.replace('#', '');
    let unit_key = unit_id;
    if (parseInt(terrain_key) === ocean_key) {
      unit_key = navy_key;
    }
    let path = `${base_url}resources/icons/units/${unit_key}.png`;
    unit = {
      unit_key: unit_id,
      unit_owner_key: unit_owner_key,
      unit_owner_color: unit_owner_color,
    }
    return set_marker_icon(path, tile_id, lat, lng, unit);
  }

  function update_unit_icon(marker, tile) {
    let unit_key = marker.unit.unit_key;
    if (parseInt(tile.terrain_key) === ocean_key) {
      unit_key = navy_key;
    }
    let this_icon = {
      url: `${base_url}resources/icons/units/${unit_key}.png`,
      scaledSize: new google.maps.Size(map_icon_size, map_icon_size),
      origin: new google.maps.Point(0,0),
      anchor: new google.maps.Point(map_icon_size / 2, map_icon_size / 2)
    };
    marker.setIcon(this_icon);
  }

  function set_marker_icon(path, tile_id, lat, lng, unit) {
    let draggable = false;
    let title = '';
    let this_icon = {
      url: path,
      scaledSize: new google.maps.Size(map_icon_size, map_icon_size),
      origin: new google.maps.Point(0,0),
      anchor: new google.maps.Point(map_icon_size / 2,map_icon_size / 2)
    };
    if (unit && unit.unit_owner_key == account.id) {
      draggable = true;
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
      tile_id: tile_id,
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
        resource_markers[<?= $tile['id']; ?>] = set_resource_icon(<?= $tile['resource_key']; ?>,<?= $tile['id'] ?>,<?= $tile['lat']; ?>, <?= $tile['lng']; ?>);
      <?php }
      if ($tile['settlement_key'] > METRO_KEY) { ?>
        settlement_markers[<?= $tile['id']; ?>] = set_settlement_icon(<?= $tile['settlement_key']; ?>, <?= $tile['id']; ?>, <?= $tile['lat']; ?>, <?= $tile['lng']; ?>);
      <?php }
      if ($tile['settlement_key'] > UNINHABITED_KEY && $tile['settlement_key'] <= METRO_KEY) { ?>
        township_markers[<?= $tile['id']; ?>] = set_township_icon(<?= $tile['settlement_key']; ?>, <?= $tile['id']; ?>, <?= $tile['lat']; ?>, <?= $tile['lng']; ?>);
      <?php }
      if ($tile['industry_key']) { ?>
        industry_markers[<?= $tile['id']; ?>] = set_industry_icon(<?= $tile['industry_key']; ?>, <?= $tile['id']; ?>, <?= $tile['lat']; ?>, <?= $tile['lng']; ?>);
      <?php }
      if ($tile['unit_key']) { ?>
        unit_markers[<?= $tile['id']; ?>] = set_unit_icon(<?= $tile['unit_key']; ?>, <?= $tile['id']; ?>, <?= $tile['terrain_key']; ?>, <?= $tile['unit_owner_key']; ?>, '<?= $tile['unit_owner_color']; ?>', <?= $tile['lat']; ?>, <?= $tile['lng']; ?>);
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

  function get_map_update() {
    if (active_requests['map_update']) {
      return;
    }
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
    }, 'map_update');
  }

  function pass_new_laws() {
    $('#laws_passed_confirm_icon').fadeTo(1, 0);
    $('#pass_new_laws_button').click(function(event) {
      let data = {
        world_key: world_key,
        input_power_structure: $('#input_power_structure').val(),
        input_tax_rate: $('#input_tax_rate').val(),
        input_ideology: $('input[name="input_ideology"]:checked').val(),
      };
      ajax_post('game/laws_form', data, function(response) {
        get_map_update();
        get_account_update();
        $('#laws_passed_confirm_icon').fadeTo(500, 1);
        $('#laws_passed_confirm_icon').fadeTo(2000, 0);
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
      new_tile.is_capitol = new_tile.is_capitol ? parseInt(new_tile.is_capitol) : null;
      new_tile.resource_key = new_tile.resource_key ? parseInt(new_tile.resource_key) : null;
      new_tile.settlement_key = new_tile.settlement_key ? parseInt(new_tile.settlement_key) : null;
      new_tile.unit_key = new_tile.unit_key ? parseInt(new_tile.unit_key) : null;
      border_color = get_tile_border_color(new_tile);
      fill_color = border_toggle ? border_color : tiles[new_tile['id']].fillColor;
      // Update settlement markers
      // Update unit markers
      tiles[new_tile['id']].setOptions({
        fillColor: fill_color,
        borders_fillColor: border_color,
      });

      update_tile_resource_marker(new_tile);
      update_tile_settlement_marker(new_tile);
      update_tile_township_marker(new_tile);
      update_tile_industry_marker(new_tile);
      update_tile_unit_marker(new_tile);
    }
    update_visibility_of_markers();
    return true;
  }
  function update_tile_resource_marker(tile) {
    if (resource_markers[tile.id]) {
      resource_markers[tile.id].setMap(null);
      resource_markers.splice(tile.id, 1);
    }
    if (tile.resource_key) {
      resource_markers[tile.id] = set_resource_icon(tile.resource_key, tile.id, tile.lat, tile.lng);
    }
  }
  function update_tile_settlement_marker(tile) {
    if (settlement_markers[tile.id]) {
      settlement_markers[tile.id].setMap(null);
      settlement_markers.splice(tile.id, 1);
    }
    if (tile.settlement_key > metro_key) {
      settlement_markers[tile.id] = set_settlement_icon(tile.settlement_key, tile.id, tile.lat, tile.lng);
    }
  }
  function update_tile_industry_marker(tile) {
    if (industry_markers[tile.id]) {
      industry_markers[tile.id].setMap(null);
      industry_markers.splice(tile.id, 1);
    }
    if (tile.industry_key) {
      industry_markers[tile.id] = set_industry_icon(tile.industry_key, tile.id, tile.lat, tile.lng);
    }
  }
  function update_tile_township_marker(tile) {
    if (township_markers[tile.id]) {
      township_markers[tile.id].setMap(null);
      township_markers.splice(tile.id, 1);
    }
    if (settlement_is_township(tile.settlement_key)) {
      township_markers[tile.id] = set_township_icon(tile.settlement_key, tile.id, tile.lat, tile.lng);
    }
  }
  function update_tile_unit_marker(tile) {
    if (unit_markers[tile.id] && unit_marker_unchanged(tile, unit_markers[tile.id])) {
      return;
    }
    if (unit_markers[tile.id]) {
      unit_markers[tile.id].setMap(null);
      unit_markers.splice(tile.id, 1);
    }
    else if (tile.unit_key) {
      unit_markers[tile.id] = set_unit_icon(tile.unit_key, tile.id, tile.terrain_key, tile.unit_owner_key, tile.unit_owner_color, tile.lat, tile.lng);
    }
  }
  function unit_marker_unchanged(tile, marker) {
    if (tile.unit_key != marker.unit.unit_key) {
      return false;
    }
    if (tile.unit_owner_key != marker.unit.unit_owner_key) {
      return false;
    }
    return true;
  }
  function needs_township_icon(tile) {
    return township_array.includes(tile.settlement_key) || parseInt(tile.is_capitol) || parseInt(tile.is_base);
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
    let start_tile_id = tiles_by_coord[start_lat + ',' + start_lng].tile_key;
    let end_tile_id = tiles_by_coord[end_lat + ',' + end_lng].tile_key;
    let lat = end_lat + (tile_size / 2);
    let lng = end_lng - (tile_size / 2);
    if (tiles_are_adjacent(start_lat, start_lng, end_lat, end_lng) && no_marker_at_square(lat, lng)) {
      unit_markers.splice(marker.tile_id, 1);
      unit_markers[end_tile_id] = marker;
      unit_markers[end_tile_id].tile_id = end_tile_id;
      allowed_move_to_new_position = true;
    }
    else {
      lat = start_lat + (tile_size / 2);
      lng = start_lng - (tile_size / 2);
    }
    let position = new google.maps.LatLng(lat, lng);
    marker.setPosition(position);
    start_lat = start_lng = null;
    return allowed_move_to_new_position;
  }

  function no_marker_at_square(lat, lng) {
    for (var i in unit_markers) {
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