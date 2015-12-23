<!DOCTYPE html>
<html>
  <head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<meta charset="utf-8">
	<title>Land</title>
	<style>
	  html, body {
		height: 100%;
		margin: 0;
		padding: 0;
	  }
	  #map {
		height: 100%;
	  }
	</style>
  </head>
  <body>
	<div id="map"></div>
	<script src="resources/jquery-1.11.1.min.js"></script>
	<script>

function initMap() 
{
	// Get ajax grid
	function get_grid_ajax(grid_coord, callback)
	{
		$.ajax(
		{
			url: "ajax.php",
			type: "GET",
			data: { grid_coord: grid_coord },
			cache: false,
			success: function(html)
			{
				var ajax_array = html.split('|');
				callback(ajax_array);
				return true;
			}
		});
	}

	// Map options
	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: 3,
		minZoom: 3,
		maxZoom: 3,
		draggable: false,
		scrollwheel: false,
		panControl: false,
		center: {lat: 20, lng: 0},
		mapTypeId: google.maps.MapTypeId.TERRAIN 
		// mapTypeId: google.maps.MapTypeId.HYBRID 
	});

	// Static Variables
	var box_size = 3;
	// Must be evenly divisible by box_size
	var x_limit = 180;
	var y_limit = 81;

	// Non Static Variables
	var box_number = 0;

	// Loop through coords
	for (x = -x_limit; x < x_limit; x = x + box_size)
	{
		for (y = -y_limit; y < y_limit; y = y + box_size)
		{
			var shape = [
				{lat: y, lng: x},
				{lat: y + box_size, lng: x},
				{lat: y + box_size, lng: x + box_size},
				{lat: y, lng: x + box_size}
			];
			// Set style of boxes
			box = new google.maps.Polygon({
			  box_number: box_number,
			  map: map,
			  paths: shape,
			  strokeColor: '#222222',
			  strokeOpacity: 0.2,
			  strokeWeight: 2,
			  fillColor: '#FF00FF',
			  fillOpacity: 0.1,
			  geodesic: true
			});

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

			// Set InfoWindow
			function set_window(event) {
				lat = event.latLng.lat();
				lng = event.latLng.lng();
				lat = round_down(lat);
				lng = round_down(lng);
				var grid_coord = lat + '|' + lng;
				ajax_array = get_grid_ajax(grid_coord, function(ajax_array){
					console.log(grid_coord);
					var content = ajax_array[0];
					var stroke = ajax_array[1];
					var fill = ajax_array[2];
					// View
					var contentString = '<b>' + content + '</b><br>';
					// 'Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() + '<br>';
					// Set InfoWindow Interaction
					infoWindow.setContent(contentString);
					infoWindow.setPosition(event.latLng);
					infoWindow.open(map);
				});
			}

			// Set box on map with listeners and info window
			box.setMap(map);
			box.addListener('click', set_window);
			infoWindow = new google.maps.InfoWindow;

			box_number++;
		}
	}

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

	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD_lT8RkN6KffGEfJ3xBcBgn2VZga-a05I&callback=initMap&signed_in=true" async defer>
	</script>
  </body>
</html>