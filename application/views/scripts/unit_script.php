<script>
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

  function update_unit_icon(marker, tile) {
    let unit_key = marker.unit.unit_key;
    if (parseInt(tile.terrain_key) === ocean_key) {
      unit_key = navy_key;
    }
    let this_icon = {
      url: `${base_url}resources/icons/units/${unit_key}-${marker.unit.unit_color}.png`,
      scaledSize: new google.maps.Size(map_icon_size, map_icon_size),
      origin: new google.maps.Point(0,0),
      anchor: new google.maps.Point(map_icon_size / 2, map_icon_size / 2),
    };
    marker.setIcon(this_icon);
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

  function move_unit_to_new_position(marker, start_lat, start_lng, end_lat, end_lng) {
    let allowed_move_to_new_position = false;
    let start_tile_id = tiles_by_coord[start_lat + ',' + start_lng].tile_key;
    let end_tile_id = tiles_by_coord[end_lat + ',' + end_lng].tile_key;
    let lat = end_lat + (tile_size / 2);
    let lng = end_lng - (tile_size / 2);
    if (tiles_are_adjacent(start_lat, start_lng, end_lat, end_lng) && no_unit_at_square(lat, lng)) {
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

  function no_unit_at_square(lat, lng) {
    for (var i in unit_markers) {
      if (unit_markers[i].getPosition().lat() == lat && unit_markers[i].getPosition().lng() == lng) {
        return false;
      }
    }
    return true;
  }
</script>