<script>
    const base_url = '<?= base_url(); ?>';
    const resources = JSON.parse('<?php echo json_encode($this->resources); ?>');
    const terrains = JSON.parse('<?php echo json_encode($this->terrains); ?>');
    const settlements = JSON.parse('<?php echo json_encode($this->settlements); ?>');
    const industries = JSON.parse('<?php echo json_encode($this->industries); ?>');
    const unit_types = JSON.parse('<?php echo json_encode($this->unit_types); ?>');
    const unit_labels = JSON.parse('<?php echo json_encode($this->unit_labels); ?>');
    const world_key = <?php echo $world['id']; ?>;
    const tile_size = <?php echo $world['tile_size']; ?>;
    let map = null;
    let account = <?php echo json_encode($account); ?>;
    let tiles = [];
    let tiles_by_coord = [];
    let highlighted_tiles = [];
    let current_tile = false;
    let resource_markers = [];
    let unit_markers = [];
    let settlement_markers = [];
    let incorporated_array = [<?php echo TOWN_KEY; ?>, <?php echo CITY_KEY; ?>, <?php echo METRO_KEY; ?>];
    let map_update_interval_ms = <?php echo MAP_UPDATE_INTERVAL_MS; ?>;
    let account_update_interval_ms = <?php echo ACCOUNT_UPDATE_INTERVAL_MS; ?>;
    let unit_valid_square_color = '<?php echo UNIT_VALID_SQUARE_COLOR; ?>';
    let selected_square_color = '<?php echo SELECTED_SQUARE_COLOR; ?>';
    let border_toggle = <?php echo DEFAULT_BORDER_TOGGLE ? 'true' : 'false'; ?>;
    let resource_toggle = <?php echo DEFAULT_RESOURCE_TOGGLE ? 'true' : 'false'; ?>;
    let settlement_toggle = <?php echo DEFAULT_SETTLEMENT_TOGGLE ? 'true' : 'false'; ?>;
    let unit_toggle = <?php echo DEFAULT_UNIT_TOGGLE ? 'true' : 'false'; ?>;
    let grid_toggle = <?php echo DEFAULT_GRID_TOGGLE ? 'true' : 'false'; ?>;
    let stroke_color = '<?= STROKE_COLOR; ?>';
    let attack_key_pressed = false;
    let active_requests = [];
    let keys = new Array();
    const navy_character = '<?= NAVY_CHARACTER; ?>';
    const navy_color = '<?= NAVY_COLOR; ?>';
    const fertile_key = <?= FERTILE_KEY; ?>;
    const barren_key = <?= BARREN_KEY; ?>;
    const mountain_key = <?= MOUNTAIN_KEY; ?>;
    const tundra_key = <?= TUNDRA_KEY; ?>;
    const coastal_key = <?= COASTAL_KEY; ?>;
    const ocean_key = <?= OCEAN_KEY; ?>;
    keys['enter'] = 13;
    keys['a'] = 65;
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