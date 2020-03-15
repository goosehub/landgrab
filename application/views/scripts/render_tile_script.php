<script>

    handle_select_settlement();
    handle_select_industry();

    function render_tile_window() {
        $('#tile_block').show();

        tile_name();
        tile_desc();
        tile_coord_link();
        tile_owner_info();
        tile_owner_username();
        tile_owner_country_name();
        tile_terrain();
        tile_resource_icon();
        tile_resource();
        tile_settlement_label();
        tile_industry_label();
        tile_population();
        tile_gdp();
        tile_register_plea();
        tile_first_claim();
        tile_unit();
        tile_defensive_bonus();
        enlist_select();
        settlement_select();
        industry_select();
    }
    function tile_name()
    {
        $('#tile_name').html(current_tile.tile_name);
        $('#tile_name_input').val(current_tile.tile_name);
        if (account.id === current_tile.account_key) {
            $('#edit_tile_name').show();
        }
        else {
            $('#edit_tile_name').hide();
        }
        $('#tile_name').show();
        $('#tile_name_input, #submit_tile_name').hide();
    }
    function tile_desc()
    {
        $('#tile_desc').html(current_tile.tile_desc);
        $('#tile_desc_input').html(current_tile.tile_desc);
        if (account.id === current_tile.account_key) {
            $('#edit_tile_desc').show();
        }
        else {
            $('#edit_tile_desc').hide();
        }
        $('#tile_desc').show();
        $('#tile_desc_input, #submit_tile_desc').hide();
    }
    function tile_coord_link()
    {
        $('#tile_coord_link').prop('href', '<?=base_url()?>world/' + current_tile.world_key + '?lng=' + current_tile.lng + '&lat=' + current_tile.lat);
        $('#tile_coord_link').html(current_tile.lng + ',' + current_tile.lat);
    }
    function tile_owner_info()
    {
        $('#tile_owner_info').hide();
        if (current_tile.account) {
            $('#tile_owner_info').show();
        }
    }
    function tile_owner_username()
    {
        $('#tile_owner_username').html(current_tile.account['username'] || '');
    }
    function tile_owner_country_name()
    {
        $('#tile_owner_country_name').html(current_tile.account['nation_name'] || '');
    }
    function tile_terrain()
    {
        $('#tile_terrain').html(terrains[current_tile.terrain_key - 1]['label'] || '--');
    }
    function tile_resource_icon()
    {
        $('#tile_resource_icon').hide();
        if (current_tile.resource_key) {
          $('#tile_resource_icon').show();
          $('#tile_resource_icon').attr('src', `../resources/icons/natural_resources/${current_tile.resource_key}.png`);
        }
    }
    function tile_resource()
    {
        $('#tile_resource').hide();
        if (current_tile.resource_key) {
            $('#tile_resource').html('(' + resources[current_tile.resource_key - 1]['label'] + ')');
            $('#tile_resource').show();
        }
    }
    function tile_settlement_label()
    {
        current_tile.settlement_key;
        if (current_tile.settlement_key) {
            $('#tile_settlement_label').html(settlements[current_tile.settlement_key  - 1]['label']);
        }
        else {
            $('#tile_settlement_label').html('--');
        }
    }
    function tile_industry_label()
    {
        $('#tile_industry_parent').hide();
        if (current_tile.industry_key) {
            $('#tile_industry_label').html(industries[current_tile.industry_key  - 1]['label']);
            $('#tile_industry_parent').show();
        }
    }
    function tile_population()
    {
        $('#tile_population').html(current_tile.population ? current_tile.population : '0');
    }
    function tile_gdp()
    {
        if (current_tile.industry_key) {
            $('#tile_gdp').html('$' + industries[current_tile.industry_key  - 1]['gdp'] + 'M');
        }
        else {
            $('#tile_gdp').html('$0');
        }
    }
    function tile_unit()
    {
        $('#tile_unit_parent').hide();
        if (current_tile.unit_key) {
            $('#tile_unit').html(unit_labels[current_tile.unit_key]);
            $('#tile_unit_parent').show();
        }
    }
    function tile_defensive_bonus()
    {
        $('#tile_defensive_bonus_parent').hide();
        let defensive_bonus = get_defensive_bonus_of_tile(current_tile);
        if (defensive_bonus !== 1) {
            $('#tile_defensive_bonus').html(defensive_bonus + 'X');
            $('#tile_defensive_bonus_parent').show();
        }
    }
    function enlist_select()
    {
        $('#tab_select_enlist, #enlist_select, #enlist_disabled, #enlist_enabled').hide();
        if (parseInt(current_tile.is_capitol) || parseInt(current_tile.is_base)) {
            $('#tab_select_enlist, #enlist_select').show();
            if (current_tile.unit_key) {
                $('#enlist_disabled').show();
            }
            else {
                $('#enlist_enabled').show();
            }
        }
    }
    function settlement_select()
    {
        $('#tab_select_settle, #settlement_select').hide();
        $('.set_settlement_button').addClass('btn-default').removeClass('btn-primary');
        if (account && current_tile.account_key === account['id']) {
            $('#tab_select_settle, #settlement_select').show();
            $('.set_settlement_button[data-id=' + current_tile.settlement_key + ']').addClass('btn-default').addClass('btn-primary');
        }
    }
    function industry_select()
    {
        $('#tab_select_industry, #industry_select').hide();
        $('.set_industry_button').addClass('btn-default').removeClass('btn-primary');
        if (account && current_tile.account_key === account['id'] && settlement_is_township(current_tile.settlement_key)) {
            $('#tab_select_industry, #industry_select').show();
            $('.set_industry_button[data-id=' + current_tile.industry_key + ']').removeClass('btn-default').addClass('btn-primary');
        }
    }
    function tile_register_plea()
    {
        $('#tile_register_plea').hide();
        if (!account) {
            $('#tile_register_plea').show();
        }
    }
    function tile_first_claim_invalid_ocean()
    {
        if (account && account['supplies']['tiles']['amount'] < 1 && current_tile.terrain_key == <?= OCEAN_KEY ?>) {
            return true;
        }
        return false;
    }
    function tile_first_claim_invalid_township()
    {
        if (account && account['supplies']['tiles']['amount'] < 1 && settlement_is_township(current_tile.settlement_key)) {
            return true;
        }
        return false;
    }
    function tile_first_claim()
    {
        $('#tile_first_claim_invalid_township').hide();
        $('#tile_first_claim_invalid_ocean').hide();
        $('#tile_first_claim').hide();
        if (tile_first_claim_invalid_ocean()) {
            $('#tile_first_claim_invalid_ocean').show();
        }
        else if (tile_first_claim_invalid_township()) {
            $('#tile_first_claim_invalid_township').show();
        }
        else if (account && account['supplies']['tiles']['amount'] < 1) {
            $('#tile_first_claim').show();
        }
    }
    function handle_select_settlement() {
        $('.set_settlement_button').click(function(){
            let settlement_key = $(this).data('id');
            let settlement = settlements.find(obj => {
                return obj.id == settlement_key
            });
            $('#select_settlement_header').html(settlement.label);
            $('#select_settlement_type').html(get_settlement_type_string(settlement));
            $('#select_settlement_pop').html(settlement.base_population);
            $('#select_settlement_terrain').html(get_settlement_terrain_string(settlement));
            $('#select_settlement_defensive_parent').hide();
            if (get_defensive_bonus_of_settlement_string(settlement.id)) {
                $('#select_settlement_defensive_bonus').html(get_defensive_bonus_of_settlement_string(settlement.id));
                $('#select_settlement_defensive_parent').show();
            }
            $('#select_settlement_input_parent').hide();
            if (settlement.input_desc) {
                $('#select_settlement_input').html(settlement.input_desc);
                $('#select_settlement_input_parent').show();
            }
            $('#select_settlement_output').html(settlement.output_desc);
        });
    }

    function handle_select_industry() {
        $('.set_industry_button').click(function(){
            let industry_key = $(this).data('id');
            let industry = industries.find(obj => {
                return obj.id == industry_key
            })
            $('#select_industry_header').html(industry.label);
            $('#select_industry_settlement').html(get_industry_settlement_string(industry.minimum_settlement_size));
            $('#select_industry_terrain').html(get_industry_terrain_string(industry.required_terrain_key));
            $('#select_industry_gdp').html(industry.gdp);
            $('#select_industry_input').html(industry.input_desc);
            $('#select_industry_output').html(industry.output_desc);
        });
    }
</script>