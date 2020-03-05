<script>
    function reset_tile_visible() {
        $('#settlement_select').hide();
        $('#industry_select').hide();
    }
    function tile_name(d) {
        $('#tile_name').html(d['tile_name'] || 'Unclaimed')
    }
    function tile_desc(d) {
        $('#tile_desc').html(d['tile_desc'] || '')
    }
    function tile_coord_link(d) {
        $('#tile_coord_link').prop('href', '<?=base_url()?>world/' + d['world_key'] + '?lng=' + d['lng'] + '&lat=' + d['lat']);
        $('#tile_coord_link').html(d['lng'] + ',' + d['lat']);
    }
    function tile_owner_info(d) {
        $('#tile_owner_info').hide();
        if (d['account']) {
            $('#tile_owner_info').show();
        }
    }
    function tile_owner_username(d) {
        $('#tile_owner_username').html(d['account']['username'] || '');
    }
    function tile_owner_country_name(d) {
        $('#tile_owner_country_name').html(d['account']['nation_name'] || '');
    }
    function tile_terrain(d) {
        $('#tile_terrain').html(terrains[d['terrain_key'] - 1]['label'] || '--');
    }
    function tile_resource_icon(d) {
        $('#tile_resource_icon').hide();
        if (d['resource_key']) {
          $('#tile_resource_icon').show();
          $('#tile_resource_icon').attr('src', `../resources/icons/natural_resources/${d['resource_key']}.png`);
        }
    }
    function tile_resource(d) {
        if (d['resource_key']) {
            $('#tile_resource').html(resources[d['resource_key'] - 1]['label'] || '--');
        }
        else {
            $('#tile_resource').html('None');
        }
    }
    function tile_settlement_label(d) {
        $('#tile_settlement_label').html(d['settlement_key'] || '--');
    }
    function tile_industry_label(d) {
        $('#tile_industry_label').html(d['industry_key'] || '--');
    }
    function tile_population(d) {
        $('#tile_population').html(d['industry_key'] || '--');
    }
    function tile_gdp(d) {
        $('#tile_gdp').html(d['gdp'] || '--');
    }
    function settlement_select(d) {
        $('#settlement_select').hide();
    }
    function industry_select(d) {
        $('#industry_select').hide();
    }
</script>