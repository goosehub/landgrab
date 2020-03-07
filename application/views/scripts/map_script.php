<script>
  pass_new_laws();
  attack_key_listen();
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
    keys['a'] = 65;
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
    $('#terrain_toggle').click(function(event) {
      $('#borders_toggle').removeClass('active');
      $('#terrain_toggle').addClass('active');
      borders_toggle = false;
      current_map_type = 'terrain';
      tiles_to_terrain();
      set_marker_set_visibility(resource_markers, true);
      set_marker_set_visibility(settlement_markers, false);
    });
    $('#borders_toggle').click(function(event) {
      $('#terrain_toggle').removeClass('active');
      $('#borders_toggle').addClass('active');
      borders_toggle = true;
      current_map_type = 'borders';
      tiles_to_borders();
      set_marker_set_visibility(resource_markers, false);
      set_marker_set_visibility(settlement_markers, true);
    });
    $('#grid_toggle').click(function(event) {
      $('#grid_toggle').removeClass('active');
      grid_toggle = !grid_toggle;
      if (grid_toggle) {
        $('#grid_toggle').addClass('active');
        tiles_without_grid();
      }
      else {
        tiles_with_grid();
      }
    });
    $('#unit_toggle').click(function(event) {
      $('#unit_toggle').removeClass('active');
      unit_toggle = !unit_toggle;
      if (unit_toggle) {
        $('#unit_toggle').addClass('active');
      }
      set_marker_set_visibility(unit_markers, unit_toggle);
    });
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
    return set_marker_icon(`${base_url}/resources/icons/natural_resources/${resource_id}.png`, lat, lng, false);
  }

  function set_capitol_icon(lat, lng) {
    return set_marker_icon(`${base_url}/resources/icons/capitol.png`, lat, lng, false);
  }

  function set_settlement_icon(settlement_id, is_capitol, lat, lng) {
    if (is_capitol) {
      return set_capitol_icon(lat, lng);
    }
    return set_marker_icon(`${base_url}/resources/icons/settlements/${settlement_id}.png`, lat, lng, false);
  }

  // Uses http://www.googlemapsmarkers.com/
  // http://www.googlemapsmarkers.com/v1/A/0099FF/FFFFFF/FF0000/
  // Becomes
  // https://chart.apis.google.com/chart?cht=d&chdp=mapsapi&chl=pin%27i%5c%27%5bA%27-2%27f%5chv%27a%5c%5dh%5c%5do%5c0099FF%27fC%5cFFFFFF%27tC%5cFF0000%27eC%5cLauto%27f%5c&ext=.png
  function set_unit_icon(unit_id, unit_owner_color, lat, lng) {
    unit_owner_color = unit_owner_color.replace('#', '');
    unit_color = unit_types[unit_id - 1].color;
    character = unit_types[unit_id - 1].character;
    let path = `http://www.googlemapsmarkers.com/v1/${character}/${unit_owner_color}/${stroke_color}/${stroke_color}`;
    return set_marker_icon(path, lat, lng, true);
  }

  function set_marker_icon(path, lat, lng, is_unit) {
    if (is_unit) {
      var draggable = true;
      lat = lat - (tile_size / 4);
      var this_icon = {
        url: path,
      };
    }
    else {
      var draggable = false;
      var this_icon = {
        url: path,
        scaledSize: new google.maps.Size(20, 20),
        origin: new google.maps.Point(0,0),
        anchor: new google.maps.Point(10,10)
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
      icon: this_icon
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
        settlement_markers.push(set_settlement_icon(<?= $tile['settlement_key']; ?>, <?= $tile['is_capitol'] ? '1' : '0'; ?>, <?= $tile['lat']; ?>, <?= $tile['lng']; ?>));
      <?php }
      if ($tile['unit_key']) { ?>
        unit_markers.push(set_unit_icon(<?= $tile['unit_key']; ?>, '<?= $tile['unit_owner_color']; ?>', <?= $tile['lat']; ?>, <?= $tile['lng']; ?>));
      <?php }
      ?>z(<?=
        $tile['id'] . ',' .
        $tile['lat'] . ',' .
        $tile['lng'] . ',' .
        '"' . $terrain_color . '",' .
        '"' . $border_color . '"'
      ; ?>);<?php // Open and close immediately to avoid whitespace eating bandwidth
    } ?>

    if (borders_toggle) {
      set_marker_set_visibility(resource_markers, false);
    }
    else {
      set_marker_set_visibility(settlement_markers, false);
    }
    if (unit_toggle) {

    }
    else {
      set_marker_set_visibility(unit_markers, false);
    }
  }

  // Declare square called by performance sensitive loop
  function z(tile_key, tile_lat, tile_lng, terrain_color, border_color) {
    let current_fill_color = borders_toggle ? border_color : terrain_color;
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
    $.ajax({
      url: "<?=base_url()?>game/get_this_full_account/" + world_key,
      type: "GET",
      data: {
        json: "true"
      },
      cache: false,
      success: function(response) {
        account = JSON.parse(response);
        update_supplies(account.supplies);
      }
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
    $.ajax({
      url: "<?=base_url()?>world/" + world_key,
      type: "GET",
      data: {
        json: "true"
      },
      cache: false,
      success: function(response) {
        data = JSON.parse(response);

        // Check for refresh signal from server 
        if (data['refresh']) {
          alert('The game is being updated, and we need to refresh your screen. This page will refresh after you press ok');
          window.location.reload();
        }

        if (account && !data['account']) {
          alert('You were away too long and you\'re session has expired, please log back in.');
          window.location.href = '<?= base_url(); ?>world/' + world_key + '?login';
          return false;
        }

        update_tiles(data['tiles']);
      }
    });
  }

  function pass_new_laws() {
    $('#pass_new_laws_button').click(function(event) {
      $.ajax({
        url: "<?=base_url()?>laws_form",
        type: 'POST',
        dataType: 'json',
        data: $('#laws_form').serialize(),
        success: function(data) {
          // Handle error
          if (data['error']) {
            alert(data['error']);
            return false;
          }
          // Do update, don't think this is needed though?
          // get_map_update(world_key);
        }
      });
    });
  }

  function update_tiles(new_tiles) {
    // This loop may rarely run up to 15,000 times, so focus is a performance
    number_of_tiles = new_tiles.length;
    for (i = 0; i < number_of_tiles; i++) {
      let new_tile = new_tiles[i];
      border_color = get_tile_border_color(new_tile);
      fill_color = borders_toggle ? border_color : tiles[new_tile['id']].fillColor;
      // Update settlement markers
      // Update unit markers
      tiles[new_tile['id']].setOptions({
        fillColor: fill_color,
        borders_fillColor: border_color,
      });
    }
    return true;
  }

  function get_tile_border_color(tile) {
    let fill_color = "#FFFFFF";
    if (tile['account_key']) {
      fill_color = tile['color'];
    }
    return fill_color;
  }

  function get_tile_terrain_color(terrain_key) {
    let terrain_color = false;
    if (terrain_key == <?= FERTILE_KEY; ?>) {
      terrain_color = '<?= FERTILE_COLOR; ?>';
    }
    if (terrain_key == <?= BARREN_KEY; ?>) {
      terrain_color = '<?= BARREN_COLOR; ?>';
    }
    if (terrain_key == <?= MOUNTAIN_KEY; ?>) {
      terrain_color = '<?= MOUNTAIN_COLOR; ?>';
    }
    if (terrain_key == <?= TUNDRA_KEY; ?>) {
      terrain_color = '<?= TUNDRA_COLOR; ?>';
    }
    if (terrain_key == <?= COASTAL_KEY; ?>) {
      terrain_color = '<?= COASTAL_COLOR; ?>';
    }
    if (terrain_key == <?= OCEAN_KEY; ?>) {
      terrain_color = '<?= OCEAN_COLOR; ?>';
    }
    return terrain_color;
  }

  function update_tile_terrain(lng, lat, world_key, type, callback) {
    $.ajax({
      url: "<?=base_url()?>tile_form",
      type: "POST",
      data: {
        world_key: world_key,
        lng: lng,
        lat: lat,
      },
      cache: false,
      success: function(data) {
        callback(data);
        return true;
      }
    });
  }

  function start_drag_unit(event, marker) {
    // Not sure why subtracting tile_size on lat makes this work, but results in correct behavior
    start_lat = round_down(event.latLng.lat()) - tile_size;
    start_lng = round_down(event.latLng.lng());
    highlight_valid_squares(start_lat, start_lng);
  }


  function end_drag_unit(event, marker) {
    // Not sure why subtracting tile_size on lat makes this work, but results in correct behavior
    let end_lat = round_down(event.latLng.lat()) - tile_size;
    let end_lng = round_down(event.latLng.lng());
    end_lng = correct_lng(end_lng);
    move_marker_to_new_position(marker, start_lat, start_lng, end_lat, end_lng);
    unhighlight_valid_squares(start_lat, start_lng);
    request_unit_attack(marker, start_lat, start_lng, end_lat, end_lng, function(marker){
      get_map_update();
    });
  }

  function request_unit_attack(marker, start_lat, start_lng, end_lat, end_lng, callback) {
    $.ajax({
      url: "<?=base_url()?>game/unit_move_to_land",
      type: "POST",
      data: {
        world_key: world_key,
        start_lat: start_lat,
        start_lng: start_lng,
        end_lat: end_lat,
        end_lng: end_lng,
      },
      cache: false,
      success: function(data) {
        callback(data);
        return true;
      }
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

  function unhighlight_valid_squares() {
    highlighted_tiles = [];
    if (borders_toggle) {
      tiles_to_borders();
    }
    else {
      tiles_to_terrain();
    }
  }

  function correct_lng(lng) {
    if (lng === 182) {
      lng = -178;
    }
    if (lng === -180) {
      lng = 180;
    }
    return lng;
  }

  function move_marker_to_new_position(marker, start_lat, start_lng, end_lat, end_lng) {
    let allowed_move_to_new_position = false;
    if (tile_in_range(start_lat, start_lng, end_lat, end_lng)) {
      lat = end_lat;
      lng = end_lng;
      allowed_move_to_new_position = true;
    }
    else {
     lat = start_lat;
     lng = start_lng; 
    }
    lat = lat - (tile_size / 4);
    lat = lat + (tile_size / 2);
    lng = lng - (tile_size / 2);
    let position = new google.maps.LatLng(lat, lng);
    marker.setPosition(position);
    start_lat = start_lng = null;
    return allowed_move_to_new_position;
  }

  function tile_in_range(start_lat, start_lng, end_lat, end_lng) {
    // Ignore if ending same place we started
    if (start_lat === end_lat && start_lng === end_lng) {
      return false;
    }
    // Check if one is changed by 1, and other is the same
    allowed_lats = [start_lat, start_lat + tile_size, start_lat - tile_size];
    allowed_lngs = [start_lng, correct_lng(start_lng + tile_size), correct_lng(start_lng - tile_size)];
    if (
      (allowed_lats.includes(end_lat) && start_lng === end_lng) || 
      (allowed_lngs.includes(end_lng) && start_lat === end_lat)
      ) {
      return true;
    }
    return false;
  }

  function tile_in_range_diagonal(start_lat, start_lng, end_lat, end_lng) {
    // Ignore if ending same place we started
    if (start_lat === end_lat && start_lng === end_lng) {
      return false;
    }
    // Check that nothing is more than 1 tile size away from standard
    allowed_lats = [start_lat, start_lat + tile_size, start_lat - tile_size];
    allowed_lngs = [start_lng, start_lng + tile_size, start_lng - tile_size];
    if (allowed_lats.includes(end_lat) && allowed_lngs.includes(end_lng)) {
      return true;
    }
    return false;
  }

  function open_tile(event) {
    // Not sure why subtracting tile_size on lat makes this work, but results in correct behavior
    var lat = round_down(event.latLng.lat()) - tile_size;
    var lng = round_down(event.latLng.lng());
    lng = correct_lng(lng);

    if (attack_key_pressed) {
      update_tile_terrain(lng, lat, world_key, 'attack', function(response) {
        get_map_update();
      });
      return true;
    }

    $('.center_block').fadeOut(100);

    tile = get_single_tile(lat, lng, world_key, function(tile) {
      d = JSON.parse(tile);

      if (d['error']) {
        alert(d['error']);
        return false;
      }

      current_tile = d;
      populate_tile_window(d);
    });
  }

  function populate_tile_window(d) {
    $('#tile_block').show();

    tile_name(d);
    tile_desc(d);
    tile_coord_link(d);
    tile_owner_info(d);
    tile_owner_username(d);
    tile_owner_country_name(d);
    tile_terrain(d);
    tile_resource_icon(d);
    tile_resource(d);
    tile_settlement_label(d);
    tile_industry_label(d);
    tile_population(d);
    tile_gdp(d);
    tile_register_plea(d);
    tile_first_claim(d);
    tile_unit(d);
    settlement_select(d);
    industry_select(d);
  }

  function get_single_tile(lat, lng, world_key, callback) {
    $.ajax({
      url: "<?=base_url()?>get_single_tile",
      type: "GET",
      data: {
        lat: lat,
        lng: lng,
        world_key: world_key
      },
      cache: false,
      success: function(data) {
        callback(data);
      }
    });
  }

</script>