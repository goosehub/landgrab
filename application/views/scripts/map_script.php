<script>
  use_toggle_cookies();
  attack_key_listen();
  map_toggle_listen();

  setInterval(function() {
    get_map_update();
  }, map_update_interval_ms);

  // Update units once in a while incase of sent treaty signed
  setInterval(function() {
    get_map_update('update_units');
  }, map_units_update_interval_ms);

  function initMap() {
    set_map();
    remove_overlay();
    generate_tiles();
    default_open_block();
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
        mapTypeIds: ['satellite', 'hybrid', 'terrain', 'paper_map', 'panther_map']
      }
    });

    styled_map_map_paper = new google.maps.StyledMapType(map_paper, {name: 'Paper'});
    map.mapTypes.set('paper_map', styled_map_map_paper);
    styled_map_map_panther = new google.maps.StyledMapType(map_panther, {name: 'Panther'});
    map.mapTypes.set('panther_map', styled_map_map_panther);

    map.setMapTypeId('paper_map');
  }

  function styled_map_toggle() {
    styled_map_toggle = getCookie('styled_map_toggle') == null ? default_map : (getCookie('styled_map_toggle') === 'true' ? true : false);
    google.maps.event.addListener(map, 'maptypeid_changed', function() { 
      console.log('marco');
      console.log(map.getMapTypeId());
      setCookie('styled_map_toggle', map.getMapTypeId());
    });
  }

  function remove_overlay() {
    // Remove loading overlay based on tiles loaded status
    google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
      $('#overlay').fadeOut();
    });
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
      if (in_array($tile['settlement_key'], [TOWN_KEY, CITY_KEY, METRO_KEY])) { ?>
        industry_markers[<?= $tile['id']; ?>] = set_industry_icon(<?= (int)$tile['industry_key']; ?>, <?= $tile['id']; ?>, <?= $tile['lat']; ?>, <?= $tile['lng']; ?>);
      <?php }
      if ($tile['unit_key']) { ?>
        unit_markers[<?= $tile['id']; ?>] = set_unit_icon(<?= $tile['unit_key']; ?>, <?= $tile['id']; ?>, <?= $tile['terrain_key']; ?>, <?= $tile['unit_owner_key']; ?>, '<?= $tile['unit_owner_color']; ?>', <?= $tile['lat']; ?>, <?= $tile['lng']; ?>);
      <?php }
      ?>z(<?=
        $tile['id'] . ',' .
        $tile['lat'] . ',' .
        $tile['lng'] . ',' .
        ($tile['settlement_key'] ? $tile['settlement_key'] : 'null') . ',' .
        ($tile['account_key'] ? $tile['account_key'] : 'null') . ',' .
        '"' . $terrain_color . '",' .
        '"' . $border_color . '"'
      ; ?>);<?php // Open and close immediately to avoid whitespace eating bandwidth
    } ?>

    update_visibility_of_markers();
  }

  // Declare square called by performance sensitive loop
  function z(tile_key, tile_lat, tile_lng, settlement_key, account_key, terrain_color, border_color) {
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
      settlement_key: settlement_key,
      fillOpacity: <?= TILE_OPACITY; ?>,
      strokeWeight: <?= STROKE_WEIGHT; ?>,
      strokeColor: '<?= STROKE_COLOR; ?>',
      fillColor: current_fill_color,
      terrain_fillColor: terrain_color,
      borders_fillColor: border_color,
      account_key: account_key,
    });
    polygon.setMap(map);
    polygon.addListener('click', open_tile);
    tiles[tile_key] = polygon;
    tiles_by_coord[tile_lat + ',' + tile_lng] = tiles[tile_key];
  }

  function get_map_update(update_type = 'update_world') {
    if (active_requests['map_update']) {
      return;
    }
    ajax_get('game/' + update_type + '/' + world_key, function(response) {
      // Check for refresh signal from server 
      if (response['refresh']) {
        alert('The game is being updated, and we need to refresh your screen. This page will refresh after you press ok');
        window.location.reload();
      }

      winner_winner_chicken_dinner(response['winner_winner_chicken_dinner']);

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

  function winner_winner_chicken_dinner(winner) {
    if (!winner) {
      $('#winner_winner_chicken_dinner_parent').hide();
    }
    if (winner && !game_won_acknowledge) {
      $('#winner_victory_industry').html(winner['industry']['label']);
      $('#winner_username').html(winner['username']);
      $('#winner_nation_name').html(winner['nation_name']);
      $('#winner_nation_flag').prop('src', `${base_url}uploads/${winner['nation_flag']}`);
      $('#winner_leader_portrait').prop('src', `${base_url}uploads/${winner['leader_portrait']}`);
      $('.winner_text_shadow').css('text-shadow', '0px 2px 2px ' + winner['color']);
      $('.winner_text_color').css('background-color', winner['color']);
      $('.winner_background_color').css('background-color', winner['color']);
      $('#winner_winner_chicken_dinner_parent').show();
      game_won_acknowledge = true;
    }
  }

  function update_tiles(new_tiles) {
    // This loop may rarely run up to 15,000 times, so focus is a performance
    number_of_tiles = new_tiles.length;
    for (let i = 0; i < number_of_tiles; i++) {
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
        account_key: new_tile.account_key,
        settlement_key: new_tile.settlement_key,
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