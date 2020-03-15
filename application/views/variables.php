<script>
    const base_url = '<?= base_url(); ?>';
    const resources = JSON.parse('<?= json_encode($this->resources); ?>');
    const terrains = JSON.parse('<?= json_encode($this->terrains); ?>');
    const settlements = JSON.parse('<?= json_encode($this->settlements); ?>');
    const industries = JSON.parse('<?= json_encode($this->industries); ?>');
    const unit_types = JSON.parse('<?= json_encode($this->unit_types); ?>');
    const unit_labels = JSON.parse('<?= json_encode($this->unit_labels); ?>');
    const world_key = <?= $world['id']; ?>;
    const tile_size = <?= $world['tile_size']; ?>;
    const town_key = <?= TOWN_KEY; ?>;
    const city_key = <?= CITY_KEY; ?>;
    const metro_key = <?= METRO_KEY; ?>;
    const township_array = [town_key, city_key, metro_key];
    const navy_character = '<?= NAVY_CHARACTER; ?>';
    const navy_color = '<?= NAVY_COLOR; ?>';
    const fertile_key = <?= FERTILE_KEY; ?>;
    const barren_key = <?= BARREN_KEY; ?>;
    const mountain_key = <?= MOUNTAIN_KEY; ?>;
    const tundra_key = <?= TUNDRA_KEY; ?>;
    const coastal_key = <?= COASTAL_KEY; ?>;
    const ocean_key = <?= OCEAN_KEY; ?>;
    const barren_defensive_bonus = <?= BARREN_DEFENSIVE_BONUS; ?>;
    const tundra_defensive_bonus = <?= TUNDRA_DEFENSIVE_BONUS; ?>;
    const mountain_defensive_bonus = <?= MOUNTAIN_DEFENSIVE_BONUS; ?>;
    const town_defensive_bonus = <?= TOWN_DEFENSIVE_BONUS; ?>;
    const city_defensive_bonus = <?= CITY_DEFENSIVE_BONUS; ?>;
    const metro_defensive_bonus = <?= METRO_DEFENSIVE_BONUS; ?>;
    const infantry_key = <?= INFANTRY_KEY; ?>;
    const tanks_key = <?= TANKS_KEY; ?>;
    const commandos_key = <?= COMMANDOS_KEY; ?>;
    let map = null;
    let account = <?= json_encode($account); ?>;
    let tiles = [];
    let tiles_by_coord = [];
    let highlighted_tiles = [];
    let current_tile = false;
    let resource_markers = [];
    let unit_markers = [];
    let settlement_markers = [];
    let map_update_interval_ms = <?= MAP_UPDATE_INTERVAL_MS; ?>;
    let account_update_interval_ms = <?= ACCOUNT_UPDATE_INTERVAL_MS; ?>;
    let unit_valid_square_color = '<?= UNIT_VALID_SQUARE_COLOR; ?>';
    let selected_square_color = '<?= SELECTED_SQUARE_COLOR; ?>';
    let stroke_weight = <?= STROKE_WEIGHT; ?>;
    let stroke_color = '<?= STROKE_COLOR; ?>';
    let attack_key_pressed = false;
    let active_requests = [];
    let start_drag_lat = null;
    let start_drag_lng = null;

    // Toggles
    let border_toggle = <?= DEFAULT_BORDER_TOGGLE ? 'true' : 'false'; ?>;
    let resource_toggle = <?= DEFAULT_RESOURCE_TOGGLE ? 'true' : 'false'; ?>;
    let settlement_toggle = <?= DEFAULT_SETTLEMENT_TOGGLE ? 'true' : 'false'; ?>;
    let unit_toggle = <?= DEFAULT_UNIT_TOGGLE ? 'true' : 'false'; ?>;
    let grid_toggle = <?= DEFAULT_GRID_TOGGLE ? 'true' : 'false'; ?>;

    // Keys
    let keys = new Array();
    keys['enter'] = 13;
    keys['a'] = 65;

    const default_map_style = [{
      featureType: "poi.business",
      elementType: "labels",
      stylers: [{
        visibility: "off"
      }]
    }];

    // https://snazzymaps.com/style/21658/pirate-map
    const map_pirate =
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