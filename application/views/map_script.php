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
      current_map_type = 'terrain';
      tiles_to_terrain();
      set_marker_set_visibility(resource_markers, true);
      set_marker_set_visibility(settlement_markers, false);
      set_marker_set_visibility(unit_markers, false);
    });
    $('#borders_toggle').click(function(event) {
      current_map_type = 'borders';
      tiles_to_borders();
      set_marker_set_visibility(resource_markers, false);
      set_marker_set_visibility(settlement_markers, true);
      set_marker_set_visibility(unit_markers, true);
    });
    $('#empty_toggle').click(function(event) {
      current_map_type = 'empty';
      tiles_to_empty();
      set_marker_set_visibility(resource_markers, false);
      set_marker_set_visibility(settlement_markers, false);
      set_marker_set_visibility(unit_markers, false);
    });
  }

  function tiles_to_terrain() {
    Object.keys(tiles).forEach(function(key) {
      tiles[key].setOptions({
        fillColor: tiles[key].terrain_fillColor,
        fillOpacity: tiles[key].terrain_fillOpacity,
        strokeWeight: <?= STROKE_WEIGHT; ?>,
        strokeColor: '<?= STROKE_COLOR; ?>',
      });
    });
  }

  function tiles_to_borders() {
    Object.keys(tiles).forEach(function(key) {
      tiles[key].setOptions({
        fillColor: tiles[key].borders_fillColor,
        fillOpacity: tiles[key].borders_fillOpacity,
        strokeWeight: <?= STROKE_WEIGHT; ?>,
        strokeColor: '<?= STROKE_COLOR; ?>',
      });
    });
  }

  function tiles_to_empty() {
    Object.keys(tiles).forEach(function(key) {
      tiles[key].setOptions({
        fillOpacity: 0,
        strokeWeight: 0,
        strokeColor: 0,
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
    return set_marker_icon(`natural_resources/${resource_id}.png`, lat, lng);
  }

  function set_capitol_icon(lat, lng) {
    return set_marker_icon(`capitol.png`, lat, lng);
  }

  function set_settlement_icon(settlement_id, is_capitol, lat, lng) {
    if (is_capitol) {
      return set_capitol_icon(lat, lng);
    }
    return set_marker_icon(`settlements/${settlement_id}.png`, lat, lng);
  }

  function set_unit_icon(unit_id, lat, lng) {
    return set_marker_icon(`units/${unit_id}.png`, lat, lng);
  }

  function set_marker_icon(path, lat, lng) {
    let myLatLng = {
      lat: lat + 1,
      lng: lng - 1
    };
    let marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      // title: slug,
      // draggable:true,
      icon: {
        url: `../resources/icons/${path}`,
        scaledSize: new google.maps.Size(20, 20), // scaled size
        origin: new google.maps.Point(0,0), // origin
        anchor: new google.maps.Point(10,10) // anchor
      }
    });
    marker.setMap(map);
    marker.addListener('click', set_window);
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
        unit_markers.push(set_unit_icon(<?= $tile['unit_key']; ?>, <?= $tile['lat']; ?>, <?= $tile['lng']; ?>));
      <?php }
      ?>z(<?=
        $tile['id'] . ',' .
        $tile['lat'] . ',' .
        $tile['lng'] . ',' .
        '"' . $terrain_color . '",' .
        '"' . $border_color . '"'
      ; ?>);<?php // Open and close immediately to avoid whitespace eating bandwidth
    } ?>

    if (use_borders) {
      set_marker_set_visibility(resource_markers, false);
    }
    else {
      set_marker_set_visibility(settlement_markers, false);
      set_marker_set_visibility(unit_markers, false);
    }
  }

  // Declare square called by performance sensitive loop
  function z(tile_key, tile_lat, tile_lng, terrain_fill_color, border_fill_color) {
    let current_fill_color = use_borders ? border_fill_color : terrain_fill_color;
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
      fillOpacity: <?= $tile['terrain_key'] === OCEAN_KEY ? 0 : TILE_OPACITY; ?>,
      strokeWeight: <?= STROKE_WEIGHT; ?>,
      strokeColor: '<?= STROKE_COLOR; ?>',
      fillColor: current_fill_color,
      terrain_fillColor: terrain_fill_color,
      borders_fillColor: border_fill_color,
    });
    polygon.setMap(map);
    polygon.addListener('click', set_window);
    tiles[tile_key] = polygon;
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
    // Loop through tiles
    // This loop may run up to 15,000 times, so focus is performance
    number_of_tiles = new_tiles.length;
    for (i = 0; i < number_of_tiles; i++) {
      // Set variables
      new_tile = new_tiles[i];
      fill_opacity = <?= TILE_OPACITY; ?>;
      fill_color = "#0000ff";
      if (new_tile['terrain_key'] == <?= FERTILE_KEY; ?>) {
        fill_color = '<?= FERTILE_COLOR; ?>';
      }
      if (new_tile['terrain_key'] == <?= BARREN_KEY; ?>) {
        fill_color = '<?= BARREN_COLOR; ?>';
      }
      if (new_tile['terrain_key'] == <?= MOUNTAIN_KEY; ?>) {
        fill_color = '<?= MOUNTAIN_COLOR; ?>';
      }
      if (new_tile['terrain_key'] == <?= TUNDRA_KEY; ?>) {
        fill_color = '<?= TUNDRA_COLOR; ?>';
      }
      if (new_tile['terrain_key'] == <?= COASTAL_KEY; ?>) {
        fill_color = '<?= COASTAL_COLOR; ?>';
      }
      if (new_tile['terrain_key'] == <?= OCEAN_KEY; ?>) {
        fill_color = '<?= OCEAN_COLOR; ?>';
      }

      // Apply variables to box
      tiles[new_tile['id']].setOptions({
        fillColor: fill_color,
        fillOpacity: fill_opacity
      });

    }

    return true;
  }

  function blind_land_attack(lng, lat, world_key, type, callback) {
    $.ajax({
      url: "<?=base_url()?>tile_form",
      type: "POST",
      data: {
        lng: lng,
        lat: lat,
        world_key: world_key,
      },
      cache: false,
      success: function(data) {
        callback(data);
        return true;
      }
    });
  }

  function update_tile_terrain(lng, lat, world_key, type, callback) {
    $.ajax({
      url: "<?=base_url()?>tile_form",
      type: "POST",
      data: {
        lng: lng,
        lat: lat,
        world_key: world_key,
      },
      cache: false,
      success: function(data) {
        callback(data);
        return true;
      }
    });
  }

  function set_window(event) {
    // Not sure why subtracting tile_size on lat makes this work, but results in correct behavior
    var lat = round_down(event.latLng.lat()) - tile_size;
    var lng = round_down(event.latLng.lng());

    if (attack_key_pressed) {
      update_tile_terrain(lng, lat, world_key, 'attack', function(response) {
      // blind_land_attack(lng, lat, world_key, 'attack', function(response) {
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