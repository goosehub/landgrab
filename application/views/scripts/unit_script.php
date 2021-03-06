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
    let moved = unit_can_move_to_new_tile(marker, start_lat, start_lng, end_lat, end_lng);
    if (!moved) {
      return;
    }
    highlighted_tiles = [];

    // Wait for last unit move request to finish if needed
    if (unit_move_request_in_progress) {
      setTimeout(function(){
        end_drag_unit(event, marker)
      }, 500);
      return;
    }

    unit_move_request_in_progress = true;
    request_unit_attack(marker, start_lat, start_lng, end_lat, end_lng, function(response){
      unit_move_request_in_progress = false;
      update_tutorial_after_move_unit();
      update_unit_icon(marker, response.tile);
      animate_combat(response.combat);
      render_comat_bonuses(response.combat);
      move_unit_to_new_tile(marker, response.combat, start_lat, start_lng, end_lat, end_lng);
      get_map_update();
    }, function(response){
      unit_move_request_in_progress = false;
      marker.setMap(null);
      delete unit_markers[marker.tile_id];
    });
  }

  function render_comat_bonuses(combat) {
    if (!combat) {
      return;
    }
    $('#matchup_offensive_bonus_parent').hide();
    $('#terrain_offensive_bonus_parent').hide();
    $('#matchup_defensive_bonus_parent').hide();
    $('#terrain_defensive_bonus_parent').hide();
    $('#township_defensive_bonus_parent').hide();
    if (combat.matchup_offensive_bonus) {
      $('#matchup_offensive_bonus_parent').show();
    }
    if (combat.terrain_offensive_bonus) {
      $('#terrain_offensive_bonus_parent').show();
    }
    if (combat.matchup_defensive_bonus) {
      $('#matchup_defensive_bonus_parent').show();
    }
    if (combat.terrain_defensive_bonus) {
      $('#terrain_defensive_bonus_parent').show();
    }
    if (combat.township_defensive_bonus) {
      $('#township_defensive_bonus_parent').show();
    }
    $('#attack_power').html('+' + combat.attack_power);
    $('#defend_power').html('+' + combat.defend_power);
    $('#matchup_offensive_bonus').html('+' + combat.matchup_offensive_bonus);
    $('#terrain_offensive_bonus').html('+' + combat.terrain_offensive_bonus);
    $('#matchup_defensive_bonus').html('+' + combat.matchup_defensive_bonus);
    $('#terrain_defensive_bonus').html('+' + combat.terrain_defensive_bonus);
    $('#township_defensive_bonus').html('+' + combat.township_defensive_bonus);
  }

  // https://getbootstrap.com/docs/3.3/components/#progress-stacked
  function animate_combat(combat) {
    if (!combat) {
      return false;
    }
    let total_power = combat.total_power;
    let victory = combat.victory;
    let victory_bar_percent = parseInt( (combat.combat_result * 100) / combat.total_power );
    let defender_percent = parseInt( (combat.defend_power * 100) / combat.total_power );
    let attacker_unit_path = `${base_url}resources/icons/units/${combat.attacker_unit_key}-own.png`;
    let defender_unit_path = `${base_url}resources/icons/units/${combat.defender_unit_key}-enemy.png`;
    let combat_wait_ms = parseInt(combat_animate_ms / 2);
    let defender_bar_percent = parseInt(defender_percent);
    let remaining_bar_percent = parseInt(100 - defender_percent);
    let fadeout_ms = 500;

    $('.center_block').hide();
    $('.defeat_message').hide();
    $('.victory_message').hide();
    $('.combat_pending_message').show();
    $('#chance_of_victory_text').html(remaining_bar_percent);
    $('#defender_unit_image').prop('src', attacker_unit_path);
    $('#attacker_unit_image').prop('src', defender_unit_path);
    $('#victory_bar').css('width', 0 + '%');
    $('#defender_bar').css('width', defender_bar_percent + '%');
    $('#total_bar').css('width', remaining_bar_percent + '%');
    $('#combat_block').show();

    setTimeout(function(){
      $('#victory_bar').css('width', victory_bar_percent + '%');
      let defender_bar_percent = parseInt(defender_percent - victory_bar_percent) > 0 ? parseInt(defender_percent - victory_bar_percent) : 0;
      $('#defender_bar').css('width', defender_bar_percent + '%');
      let remaining_bar_percent = parseInt(100 - victory_bar_percent - defender_bar_percent);
      $('#total_bar').css('width', remaining_bar_percent + '%');
      victory_message_render(victory);
      setTimeout(function(){
        $('#combat_block').fadeOut(fadeout_ms);
      }, combat_wait_ms);
    }, combat_wait_ms);
  }

  function victory_message_render(victory) {
    $('.combat_pending_message').hide();
    if (victory) {
      $('.victory_message').show();
    }
    else {
      $('.defeat_message').show();
    }
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

  function request_unit_attack(marker, start_lat, start_lng, end_lat, end_lng, callback, fail_callback) {
    let data = {
      world_key: world_key,
      start_lat: start_lat,
      start_lng: start_lng,
      end_lat: end_lat,
      end_lng: end_lng,
    };
    ajax_post('game/unit_move_to_land', data, function(tile) {
      callback(tile);
    }, null, function(fail) {
      fail_callback();
    });
  }

  function unit_can_move_to_new_tile(marker, start_lat, start_lng, end_lat, end_lng) {
    // Define variables
    let lat = start_lat + (tile_size / 2);
    let lng = start_lng - (tile_size / 2);
    let final_end_lat = end_lat + (tile_size / 2);
    let final_end_lng = end_lng - (tile_size / 2);
    let allowed_move_to_new_position = false;
    let start_tile_id = tiles_by_coord[start_lat + ',' + start_lng].tile_key;
    let end_tile_id = tiles_by_coord[end_lat + ',' + end_lng].tile_key;
    let end_tile_account_key = tiles_by_coord[end_lat + ',' + end_lng].account_key;
    let settlement_key = tiles_by_coord[end_lat + ',' + end_lng].settlement_key;

    // Check conditions
    if (!tiles_are_adjacent(start_lat, start_lng, end_lat, end_lng)) {
      // No alert, probably not intentional
    }
    else if (account.supplies.support.amount <= 0) {
      swal('', 'You can not move units without political support', 'warning');
    }
    else if (!no_own_unit_at_square(final_end_lat, final_end_lng)) {
      // swal('', 'You can not stack units', 'warning');
      // Workaround, will allow users to attempt to stack units client side
      lat = final_end_lat;
      lng = final_end_lng;
      allowed_move_to_new_position = true;
    }
    else if (!no_friendly_unit_at_square(final_end_lat, final_end_lng)) {
      swal('', 'You must declare war through diplomacy before you can attack this unit', 'warning');
    }
    else if (is_friendly_square(end_lat, end_lng)) {
      swal('', 'You must declare war through diplomacy before you can attack this land', 'warning');
    }
    else if (!unit_can_take_settlement(end_tile_account_key, settlement_key, marker.unit.unit_key)) {
      swal('', 'This unit is not able to take this size of township', 'warning');
    }
    else {
      lat = final_end_lat;
      lng = final_end_lng;
      allowed_move_to_new_position = true;
    }

    // Move marker into new position
    let position = new google.maps.LatLng(lat, lng);
    marker.setPosition(position);
    start_lat = start_lng = null;
    return allowed_move_to_new_position;
  }

  function move_unit_to_new_tile(marker, combat, start_lat, start_lng, end_lat, end_lng) {
    let start_tile_id = tiles_by_coord[start_lat + ',' + start_lng].tile_key;
    let end_tile_id = tiles_by_coord[end_lat + ',' + end_lng].tile_key;

    // Wait to remove old marker so it's seemless between old and new marker
    setTimeout(function(){
      marker.setMap(null);
    }, unit_linger_ms);

    // If replacing marker, remove marker being replaced
    if (combat && combat.victory && unit_markers[end_tile_id]) {
      unit_markers[end_tile_id].setMap(null);
      delete unit_markers[end_tile_id];
    }
  }

  function unit_can_take_settlement(end_tile_account_key, settlement_key, unit_key) {
    if (end_tile_account_key == account.id) {
      return true;
    }
    if (settlement_key == town_key) {
      if (unit_key == airforce_key) {
        return false;
      }
    }
    if (settlement_key == city_key) {
      if (unit_key == airforce_key) {
        return false;
      }
    }
    if (settlement_key == metro_key) {
      if (unit_key == airforce_key) {
        return false;
      }
      if (unit_key == infantry_key) {
        return false;
      }
    }
    return true;
  }

  function no_own_unit_at_square(lat, lng) {
    for (var i in unit_markers) {
      if (unit_markers[i].getPosition().lat() == lat && unit_markers[i].getPosition().lng() == lng) {
        if (unit_markers[i].unit.unit_owner_key == account['id']) {
          return false;
        }
      }
    }
    return true;
  }

  function no_friendly_unit_at_square(lat, lng) {
    for (var i in unit_markers) {
      if (unit_markers[i].getPosition().lat() == lat && unit_markers[i].getPosition().lng() == lng) {
        return find_treaty_by_account_key(unit_markers[i].unit.unit_owner_key) == war_key;
      }
    }
    return true;
  }

  function is_friendly_square(lat, lng) {
    let tile_account_key = tiles_by_coord[lat + ',' + lng].account_key;
    if (!tile_account_key) {
      return false;
    }
    if (tile_account_key == account['id']) {
      return false;
    }
    if (find_treaty_by_account_key(tile_account_key) == war_key) {
      return false;
    }
    return true;
  }
</script>