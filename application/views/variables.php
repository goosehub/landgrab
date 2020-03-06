<script>
    let base_url = '<?= base_url(); ?>';
	let map = null;
	let world_key = <?php echo $world['id']; ?>;
	let tile_size = <?php echo $world['tile_size']; ?>;
	let account = <?php echo json_encode($account); ?>;
    let tiles = [];
	let current_tile = false;
	let resource_markers = [];
	let unit_markers = [];
    let settlement_markers = [];
	let current_map_type = 'terrain';
	let map_update_interval_ms = <?php echo MAP_UPDATE_INTERVAL_MS; ?>;
    let account_update_interval_ms = <?php echo ACCOUNT_UPDATE_INTERVAL_MS; ?>;
    let use_borders = <?php echo USE_BORDERS ? 'true' : 'false'; ?>;
	let unit_toggle = <?php echo DEFAULT_UNIT_TOGGLE ? 'true' : 'false'; ?>;
    let stroke_color = '<?= STROKE_COLOR; ?>';
	let attack_key_pressed = false;
	let keys = new Array();
    let resources = JSON.parse('<?php echo json_encode($this->resources); ?>');
	let terrains = JSON.parse('<?php echo json_encode($this->terrains); ?>');
    let settlements = JSON.parse('<?php echo json_encode($this->settlements); ?>');
    let industries = JSON.parse('<?php echo json_encode($this->industries); ?>');
    let unit_types = JSON.parse('<?php echo json_encode($this->unit_types); ?>');
    let unit_labels = JSON.parse('<?php echo json_encode($this->unit_labels); ?>');
    let start_drag_lat = null;
    let start_drag_lng = null;
	let styledMapType = {};
    let default_map_style = [{
      featureType: "poi.business",
      elementType: "labels",
      stylers: [{
        visibility: "off"
      }]
    }];

    // https://snazzymaps.com/style/21658/pirate-map
    let map_pirate =
    [
        {
            "featureType": "all",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#d4b78f"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "all",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#0d0000"
                },
                {
                    "visibility": "on"
                },
                {
                    "weight": 1
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#98290e"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "administrative.province",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "administrative.locality",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#98290e"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "administrative.locality",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "administrative.neighborhood",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#d4b78f"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#c4b17e"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "labels.icon",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#0d0000"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#d9be94"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "road.highway.controlled_access",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#0d0000"
                },
                {
                    "visibility": "off"
                },
                {
                    "weight": 2
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "road.local",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#a8ac91"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#98290e"
                },
                {
                    "visibility": "on"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        }
    ];
</script>