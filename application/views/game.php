<!DOCTYPE html>
<html>
  <head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<meta charset="utf-8">
	<title>Land</title>
	<style>
	  /* Global */
	  html, body {
		height: 100%;
		margin: 0;
		padding: 0;
	  }
	  /* Map */
	  #map {
		height: 100%;
	  }

	  /* Overlay */
	  #overlay {
	      position: absolute;
	      left: 0;
	      top: 0;
	      bottom: 0;
	      right: 0;
	      background: #000;
	      opacity: 0.8;
	      filter: alpha(opacity=80);
	  }
	  #loading {
	      width: 50px;
	  }
	</style>
  </head>
  <body>

  	<!-- Map Element -->
	<div id="map"></div>

	<div id="account"><?php if (isset($_SESSION['username'])) { ?>
    <?php } else { ?>
    	Login
    <?php } ?>
    </div>

	<!-- jQuery -->
	<script src="resources/jquery-1.11.1.min.js"></script>

	<!-- Master Script -->
	<script>

// Loading Overlay
loading = function() {
    // add the overlay with loading image to the page
    var over = '<div id="overlay"></div>';
    $(over).appendTo('body');
};
loading();

function initMap() 
{
	// 
	// Functions
	// 

	// Get single grid ajax
	function get_single_grid(grid_coord, callback)
	{
		$.ajax(
		{
			url: "ajax",
			type: "GET",
			data: { request: grid_coord },
			cache: false,
			success: function(html)
			{
				var grids = html.split('|');
				callback(grids);
				return true;
			}
		});
	}

	// For rounding grid coords
	function round_down(n) {
		if(n > 0)
		{
	        return Math.floor(n/box_size) * box_size;
		}
	    else if( n < 0)
	    {
	        return Math.ceil(n/box_size) * box_size;
	    }
	    else
	    {
	        return box_size;
	    }
	}

	// 
	// Map options
	// 

	var map = new google.maps.Map(document.getElementById('map'), {
		// Starting center
		center: {lat: 20, lng: 0},

		// Default zoom and limits
		zoom: 3,
		minZoom: 3,
		maxZoom: 10,

		// Prevent panning and zooming
		// draggable: false,
		// scrollwheel: false,
		// panControl: false,

		// Map type
		mapTypeId: google.maps.MapTypeId.TERRAIN 
		// mapTypeId: google.maps.MapTypeId.HYBRID 
	});

	// Size of grid box squares
	// Box size 2 creates 57600 grid squares
	var box_size = 2;
	// Area covered defined with these limits
	// Must be evenly divisible by box_size
	var x_limit = 180;
	var y_limit = 80;

	// 
	// Grid loop
	// 

	// Loop lng
	for (x = -x_limit; x < x_limit; x = x + box_size)
	{
		// Loop lat for each lng
		for (y = -y_limit; y < y_limit; y = y + box_size)
		{
			// Define shape of grid polygon
			var shape = [
				{lat: y, lng: x},
				{lat: y + box_size, lng: x},
				{lat: y + box_size, lng: x + box_size},
				{lat: y, lng: x + box_size}
			];
			// Style grid
			box = new google.maps.Polygon({
			  map: map,
			  paths: shape,
			  strokeColor: '#222222',
			  strokeOpacity: 0.2,
			  strokeWeight: 2,
			  fillColor: '#FF00FF',
			  fillOpacity: 0.1,
			  geodesic: true
			});

			// Set grid window
			function set_window(event) {
				lat = event.latLng.lat();
				lng = event.latLng.lng();
				lat = round_down(lat);
				lng = round_down(lng);
				var grid_coord = lat + '|' + lng;
				grid = get_single_grid(grid_coord, function(grid){
					console.log(grid_coord);
					var owner = grid[0];
					var content = grid[1];
					// View
					var contentString = '<strong>' + owner + '</strong><br>' + content + '<br>';
					// 'Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() + '<br>';
					// Set InfoWindow Interaction
					infoWindow.setContent(contentString);
					infoWindow.setPosition(event.latLng);
					infoWindow.open(map);
				});
			}

			// Attach grid window
			box.setMap(map);
			box.addListener('click', set_window);
			infoWindow = new google.maps.InfoWindow;
		}
	}

	// 
	// Map Styling
	// 

	// Optional Styling of map
	var styles = [
	  {
		featureType: "all",
		stylers: [
		  { saturation: -80 }
		]
	  },{
		featureType: "road.arterial",
		elementType: "geometry",
		stylers: [
		  { hue: "#00ffee" },
		  { saturation: 50 }
		]
	  },{
		featureType: "poi.business",
		elementType: "labels",
		stylers: [
		  { visibility: "off" }
		]
	  }
	];

	var styledMap = new google.maps.StyledMapType(styles,
	  {name: "Styled Map"});

	// Uncomment below to turn on map styles
	// map.mapTypes.set('map_style', styledMap);
	// map.setMapTypeId('map_style');

}

// Remove loading overlay
setTimeout(function(){
	$('#overlay').fadeOut();
}, 3000);

	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD_lT8RkN6KffGEfJ3xBcBgn2VZga-a05I&callback=initMap&signed_in=true" async defer>
	</script>
  </body>
</html>