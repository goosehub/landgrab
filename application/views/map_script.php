<script>

  let attack_key_pressed = false;
  let keys = new Array();
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

  function set_map() {
    map = new google.maps.Map(document.getElementById('map'), {
      // Zoom on tile if set as parameter
      <?php if ( isset($_GET['tile']) ) { $tile_coords_split = explode(',', $_GET['tile']); ?>

      // Logic to center isn't understood, but results in correct behavior in all 4 corners
      center: {
        lat: <?php echo $tile_coords_split[0] + ($world['tile_size'] / 2); ?>,
        lng: <?php echo $tile_coords_split[1] - ($world['tile_size'] / 2); ?>
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
  }

  function remove_overlay() {
    // Remove loading overlay based on tiles loaded status
    google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
      $('#overlay').fadeOut();
    });
  }

  function example_marker() {
    var myLatLng = {lat: -1, lng: 1};
    var marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      title: 'Hello World!',
      // draggable:true,
      icon: {
        url: 'https://images.vexels.com/media/users/3/128926/isolated/preview/c60c97eba10a56280114b19063d04655-plane-airport-round-icon-by-vexels.png',
        scaledSize: new google.maps.Size(20, 20), // scaled size
        origin: new google.maps.Point(0,0), // origin
        anchor: new google.maps.Point(10,10) // anchor
      }
    });
    marker.setMap(map);
  }

  function initMap() {
    set_map();
    remove_overlay();
    // example_marker();
    generate_tiles();
  }

  function generate_tiles() {
    <?php 
    // This foreach loop runs 15,000 times, so performance and bandwidth is key
    // Because of this, some unconventional code may be used
    foreach ($tiles as $tile) {
      $stroke_weight = 0.2; 
      $stroke_color = '#222222';
      $fill_color = "#FFFFFF";
      $fill_opacity = '0';
      if ($tile['terrain_key'] === '1') {
        $fill_color = '#00FF00'; // Fertile
        $fill_opacity = '0.4';
      }
      if ($tile['terrain_key'] === '2') {
        $fill_color = '#666666'; // Barren
        $fill_opacity = '0.4';
      }
      if ($tile['terrain_key'] === '3') {
        $fill_color = '#000000'; // Mountain
        $fill_opacity = '0.4';
      }
      if ($tile['terrain_key'] === '4') {
        $fill_color = '#BBBBBB'; // Tundra
        $fill_opacity = '0.4';
      }
      if ($tile['terrain_key'] === '5') {
        $fill_color = '#ECF492'; // Coastal
        $fill_opacity = '0.4';
      }
      if ($tile['terrain_key'] === '6') {
        $fill_color = '#0000FF'; // Ocean
        $fill_opacity = '0.4';
      }
      ?>z(<?php echo
        $tile['id'] . ',' .
        $tile['lat'] . ',' .
        $tile['lng'] . ',' .
        $stroke_weight . ',' .
        '"' . $stroke_color . '"' . ',' .
        '"' . $fill_color . '"' . ',' .
        $fill_opacity; ?>);<?php // Open and close immediately to avoid whitespace eating bandwidth
    } ?>
  }

  // Declare square called by performance sensitive loop
  function z(tile_key, tile_lat, tile_lng, stroke_weight, stroke_color, fill_color, fill_opacity) {
    shape = [{
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
    tiles[tile_key] = box;
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

  function set_window(event) {
    // Not sure why subtracting tile_size on lat makes this work, but results in correct behavior
    var lat = round_down(event.latLng.lat()) - tile_size;
    var lng = round_down(event.latLng.lng());

    if (attack_key_pressed) {
      blind_land_attack(lng, lat, world_key, 'attack', function(response) {});
      return true;
    }

    $('.center_block').fadeOut(100);

    tile = get_single_tile(lat, lng, world_key, function(tile) {
      d = JSON.parse(tile);

      if (d['error']) {
        alert(d['error']);
        return false;
      }

      populate_tile_window(d);

      // Unbind the last click handler from get_single_land 
      $('#submit').off('click');

      $('#submit').click(function() {
        var form_type = $(this).val();
        // land_form_ajax(form_type);
      });
    });
  }

  function populate_tile_window(d) {
    $('#tile_block').show();
    $('#coord_link').prop('href', '<?=base_url()?>world/' + d['world_key'] + '?lng=' + d['lng'] + '&lat=' + d['lat']);
    $('#coord_link').html(d['lng'] + ',' + d['lat']);
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