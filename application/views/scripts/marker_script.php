<script>

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

  function set_resource_icon(resource_id, tile_id, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/natural_resources/${resource_id}.png`, tile_id, lat, lng, false);
  }

  function set_industry_icon(industry_id, tile_id, lat, lng) {
    if (!industry_id) {
      industry_id = 0;
    }
    return set_marker_icon(`${base_url}resources/icons/industries/${industry_id}.png`, tile_id, lat, lng, false);
  }

  function set_township_icon(settlement_id, tile_id, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/settlements/${settlement_id}.png`, tile_id, lat, lng, false);
  }

  function set_settlement_icon(settlement_id, tile_id, lat, lng) {
    return set_marker_icon(`${base_url}resources/icons/settlements/${settlement_id}.png`, tile_id, lat, lng, false);
  }

  function set_unit_icon(unit_id, tile_id, terrain_key, unit_owner_key, unit_owner_color, lat, lng) {
    unit_owner_color = unit_owner_color.replace('#', '');
    let unit_key = unit_id;
    if (parseInt(terrain_key) === ocean_key) {
      unit_key = navy_key;
    }
    let unit_color = 'neutral';
    if (unit_owner_key == account.id) {
      unit_color = 'own';
    }
    else if (find_treaty_by_account_key(unit_owner_key) == war_key) {
      unit_color = 'enemy';
    }
    else if (find_treaty_by_account_key(unit_owner_key) == passage_key) {
      unit_color = 'ally';
    }
    let path = `${base_url}resources/icons/units/${unit_key}-${unit_color}.png`;
    unit = {
      unit_key: unit_id,
      unit_color: unit_color,
      unit_owner_key: unit_owner_key,
      unit_owner_color: unit_owner_color,
    }
    return set_marker_icon(path, tile_id, lat, lng, unit);
  }

  function set_marker_icon(path, tile_id, lat, lng, unit) {
    let this_icon = {
      url: path,
      scaledSize: new google.maps.Size(map_icon_size, map_icon_size),
      origin: new google.maps.Point(0,0),
      anchor: new google.maps.Point(map_icon_size / 2,map_icon_size / 2)
    };
    let myLatLng = {
      lat: parseInt(lat) + (tile_size / 2),
      lng: parseInt(lng) - (tile_size / 2)
    };
    let this_marker = {
      position: myLatLng,
      map: map,
      draggable: false,
      icon: this_icon,
      unit: unit,
      tile_id: tile_id,
      title: '',
    };
    if (unit) {
      this_marker.optimized = false;
      this_marker.zIndex = 10000;
      if (unit.unit_owner_key == account.id) {
        this_marker.draggable = true;
      }
    }
    let marker = new google.maps.Marker(this_marker);
    marker.setMap(map);
    if (this_marker.draggable) {
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

  function update_tile_resource_marker(tile) {
    if (resource_markers[tile.id]) {
      resource_markers[tile.id].setMap(null);
      delete resource_markers[tile.id];
    }
    if (tile.resource_key) {
      resource_markers[tile.id] = set_resource_icon(tile.resource_key, tile.id, tile.lat, tile.lng);
    }
  }

  function update_tile_settlement_marker(tile) {
    if (settlement_markers[tile.id]) {
      settlement_markers[tile.id].setMap(null);
      delete settlement_markers[tile.id];
    }
    if (tile.settlement_key > metro_key) {
      settlement_markers[tile.id] = set_settlement_icon(tile.settlement_key, tile.id, tile.lat, tile.lng);
    }
  }

  function update_tile_industry_marker(tile) {
    if (industry_markers[tile.id]) {
      industry_markers[tile.id].setMap(null);
      delete industry_markers[tile.id];
    }
    if (settlement_is_township(tile.settlement_key)) {
      industry_markers[tile.id] = set_industry_icon(tile.industry_key, tile.id, tile.lat, tile.lng);
    }
  }

  function update_tile_township_marker(tile) {
    if (township_markers[tile.id]) {
      township_markers[tile.id].setMap(null);
      delete township_markers[tile.id];
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
      delete unit_markers[tile.id];
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
</script>