<script>

    handle_edit_tile_meta();
    handle_first_claim();
    handle_set_settlement();
    handle_set_industry();
    handle_exit_tile_block()
    handle_enlist_unit();

    function handle_set_settlement() {
        $('.set_settlement_button').click(function(event) {
            let settlement_key = $(this).data('id');
            let data = {
                settlement_key: settlement_key,
                tile_id: current_tile.id,
            };
            ajax_post('game/update_settlement', data, function(response) {
                current_tile.settlement_key = settlement_key;
                get_map_update();
                render_tile_window();
            });
        });
    }
    function handle_set_industry() {
        $('.set_industry_button').click(function(event) {
            let industry_key = $(this).data('id');
            let data = {
                industry_key: industry_key,
                tile_id: current_tile.id,
            };
            ajax_post('game/update_industry', data, function(response) {
                current_tile.industry_key = industry_key;
                get_map_update();
                render_tile_window();
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

    function handle_first_claim() {
        $('#do_first_claim').click(function(){
            let data = {
                world_key: current_tile.world_key,
                lat: current_tile.lat,
                lng: current_tile.lng,
            };
            ajax_post('game/do_first_claim', data, function(response) {
                get_map_update();
                account['supplies']['tiles']['amount'] = 1;
                render_tile(current_tile.lat, current_tile.lng)
            });
        });
    }

    function render_tile(lat, lng) {
        tile = get_tile(lat, lng, world_key, function(response) {
          current_tile = response;
          highlight_single_square(current_tile.id);
          render_tile_window();
        });
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
                let unit_owner_color = account.color;
                let lat = current_tile.lat;
                let lng = current_tile.lng;
                set_unit_icon(unit_id, terrain_key, unit_owner_color, lat, lng);
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