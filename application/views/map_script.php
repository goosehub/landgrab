<script>

	function initMap() {
	  // 
	  // Map options
	  // 

	  var map = new google.maps.Map(document.getElementById('map'), {
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

	  // Example Icon
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

	  // Remove loading overlay based on tiles loaded status
	  google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
	    $('#overlay').fadeOut();
	  });
	}

</script>