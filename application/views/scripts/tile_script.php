<script>

    handle_edit_tile_meta();
    handle_first_claim();
    handle_set_settlement();
    handle_set_industry();

    function handle_set_settlement() {
        $('.set_settlement_button').click(function(event) {
            console.log(event);
            console.log($(this));
            let data = {
                settlement_id: $(this).data('id'),
                tile_id: current_tile.id,
            };
            ajax_post('game/update_settlement', data, function(response) {
                // 
            });
        });
    }
    function handle_set_industry() {
        $('.set_industry_button').click(function(event) {
            console.log(event);
            console.log($(this));
            let data = {
                industry_id: $(this).data('id'),
                tile_id: current_tile.id,
            };
            ajax_post('game/update_industry', data, function(response) {
                // 
            });
        });
    }

    function handle_edit_tile_meta() {
        $('#edit_tile_name').click(function(event) {
            $('#edit_tile_name, #tile_name').hide();
            $('#tile_name_input, #submit_tile_name').show();
        });
        $('#edit_tile_desc').click(function(event) {
            $('#edit_tile_desc, #tile_desc').hide();
            $('#tile_desc_input, #submit_tile_desc').show();
        });
        $('#submit_tile_name').click(function(event) {
            let data = {
                tile_id: current_tile.id,
                tile_name: $('#tile_name_input').val(),
            };
            ajax_post('game/update_tile_name', data, function(response) {
                $('#tile_name').html($('#tile_name_input').val());
                $('#edit_tile_name, #tile_name').show();
                $('#tile_name_input, #submit_tile_name').hide();
            });
        });
        $('#submit_tile_desc').click(function(event) {
            let data = {
                tile_id: current_tile.id,
                tile_desc: $('#tile_desc_input').val(),
            }
            ajax_post('game/update_tile_desc', data, function(response) {
                $('#tile_desc').html(nl2br($('#tile_desc_input').val()));
                $('#edit_tile_desc, #tile_desc').show();
                $('#tile_desc_input, #submit_tile_desc').hide();
            });
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
            });
        });
    }

</script>