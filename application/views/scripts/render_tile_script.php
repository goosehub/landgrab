<script>
    function render_tile_window() {
        $('#tile_block').show();

        tile_name(current_tile);
        tile_desc(current_tile);
        tile_coord_link(current_tile);
        tile_owner_info(current_tile);
        tile_owner_username(current_tile);
        tile_owner_country_name(current_tile);
        tile_terrain(current_tile);
        tile_resource_icon(current_tile);
        tile_resource(current_tile);
        tile_settlement_label(current_tile);
        tile_industry_label(current_tile);
        tile_population(current_tile);
        tile_gdp(current_tile);
        tile_register_plea(current_tile);
        tile_first_claim(current_tile);
        tile_unit(current_tile);
        settlement_select(current_tile);
        industry_select(current_tile);
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
        if (current_tile.industry_key) {
            $('#tile_industry_label').html(industries[current_tile.industry_key  - 1]['label']);
        }
        else {
            $('#tile_industry_label').html('--');
        }
    }
    function tile_population()
    {
        if (current_tile.settlement_key) {
            $('#tile_population').html(settlements[current_tile.settlement_key  - 1]['base_population'] + 'K');
        }
        else {
            $('#tile_population').html('--');
        }
    }
    function tile_gdp()
    {
        if (current_tile.industry_key) {
            $('#tile_gdp').html('$' + industries[current_tile.industry_key  - 1]['gdp'] + 'M');
        }
        else {
            $('#tile_gdp').html('--');
        }
    }
    function tile_unit()
    {
        $('#tile_unit_parent').hide();
        if (current_tile.unit_key) {
            $('#tile_unit').html(unit_labels[current_tile.unit_key]);
            $('#tile_unit_parent').show();
        }
        else {
            $('#tile_population').html('--');
        }
    }
    function settlement_select()
    {
        $('#settlement_select').hide();
        $('.set_settlement_button').addClass('btn-default').removeClass('btn-primary');
        if (account && current_tile.account_key === account['id']) {
            $('#settlement_select').show();
            $('.set_settlement_button[data-id=' + current_tile.settlement_key + ']').addClass('btn-default').addClass('btn-primary');
        }
    }
    function industry_select()
    {
        $('#industry_select').hide();
        $('.set_industry_button').addClass('btn-default').removeClass('btn-primary');
        if (account && current_tile.account_key === account['id'] && settlement_is_incorporated(current_tile.settlement_key)) {
            $('#industry_select').show();
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
            $('#tile_first_claim_invalid_ocean').show();
            return true;
        }
        return false;
    }
    function tile_first_claim_invalid_incorporated()
    {
        if (account && account['supplies']['tiles']['amount'] < 1 && settlement_is_incorporated(current_tile.settlement_key)) {
            $('#tile_first_claim_invalid_incorporated').show();
            return true;
        }
        return false;
    }
    function tile_first_claim()
    {
        $('#tile_first_claim_invalid_incorporated').hide();
        $('#tile_first_claim_invalid_ocean').hide();
        $('#tile_first_claim').hide();
        if (tile_first_claim_invalid_ocean()) {
            return;
        }
        if (tile_first_claim_invalid_incorporated()) {
            return;
        }
        if (account && account['supplies']['tiles']['amount'] < 1) {
            $('#tile_first_claim').show();
        }
    }
</script>