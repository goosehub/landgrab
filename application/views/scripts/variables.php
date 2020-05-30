<script>
    const base_url = '<?= base_url(); ?>';
    const resources = JSON.parse('<?= json_encode($this->resources); ?>');
    const terrains = JSON.parse('<?= json_encode($this->terrains); ?>');
    const settlements = sort_by_id(JSON.parse('<?= json_encode($this->settlements); ?>'));
    const industries = sort_by_id(JSON.parse('<?= json_encode($this->industries); ?>'));
    const unit_types = JSON.parse('<?= json_encode($this->unit_types); ?>');
    const unit_labels = JSON.parse('<?= json_encode($this->unit_labels); ?>');
    const treaties = JSON.parse('<?= json_encode($this->treaties); ?>');
    const world_key = <?= $world['id']; ?>;
    const tile_size = <?= $world['tile_size']; ?>;
    const map_icon_size = <?= MAP_ICON_SIZE; ?>;
    const focus_zoom = <?= FOCUS_ZOOM; ?>;
    const default_zoom = <?= DEFAULT_ZOOM; ?>;
    const max_zoom = <?= MAX_ZOOM; ?>;
    const uninhabited_key = <?= UNINHABITED_KEY ?>;
    const town_key = <?= TOWN_KEY; ?>;
    const city_key = <?= CITY_KEY; ?>;
    const metro_key = <?= METRO_KEY; ?>;
    const township_array = [town_key, city_key, metro_key];
    const fertile_key = <?= FERTILE_KEY; ?>;
    const barren_key = <?= BARREN_KEY; ?>;
    const mountain_key = <?= MOUNTAIN_KEY; ?>;
    const tundra_key = <?= TUNDRA_KEY; ?>;
    const coastal_key = <?= COASTAL_KEY; ?>;
    const ocean_key = <?= OCEAN_KEY; ?>;
    const war_key = <?= WAR_KEY; ?>;
    const peace_key = <?= PEACE_KEY; ?>;
    const passage_key = <?= PASSAGE_KEY; ?>;
    const fertile_text_color = '<?= FERTILE_TEXT_COLOR; ?>';
    const barren_text_color = '<?= BARREN_TEXT_COLOR; ?>';
    const mountain_text_color = '<?= MOUNTAIN_TEXT_COLOR; ?>';
    const tundra_text_color = '<?= TUNDRA_TEXT_COLOR; ?>';
    const coastal_text_color = '<?= COASTAL_TEXT_COLOR; ?>';
    const fertile_color = '<?= FERTILE_COLOR ?>';
    const barren_color = '<?= BARREN_COLOR ?>';
    const mountain_color = '<?= MOUNTAIN_COLOR ?>';
    const tundra_color = '<?= TUNDRA_COLOR ?>';
    const coastal_color = '<?= COASTAL_COLOR ?>';
    const ocean_color = '<?= OCEAN_COLOR ?>';
    const barren_offensive_bonus = <?= BARREN_OFFENSIVE_BONUS; ?>;
    const tundra_defensive_bonus = <?= TUNDRA_DEFENSIVE_BONUS; ?>;
    const mountain_defensive_bonus = <?= MOUNTAIN_DEFENSIVE_BONUS; ?>;
    const town_defensive_bonus = <?= TOWN_DEFENSIVE_BONUS; ?>;
    const city_defensive_bonus = <?= CITY_DEFENSIVE_BONUS; ?>;
    const metro_defensive_bonus = <?= METRO_DEFENSIVE_BONUS; ?>;
    const infantry_key = <?= INFANTRY_KEY; ?>;
    const tanks_key = <?= TANKS_KEY; ?>;
    const airforce_key = <?= AIRFORCE_KEY; ?>;
    const navy_key = <?= NAVY_KEY; ?>;
    const cash_key = <?= CASH_KEY ?>;
    const food_key = <?= FOOD_KEY ?>;
    const cash_crops_key = <?= CASH_CROPS_KEY ?>;
    const port_key = <?= PORT_KEY ?>;
    const machinery_key = <?= MACHINERY_KEY ?>;
    const automotive_key = <?= AUTOMOTIVE_KEY ?>;
    const aerospace_key = <?= AEROSPACE_KEY ?>;
    const entertainment_key = <?= ENTERTAINMENT_KEY ?>;
    const financial_key = <?= FINANCIAL_KEY ?>;
    const food_category_id = <?= FOOD_CATEGORY_ID ?>;
    const cash_crops_category_id = <?= CASH_CROPS_CATEGORY_ID ?>;
    const democracy_key = <?= DEMOCRACY_KEY ?>;
    const oligarchy_key = <?= OLIGARCHY_KEY ?>;
    const autocracy_key = <?= AUTOCRACY_KEY ?>;
    const free_market_key = <?= FREE_MARKET_KEY ?>;
    const socialism_key = <?= SOCIALISM_KEY ?>;
    const democracy_socialism_max_support = <?= DEMOCRACY_SOCIALISM_MAX_SUPPORT ?>;
    const oligarchy_socialism_max_support = <?= OLIGARCHY_SOCIALISM_MAX_SUPPORT ?>;
    const autocracy_socialism_max_support = <?= AUTOCRACY_SOCIALISM_MAX_SUPPORT ?>;
    const democracy_support_regen = <?= DEMOCRACY_SUPPORT_REGEN ?>;
    const oligarchy_support_regen = <?= OLIGARCHY_SUPPORT_REGEN ?>;
    const autocracy_support_regen = <?= AUTOCRACY_SUPPORT_REGEN ?>;
    const trade_expire_hours = <?= TRADE_EXPIRE_HOURS ?>;
    const town_food_cost = <?= TOWN_FOOD_COST ?>;
    const city_food_cost = <?= CITY_FOOD_COST ?>;
    const metro_food_cost = <?= METRO_FOOD_COST ?>;
    const town_cash_crops_cost = <?= TOWN_CASH_CROPS_COST ?>;
    const city_cash_crops_cost = <?= CITY_CASH_CROPS_COST ?>;
    const metro_cash_crops_cost = <?= METRO_CASH_CROPS_COST ?>;
    const town_energy_cost = <?= TOWN_ENERGY_COST ?>;
    const city_energy_cost = <?= CITY_ENERGY_COST ?>;
    const metro_energy_cost = <?= METRO_ENERGY_COST ?>;
    const town_merchandise_cost = <?= TOWN_MERCHANDISE_COST ?>;
    const city_merchandise_cost = <?= CITY_MERCHANDISE_COST ?>;
    const metro_merchandise_cost = <?= METRO_MERCHANDISE_COST ?>;
    const town_steel_cost = <?= TOWN_STEEL_COST ?>;
    const city_steel_cost = <?= CITY_STEEL_COST ?>;
    const metro_steel_cost = <?= METRO_STEEL_COST ?>;
    const town_pharmaceuticals_cost = <?= TOWN_PHARMACEUTICALS_COST ?>;
    const city_pharmaceuticals_cost = <?= CITY_PHARMACEUTICALS_COST ?>;
    const metro_pharmaceuticals_cost = <?= METRO_PHARMACEUTICALS_COST ?>;
    const food_key_array = [
      <?= GRAIN_KEY ?>,
      <?= VEGETABLES_KEY ?>,
      <?= FRUIT_KEY ?>,
      <?= LIVESTOCK_KEY ?>,
      <?= FISH_KEY ?>,
    ];
    const cash_crops_key_array = [
      <?= COFFEE_KEY ?>,
      <?= TEA_KEY ?>,
      <?= CANNABIS_KEY ?>,
      <?= WINE_KEY ?>,
      <?= TOBACCO_KEY ?>,
    ];
    const food_slug_array = [
      'grain',
      'vegetables',
      'fruit',
      'livestock',
      'fish',
    ];
    const cash_crops_slug_array = [
      'coffee',
      'tea',
      'cannabis',
      'wine',
      'tobacco',
    ];
    const force_tab = '&nbsp;'.repeat(4);
    let map = null;
    let account = <?= json_encode($account); ?>;
    let tiles = [];
    let tiles_by_coord = [];
    let highlighted_tiles = [];
    let current_tile = false;
    let trade_partner = false;
    let view_trade = false;
    let preview_settlement_key = null;
    let preview_industry_key = null;
    let resource_markers = [];
    let unit_markers = [];
    let settlement_markers = [];
    let township_markers = [];
    let industry_markers = [];
    let map_update_interval_ms = <?= MAP_UPDATE_INTERVAL_MS; ?>;
    let map_units_update_interval_ms = <?= MAP_UNITS_UPDATE_INTERVAL_MS; ?>;
    let account_update_interval_ms = <?= ACCOUNT_UPDATE_INTERVAL_MS; ?>;
    let combat_animate_ms = <?= COMBAT_ANIMATE_MS; ?>;
    let unit_linger_ms = <?= UNIT_LINGER_MS; ?>;
    let unit_valid_square_color = '<?= UNIT_VALID_SQUARE_COLOR; ?>';
    let selected_square_color = '<?= SELECTED_SQUARE_COLOR; ?>';
    let stroke_weight = <?= STROKE_WEIGHT; ?>;
    let stroke_color = '<?= STROKE_COLOR; ?>';
    let attack_key_pressed = false;
    let active_requests = [];
    let start_drag_lat = null;
    let start_drag_lng = null;
    let power_structure = null;
    let tax_rate = null;
    let ideology = null;
    let current_leaderboard_supply = <?= DEFAULT_LEADERBOARD_SUPPLY_KEY; ?>;
    let game_won_acknowledge = false;

    // Toggles
    let border_toggle = <?= DEFAULT_BORDER_TOGGLE ? 'true' : 'false'; ?>;
    let resource_toggle = <?= DEFAULT_RESOURCE_TOGGLE ? 'true' : 'false'; ?>;
    let settlement_toggle = <?= DEFAULT_SETTLEMENT_TOGGLE ? 'true' : 'false'; ?>;
    let township_and_industry_toggle = <?= DEFAULT_TOWNSHIP_AND_INDUSTRY_TOGGLE; ?>;
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