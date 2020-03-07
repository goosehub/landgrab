<script>
    function tile_name(d)
    {
        $('#tile_name').html(d['tile_name'] || 'Unclaimed')
    }
    function tile_desc(d)
    {
        $('#tile_desc').html(d['tile_desc'] || '')
    }
    function tile_coord_link(d)
    {
        $('#tile_coord_link').prop('href', '<?=base_url()?>world/' + d['world_key'] + '?lng=' + d['lng'] + '&lat=' + d['lat']);
        $('#tile_coord_link').html(d['lng'] + ',' + d['lat']);
    }
    function tile_owner_info(d)
    {
        $('#tile_owner_info').hide();
        if (d['account']) {
            $('#tile_owner_info').show();
        }
    }
    function tile_owner_username(d)
    {
        $('#tile_owner_username').html(d['account']['username'] || '');
    }
    function tile_owner_country_name(d)
    {
        $('#tile_owner_country_name').html(d['account']['nation_name'] || '');
    }
    function tile_terrain(d)
    {
        $('#tile_terrain').html(terrains[d['terrain_key'] - 1]['label'] || '--');
    }
    function tile_resource_icon(d)
    {
        $('#tile_resource_icon').hide();
        if (d['resource_key']) {
          $('#tile_resource_icon').show();
          $('#tile_resource_icon').attr('src', `../resources/icons/natural_resources/${d['resource_key']}.png`);
        }
    }
    function tile_resource(d)
    {
        $('#tile_resource').hide();
        if (d['resource_key']) {
            $('#tile_resource').html('(' + resources[d['resource_key'] - 1]['label'] + ')');
            $('#tile_resource').show();
        }
    }
    function tile_settlement_label(d)
    {
        if (d['settlement_key']) {
            $('#tile_settlement_label').html(settlements[d['settlement_key']  - 1]['label']);
        }
        else {
            $('#tile_settlement_label').html('--');
        }
    }
    function tile_industry_label(d)
    {
        if (d['industry_key']) {
            $('#tile_industry_label').html(industries[d['industry_key']  - 1]['label']);
        }
        else {
            $('#tile_industry_label').html('--');
        }
    }
    function tile_population(d)
    {
        if (d['settlement_key']) {
            $('#tile_population').html(settlements[d['settlement_key']  - 1]['base_population'] + 'K');
        }
        else {
            $('#tile_population').html('--');
        }
    }
    function tile_gdp(d)
    {
        if (d['industry_key']) {
            $('#tile_gdp').html('$' + industries[d['industry_key']  - 1]['gdp'] + 'M');
        }
        else {
            $('#tile_gdp').html('--');
        }
    }
    function tile_unit(d)
    {
        $('#tile_unit_parent').hide();
        if (d['unit_key']) {
            $('#tile_unit').html(unit_labels[d['unit_key']]);
            $('#tile_unit_parent').show();
        }
        else {
            $('#tile_population').html('--');
        }
    }
    function settlement_select(d)
    {
        $('#settlement_select').hide();
        $('.set_settlement_button').addClass('btn-default').removeClass('btn-primary');
        if (account && d['account_key'] === account['id']) {
            $('#settlement_select').show();
            $('.set_settlement_button[data-id=' + d['settlement_key'] + ']').addClass('btn-default').addClass('btn-primary');
        }
    }
    function industry_select(d)
    {
        $('#industry_select').hide();
        $('.set_industry_button').addClass('btn-default').removeClass('btn-primary');
        if (account && d['account_key'] === account['id'] && settlement_is_incorporated(d['settlement_key'])) {
            $('#industry_select').show();
            $('.set_industry_button[data-id=' + d['industry_key'] + ']').removeClass('btn-default').addClass('btn-primary');
        }
    }
    function tile_register_plea(d)
    {
        $('#tile_register_plea').hide();
        if (!account) {
            $('#tile_register_plea').show();
        }
    }
    function tile_first_claim_invalid_ocean(D)
    {
        if (account && account['supplies']['tiles']['amount'] < 1 && d['terrain_key'] == <?= OCEAN_KEY ?>) {
            $('#tile_first_claim_invalid_ocean').show();
            return true;
        }
        return false;
    }
    function tile_first_claim_invalid_incorporated(d)
    {
        if (account && account['supplies']['tiles']['amount'] < 1 && settlement_is_incorporated(d['settlement_key'])) {
            $('#tile_first_claim_invalid_incorporated').show();
            return true;
        }
        return false;
    }
    function tile_first_claim(d)
    {
        $('#tile_first_claim_invalid_incorporated').hide();
        $('#tile_first_claim_invalid_ocean').hide();
        $('#tile_first_claim').hide();
        if (tile_first_claim_invalid_ocean(d)) {
            return;
        }
        if (tile_first_claim_invalid_incorporated(d)) {
            return;
        }
        if (account && account['supplies']['tiles']['amount'] < 1) {
            $('#tile_first_claim').show();
        }
    }
    function settlement_is_incorporated(settlement_key)
    {
        return settlement_key == <?= TOWN_KEY; ?> || settlement_key == <?= CITY_KEY; ?> || settlement_key == <?= METRO_KEY; ?>
    }
</script>