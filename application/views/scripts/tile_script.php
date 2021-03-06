<script>

    handle_edit_tile_meta();
    handle_set_settlement();
    handle_set_industry();
    handle_exit_tile_block()
    handle_enlist_unit();
    handle_tile_open_diplomacy();

    function handle_tile_open_diplomacy() {
        $('#tile_open_diplomacy').click(function(event) {
            new_diplomacy(current_tile.account_key);
        });
    }

    function handle_set_settlement() {
        $('#set_settlement_button').click(function(event) {
            let data = {
                settlement_key: preview_settlement_key,
                tile_id: current_tile.id,
                tile_name: $('#town_tile_name_input').val() ? $('#town_tile_name_input').val() : null,
            };
            ajax_post('game/update_settlement', data, function(response) {
                update_tutorial_after_set_settlement();
                if (preview_settlement_key == town_key) {
                    update_tutorial_after_set_township();
                }
                get_tile(current_tile.lat, current_tile.lng, current_tile.world_key, function(response) {
                    current_tile = response;
                    get_map_update();
                    render_tile_window();
                    tile_name();
                });
            });
        });
    }
    function handle_set_industry() {
        $('#set_industry_button').click(function(event) {
            let data = {
                industry_key: preview_industry_key,
                tile_id: current_tile.id,
            };
            ajax_post('game/update_industry', data, function(response) {
                update_tutorial_after_set_industry();
                get_tile(current_tile.lat, current_tile.lng, current_tile.world_key, function(response) {
                    current_tile = response;
                    get_map_update();
                    render_tile_window();
                });
            });
        });
    }

    function handle_edit_tile_meta() {
        $('#edit_tile_name').click(function(event) {
            $('#edit_tile_name, #tile_name').hide();
            $('#tile_name_input, #submit_tile_name').show();
            $('#tile_name_input').focus();
        });
        $('#edit_tile_desc').click(function(event) {
            $('#edit_tile_desc, #tile_desc').hide();
            $('#tile_desc_input').val('');
            $('#tile_desc_input, #submit_tile_desc').show();
            $('#tile_desc_input').focus();
        });
        $('#submit_tile_name').click(function(event) {
            submit_tile_name();
        });
        $('#submit_tile_desc').click(function(event) {
            submit_tile_desc();
        });
        $('#tile_name_input').keypress(function(event) {
            if (event.which == keys['enter']) {
                submit_tile_name();
                event.preventDefault();
            }
        });
        $('#town_tile_name_input').keypress(function(event) {
            if (event.which == keys['enter']) {
                $('#set_settlement_button').trigger('click');
                event.preventDefault();
            }
        });
        $('#tile_desc_input').keypress(function(event) {
            if (event.which == keys['enter'] && !event.shiftKey) {
                submit_tile_desc();
                event.preventDefault();
            }
        });
    }
    function submit_tile_name() {
        let data = {
            tile_id: current_tile.id,
            tile_name: $('#tile_name_input').val(),
        };
        ajax_post('game/update_tile_name', data, function(response) {
            current_tile.tile_name = nl2br($('#tile_name_input').val());
            $('#tile_name').html($('#tile_name_input').val());
            $('#edit_tile_name, #tile_name').show();
            $('#tile_name_input, #submit_tile_name').hide();
        });
    }
    function submit_tile_desc() {
        let data = {
            tile_id: current_tile.id,
            tile_desc: $('#tile_desc_input').val(),
        }
        ajax_post('game/update_tile_desc', data, function(response) {
            current_tile.tile_desc = nl2br($('#tile_desc_input').val());
            // $('#tile_desc').html(nl2br($('#tile_desc_input').val()));
            render_tile_window();
            $('#edit_tile_desc, #tile_desc').show();
            $('#tile_desc_input, #submit_tile_desc').hide();
        });        
    }

    function place_first_unit() {
        let terrain_key = current_tile.terrain_key;
        let unit_owner_key = account.id;
        let unit_owner_color = account.color;
        let lat = current_tile.lat;
        let lng = current_tile.lng;
        let unit_id = infantry_key;
        unit_markers[current_tile.id] = set_unit_icon(unit_id, current_tile.id, terrain_key, unit_owner_key, unit_owner_color, lat, lng);
    }

    function render_tile(lat, lng) {
        tile = get_tile(lat, lng, world_key, function(response) {
            current_tile = response;
            preview_settlement_key = current_tile.settlement_key;
            preview_industry_key = current_tile.industry_key;
            render_settlement_extended(current_tile.settlement_key);
            render_industry_extended(current_tile.industry_key);
            highlight_single_square(current_tile.id);
            render_tile_window();
            open_selection_tab();
        });
    }

    function open_selection_tab() {
        if (current_tile.account_key == account.id) {
            if (parseInt(current_tile.is_capitol) || parseInt(current_tile.is_base)) {
                $('#enlist_tab_button').tab('show');
            }
            else if ([town_key, city_key, metro_key].includes(parseInt(current_tile.settlement_key))) {
                $('#industry_tab_button').tab('show');
            }
            else {
                $('#settle_tab_button').tab('show');
            }
        }
    }

    function handle_exit_tile_block() {
        $('#exit_tile_block').click(function(){
            unhighlight_all_squares();
        })
    }

    function handle_enlist_unit() {
        $('.enlist_unit_button').click(function(){
            let unit_id = $(this).data('id');
            let data = {
                unit_id: unit_id,
                tile_id: current_tile.id,
            };
            ajax_post('game/request_unit_spawn', data, function(response) {
                let terrain_key = current_tile.terrain_key;
                let unit_owner_key = account.id;
                let unit_owner_color = account.color;
                let lat = current_tile.lat;
                let lng = current_tile.lng;
                unit_markers[current_tile.id] = set_unit_icon(unit_id, current_tile.id, terrain_key, unit_owner_key, unit_owner_color, lat, lng);
            });
        });
    }

    function get_tile(lat, lng, world_key, callback) {
        let data = {
            lat: lat,
            lng: lng,
            world_key: world_key
        };
        ajax_post('game/get_tile', data, function(response) {
            callback(response);
        });
    }

</script>