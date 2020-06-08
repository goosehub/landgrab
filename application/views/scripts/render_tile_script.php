<script>

    handle_select_settlement();
    handle_select_industry();

    function render_tile_window() {
        $('#tile_block').show();

        // Order may matter
        tile_name();
        tile_desc();
        tile_coord_link();
        tile_owner_info();
        tile_owner_username();
        tile_owner_country_name();
        tile_treaty();
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
        tile_offensive_bonus();
        tile_tab_toggle();
        settlement_selection_disable();
        industry_selection_disable();
        settlement_select();
        industry_select();
        enlist_select();
        tile_town_name(current_tile.settlement_key);
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
        // Debug
        $('#tile_id').html(current_tile.id);
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
    function tile_treaty()
    {
        $('#tile_treaty_parent').hide();
        if (current_tile.account_key && current_tile.account_key != account['id']) {
            $('#tile_treaty_parent').show();
            let treaty_key = find_treaty_by_account_key(current_tile.account_key);
            let treaty_name = get_treaty_name(treaty_key);
            $('#tile_treaty').removeClass();
            $('#tile_treaty').addClass(treaty_class(treaty_key));
            $('#tile_treaty').html(treaty_name);
        }
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
          $('#tile_resource_icon').attr('src', base_url + `resources/icons/natural_resources/${current_tile.resource_key}.png`);
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
        $('#tile_settlement_label_parent').hide();
        if (current_tile.settlement_key) {
            $('#tile_settlement_label_parent').show();
            $('#tile_settlement_label').html(settlements[current_tile.settlement_key  - 1]['label']);
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
        $('#tile_population_parent').hide();
        let population = format_population(current_tile.population);
        $('#tile_population').html(population);
        if (current_tile.population > 0) {
            $('#tile_population_parent').show();
        }
    }
    function format_population(population)
    {
        return population ? parseInt(population).toLocaleString() + 'K' : '0';
        // Alternative millions formatting
        // 'de-DE' uses period instead of comma
        let locale_type = population >= 1000 ? 'de-DE' : 'en-US';
        let suffix = population >= 1000 ? 'M' : 'K';
        let formatted_population = population ? parseInt(population).toLocaleString(locale_type) : '0';
        return formatted_population + suffix
    }
    function tile_gdp()
    {
        $('#tile_gdp_parent').hide();
        let sum_gdp = 0;
        if (current_tile.industry_key) {
            sum_gdp += parseInt(industries[current_tile.industry_key  - 1]['gdp']);
        }
        if (current_tile.settlement_key) {
            sum_gdp += parseInt(settlements[current_tile.settlement_key  - 1]['gdp']);
        }
        $('#tile_gdp').html('$' + sum_gdp + 'B');
        if (sum_gdp > 0) {
            $('#tile_gdp_parent').show();
        }
    }
    function tile_unit()
    {
        $('#tile_unit_parent').hide();
        if (current_tile.unit_key) {
            let unit_string = unit_labels[current_tile.unit_key];
            if (current_tile.unit_owner_key != current_tile.account_key) {
                unit_string += ' ' + '(' + current_tile.unit_owner_username + ')';
            }
            $('#tile_unit').html(unit_string);
            $('#tile_unit_parent').show();
        }
    }
    function tile_defensive_bonus()
    {
        $('#tile_defensive_bonus_parent').hide();
        let defensive_bonus = get_defensive_bonus_of_tile(current_tile);
        if (defensive_bonus > 0) {
            $('#tile_defensive_bonus').html('+' + defensive_bonus);
            $('#tile_defensive_bonus_parent').show();
        }
    }
    function tile_offensive_bonus()
    {
        $('#tile_offensive_bonus_parent').hide();
        let offensive_bonus = get_offensive_bonus_of_tile(current_tile);
        if (offensive_bonus > 0) {
            $('#tile_offensive_bonus').html('+' + offensive_bonus);
            $('#tile_offensive_bonus_parent').show();
        }
    }
    function tile_tab_toggle()
    {
        $('#tile_select_select, #tile_select_extended').hide();
        if (account && current_tile.account_key === account['id']) {
            $('#tile_select_select, #tile_select_extended').show();
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
    function settlement_selection_disable()
    {
        $('.preview_settlement_button.btn-soft-disabled').removeClass('btn-soft-disabled').addClass('btn-default');
        $('.preview_settlement_button').each(function(){
            let settlement_key = $(this).data('id');
            let output_supply_key = $(this).data('output');
            $(this).show();
            if (cash_crops_key_array.includes(output_supply_key) && output_supply_key != account.cash_crop_key) {
                $(this).hide();
            }
            if (!settlement_allowed_on_this_tile(settlement_key)) {
                $(this).removeClass('btn-default').addClass('btn-soft-disabled');
            }
        });
    }
    function industry_selection_disable()
    {
        $('.preview_industry_button.btn-soft-disabled').removeClass('btn-soft-disabled').addClass('btn-default');
        $('.preview_industry_button').each(function(){
            let industry_key = $(this).data('id');
            if (!industry_allowed_on_this_tile(industry_key)) {
                $(this).removeClass('btn-default').addClass('btn-soft-disabled');
            }
        });
    }
    function settlement_select()
    {
        $('.preview_settlement_button.btn-primary').removeClass('btn-primary').addClass('btn-default');
        if (account && current_tile.account_key === account['id']) {
            $('.preview_settlement_button[data-id=' + preview_settlement_key + ']').addClass('btn-default').addClass('btn-primary');
            $('#settlement_selection_icon_preview').prop('src', `${base_url}resources/icons/settlements/${preview_settlement_key}.png`);
            $('.preview_settlement_button.btn-action').removeClass('btn-action').addClass('btn-default');
            $('.preview_settlement_button[data-id=' + current_tile.settlement_key + ']').removeClass('btn-default').addClass('btn-action');
            $('#settlement_establish_parent').hide();
            $('#set_settlement_button').removeClass('disabled');
            if (current_tile.settlement_key != preview_settlement_key) {
                $('#settlement_establish_parent').show();
            }
            if (!settlement_allowed_on_this_tile(preview_settlement_key)) {
                $('#set_settlement_button').addClass('disabled');
            }
        }
    }
    function industry_select()
    {
        $('.preview_industry_button.btn-primary').removeClass('btn-primary').addClass('btn-default');
        $('#tab_select_industry, #industry_select').hide();
        if (account && current_tile.account_key === account['id'] && settlement_is_township(current_tile.settlement_key)) {
            $('#tab_select_industry, #industry_select').show();
            $('.preview_industry_button[data-id=' + preview_industry_key + ']').removeClass('btn-default').addClass('btn-primary');
            $('#industry_selection_icon_preview').prop('src', `${base_url}resources/icons/industries/${preview_industry_key}.png`);
            $('.preview_industry_button.btn-action').removeClass('btn-action').addClass('btn-default');
            $('.preview_industry_button[data-id=' + current_tile.industry_key + ']').removeClass('btn-default').addClass('btn-action');
            $('#set_industry_button').hide().val('');
            $('#set_industry_button').removeClass('disabled');
            if (current_tile.industry_key != preview_industry_key) {
                $('#set_industry_button').show();
            }
            if (!industry_allowed_on_this_tile(preview_industry_key)) {
                $('#set_industry_button').addClass('disabled');
            }
        }
    }
    function settlement_allowed_on_this_tile(settlement_key)
    {
        if (!settlement_key) {
            return true;
        }
        let settlement = get_settlement_from_state(settlement_key);
        if (!settlement_is_township(current_tile.settlement_key) && (settlement_key == city_key || settlement_key == metro_key)) {
            return false;
        }
        if (!settlement_allowed_on_terrain(current_tile.terrain_key, settlement)) {
            return false;
        }
        if (settlement_is_township(current_tile.settlement_key) && parseInt(current_tile.population) < parseInt(settlement.base_population)) {
            return false;
        }
        return true;
    }
    function industry_allowed_on_this_tile(industry_key)
    {
        let industry = get_industry_from_state(industry_key);
        if (!industry) {
            return false;
        }
        if (industry.required_terrain_key && industry.required_terrain_key != current_tile.terrain_key) {
            return false;
        }
        if (industry.minimum_settlement_size && industry.minimum_settlement_size > current_tile.settlement_key) {
            return false;
        }
        return true;
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
    function tile_first_claim_invalid_taken()
    {
        if (account && account['supplies']['tiles']['amount'] < 1 && current_tile.settlement_key) {
            return true;
        }
        return false;
    }
    function tile_first_claim()
    {
        $('#tile_first_claim_invalid_taken').hide();
        $('#tile_first_claim_invalid_ocean').hide();
        $('#tile_first_claim').hide();
        if (tile_first_claim_invalid_ocean()) {
            $('#tile_first_claim_invalid_ocean').show();
        }
        else if (tile_first_claim_invalid_taken()) {
            $('#tile_first_claim_invalid_taken').show();
        }
        else if (account && account['supplies']['tiles']['amount'] < 1) {
            // $('#tile_first_claim').show();
            $('.center_block').hide();
            $('#account_update_block').fadeIn();
            $('#update_nation_button').html('Start your nation here');
            $('#cash_crop_key_parent').show();
            $('#account_update_form').data('first-claim', true);
        }
    }
    function handle_select_settlement() {
        $('.preview_settlement_button').click(function(){
            let settlement_key = $(this).data('id');
            preview_settlement_key = settlement_key;
            settlement_select();
            render_settlement_extended(settlement_key);
        });
    }

    function handle_select_industry() {
        $('.preview_industry_button').click(function(){
            let industry_key = $(this).data('id');
            preview_industry_key = industry_key;
            if (industry_key) {
                industry_select();
                render_industry_extended(industry_key);
            }
        });
    }
    function render_settlement_extended(settlement_key) {
        $('#settlement_extended_info').hide();
        if (settlement_key == null || settlement_key == 1) {
            return;
        }
        $('#settlement_extended_info').show();
        let settlement = settlements.find(obj => {
            return obj.id == preview_settlement_key
        });
        $('#select_settlement_header').html(settlement.label);
        $('#select_settlement_type').html(get_settlement_type_string(settlement));
        $('#select_settlement_gdp').html('$' + number_format(settlement.gdp) + 'B');
        $('#select_settlement_pop').html(format_population(settlement.base_population));
        $('#select_settlement_terrain').html(get_settlement_terrain_string_with_color(settlement));
        $('#select_settlement_defensive_parent').hide();
        if (get_defensive_bonus_of_settlement_string(settlement.id)) {
            $('#select_settlement_defensive_bonus').html(get_defensive_bonus_of_settlement_string(settlement.id));
            $('#select_settlement_defensive_parent').show();
        }
        $('#select_settlement_required_population_parent').hide();
        $('#select_settlement_upfront_cost_parent').hide();
        $('#select_settlement_input_parent').hide();
        if (settlement_is_township(settlement.id)) {
            $('#select_settlement_required_population').html(format_population(settlement.base_population));
            $('#select_settlement_upfront_cost').html(settlement_upfront_cost_string(settlement));
            $('#select_settlement_input').html(settlement_input_string(settlement));
            $('#select_settlement_required_population_parent').show();
            $('#select_settlement_upfront_cost_parent').show();
            $('#select_settlement_input_parent').show();
        }
        let output = settlement.output ? settlement.output.label : 'Nothing';
        if (settlement_is_township(settlement.id)) {
            output = 'Industry';
        }
        if (settlement.output_supply_amount > 1) {
            output = number_format(settlement.output_supply_amount) + ' ' + output;
        }
        tile_town_name(settlement_key);
        $('#select_settlement_output').html(output);
    }
    function tile_town_name(settlement_key) {
        $('#town_tile_name_input').val(current_tile.tile_name);
        $('#town_tile_name_input').hide();
        if (settlement_key == town_key) {
            $('#town_tile_name_input').show();
        }
    }
    function render_industry_extended(industry_key) {
        $('#industry_extended_info').hide();
        if (industry_key == null) {
            return;
        }
        $('#industry_extended_info').show();
        let industry = industries.find(obj => {
            return obj.id == preview_industry_key
        })
        $('#select_industry_header').html(industry.label);
        $('#select_industry_settlement').html(get_industry_settlement_string(industry.minimum_settlement_size));
        $('#select_industry_terrain_parent').hide();
        if (industry.required_terrain_key) {
            $('#select_industry_terrain_parent').show();
            $('#select_industry_terrain').html(get_industry_terrain_string(industry.required_terrain_key));
        }
        $('#select_industry_gdp').html('$' + number_format(industry.gdp) + ' Billion');
        $('#select_industry_input_parent').hide();
        if (industry.inputs.length) {
            $('#select_industry_input_parent').show();
            $('#select_industry_input').html(industry_input_string(industry));
        }
        $('#select_industry_output_parent').hide();
        if (industry_output_string(industry)) {
            $('#select_industry_output_parent').show();
            $('#select_industry_output').html(industry_output_string(industry));
        }
        $('#select_industry_upfront_parent').hide();
        if (industry.upfront_cost) {
            $('#select_industry_upfront_parent').show();
            $('#select_industry_upfront').html('$' + number_format(industry.upfront_cost) + ' Billion');
        }
        $('#select_industry_special_parent').hide();
        if (industry.meta) {
            $('#select_industry_special_parent').show();
            $('#select_industry_special').html(industry.meta);
        }
    }
    function get_settlement_terrain_string_with_color(settlement)
    {
        let string = '';
        if (settlement.is_allowed_on_fertile == 1 && settlement.is_allowed_on_coastal == 1 && settlement.is_allowed_on_barren == 1 && settlement.is_allowed_on_mountain == 1 && settlement.is_allowed_on_tundra == 1) {
            return 'Any';
        }
        if (settlement.is_allowed_on_fertile == 1) {
            string += `<span class="terrain_value" style="color: ${fertile_text_color}">Fertile</span>, `;
        }
        if (settlement.is_allowed_on_coastal == 1) {
            string += `<span class="terrain_value" style="color: ${coastal_text_color}">Coastal</span>, `;
        }
        if (settlement.is_allowed_on_barren == 1) {
            string += `<span class="terrain_value" style="color: ${barren_text_color}">Barren</span>, `;
        }
        if (settlement.is_allowed_on_mountain == 1) {
            string += `<span class="terrain_value" style="color: ${mountain_text_color}">Mountain</span>, `;
        }
        if (settlement.is_allowed_on_tundra == 1) {
            string += `<span class="terrain_value" style="color: ${tundra_text_color}">Tundra</span>, `;
        }
        return string.slice(0,-2);
    }
    function get_industry_terrain_string(required_terrain_key)
    {
        if (fertile_key == required_terrain_key) {
            return `<span class="terrain_value" style="color: ${fertile_text_color}">Fertile</span>`;
        }
        if (barren_key == required_terrain_key) {
            return `<span class="terrain_value" style="color: ${barren_text_color}">Barren</span>`;
        }
        if (mountain_key == required_terrain_key) {
            return `<span class="terrain_value" style="color: ${mountain_text_color}">Mountain</span>`;
        }
        if (tundra_key == required_terrain_key) {
            return `<span class="terrain_value" style="color: ${tundra_text_color}">Tundra</span>`;
        }
        if (coastal_key == required_terrain_key) {
            return `<span class="terrain_value" style="color: ${coastal_text_color}">Coastal</span>`;
        }
        return 'Any';
    }
    function settlement_upfront_cost_string(settlement) {
        if (settlement.id == town_key) {
            return town_food_cost + ' Food';
        }
        if (settlement.id == city_key) {
            return city_food_cost + ' Food';
        }
        if (settlement.id == metro_key) {
            return metro_food_cost + ' Food';
        }
        return 0;
    }
    function settlement_input_string(settlement) {
        // Yep, it's one of those functions
        // Grouping of food and cash crops make this cleaner, or at least far easier to write and read
        // Dynamic variables names could be used to make this moore DRY
        // Also, we can have a straight string, but I want constants to control it
        // return settlement.input_desc;
        if (!account || current_tile.account_key != account['id']) {
            return;
        }
        let html = '';
        let sup = account.supplies;
        let food_amount = 0 + parseInt(sup['grain'].amount) + parseInt(sup['fruit'].amount) + parseInt(sup['vegetables'].amount) + parseInt(sup['livestock'].amount) + parseInt(sup['fish'].amount);
        let cash_crops_amount = 0 + parseInt(sup['coffee'].amount) + parseInt(sup['tea'].amount) + parseInt(sup['cannabis'].amount) + parseInt(sup['wine'].amount) + parseInt(sup['tobacco'].amount);
        if (settlement.id == town_key) {
            if (town_food_cost) {
                amount_class = food_amount < town_food_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Food</span>: <span class="${amount_class}">${number_format(food_amount)}</span>/${town_food_cost}`;
            }
            if (town_energy_cost) {
                amount_class = sup['energy'].amount < town_energy_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Energy</span>: <span class="${amount_class}">${number_format(sup['energy'].amount)}</span>/${town_energy_cost}`;
            }
            if (town_cash_crops_cost) {
                amount_class = cash_crops_amount < town_cash_crops_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Cash Crops</span>: <span class="${amount_class}">${number_format(cash_crops_amount)}</span>/${town_cash_crops_cost}`;
            }
            if (town_merchandise_cost) {
                amount_class = sup['merchandise'].amount < town_merchandise_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Merchandise</span>: <span class="${amount_class}">${number_format(sup['merchandise'].amount)}</span>/${town_merchandise_cost}`;
            }
            if (town_pharmaceuticals_cost) {
                amount_class = sup['pharmaceuticals'].amount < town_pharmaceuticals_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Pharmaceuticals</span>: <span class="${amount_class}">${number_format(sup['pharmaceuticals'].amount)}</span>/${town_pharmaceuticals_cost}`;
            }
            if (town_steel_cost) {
                amount_class = sup['steel'].amount < town_steel_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Steel</span>: <span class="${amount_class}">${number_format(sup['steel'].amount)}</span>/${town_steel_cost}`;
            }
        }
        if (settlement.id == city_key) {
            if (city_food_cost) {
                amount_class = food_amount < city_food_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Food</span>: <span class="${amount_class}">${number_format(food_amount)}</span>/${city_food_cost}`;
            }
            if (city_energy_cost) {
                amount_class = sup['energy'].amount < city_energy_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Energy</span>: <span class="${amount_class}">${number_format(sup['energy'].amount)}</span>/${city_energy_cost}`;
            }
            if (city_cash_crops_cost) {
                amount_class = cash_crops_amount < city_cash_crops_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Cash Crops</span>: <span class="${amount_class}">${number_format(cash_crops_amount)}</span>/${city_cash_crops_cost}`;
            }
            if (city_merchandise_cost) {
                amount_class = sup['merchandise'].amount < city_merchandise_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Merchandise</span>: <span class="${amount_class}">${number_format(sup['merchandise'].amount)}</span>/${city_merchandise_cost}`;
            }
            if (city_pharmaceuticals_cost) {
                amount_class = sup['pharmaceuticals'].amount < city_pharmaceuticals_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Pharmaceuticals</span>: <span class="${amount_class}">${number_format(sup['pharmaceuticals'].amount)}</span>/${city_pharmaceuticals_cost}`;
            }
            if (city_steel_cost) {
                amount_class = sup['steel'].amount < city_steel_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Steel</span>: <span class="${amount_class}">${number_format(sup['steel'].amount)}</span>/${city_steel_cost}`;
            }
        }
        if (settlement.id == metro_key) {
            if (metro_food_cost) {
                amount_class = food_amount < metro_food_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Food</span>: <span class="${amount_class}">${number_format(food_amount)}</span>/${metro_food_cost}`;
            }
            if (metro_energy_cost) {
                amount_class = sup['energy'].amount < metro_energy_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Energy</span>: <span class="${amount_class}">${number_format(sup['energy'].amount)}</span>/${metro_energy_cost}`;
            }
            if (metro_cash_crops_cost) {
                amount_class = cash_crops_amount < metro_cash_crops_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Cash Crops</span>: <span class="${amount_class}">${number_format(cash_crops_amount)}</span>/${metro_cash_crops_cost}`;
            }
            if (metro_merchandise_cost) {
                amount_class = sup['merchandise'].amount < metro_merchandise_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Merchandise</span>: <span class="${amount_class}">${number_format(sup['merchandise'].amount)}</span>/${metro_merchandise_cost}`;
            }
            if (metro_pharmaceuticals_cost) {
                amount_class = sup['pharmaceuticals'].amount < metro_pharmaceuticals_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Pharmaceuticals</span>: <span class="${amount_class}">${number_format(sup['pharmaceuticals'].amount)}</span>/${metro_pharmaceuticals_cost}`;
            }
            if (metro_steel_cost) {
                amount_class = sup['steel'].amount < metro_steel_cost ? 'text-danger' : 'text-success';
                html += `<br>${force_tab}<span class="text-danger">Steel</span>: <span class="${amount_class}">${number_format(sup['steel'].amount)}</span>/${metro_steel_cost}`;
            }
        }
        return html;
    }
    function insufficient_township_supplies(settlement) {
        if (!account || current_tile.account_key != account['id']) {
            return;
        }
        let sup = account.supplies;
        if (settlement.id == town_key) {
            if (sup['grain'].amount + sup['fruit'].amount + sup['vegetables'].amount + sup['livestock'].amount + sup['fish'].amount < town_food_cost) {
                return true;
            }
            if (sup['energy'].amount < town_energy_cost) {
                return true;
            }
        }
        if (settlement.id == city_key) {
            if (sup['grain'].amount + sup['fruit'].amount + sup['vegetables'].amount + sup['livestock'].amount + sup['fish'].amount < city_food_cost) {
                return true;
            }
            if (sup['coffee'].amount + sup['tea'].amount + sup['cannabis'].amount + sup['wine'].amount + sup['tobacco'].amount < city_cash_crops_cost) {
                return true;
            }
            if (sup['energy'].amount < city_energy_cost) {
                return true;
            }
            if (sup['merchandise'].amount < city_merchandise_cost) {
                return true;
            }
        }
        if (settlement.id == metro_key) {
            if (sup['grain'].amount + sup['fruit'].amount + sup['vegetables'].amount + sup['livestock'].amount + sup['fish'].amount < metro_food_cost) {
                return true;
            }
            if (sup['coffee'].amount + sup['tea'].amount + sup['cannabis'].amount + sup['wine'].amount + sup['tobacco'].amount < metro_cash_crops_cost) {
                return true;
            }
            if (sup['energy'].amount < metro_energy_cost) {
                return true;
            }
            if (sup['merchandise'].amount < metro_merchandise_cost) {
                return true;
            }
            if (sup['pharmaceuticals'].amount < metro_pharmaceuticals_cost) {
                return true;
            }
        }
        return false;
    }
    function industry_input_string(industry) {
        if (!account || current_tile.account_key != account['id']) {
            return;
        }
        let input_string = '';
        for (let i = 0; i < industry.inputs.length; i++) {
            input = industry.inputs[i];
            input_string += `<br>${force_tab}`;
            input_string += `<span class="text-danger">${input.label}</span>: `;
            let input_class = parseInt(account.supplies[input.slug].amount) < parseInt(input.amount) ? 'text-danger' : 'text-success';
            input_string += `<span class="${input_class}">${number_format(account.supplies[input.slug].amount)}</span>/`;
            input_string += `<span class="text-default">${number_format(input.amount)}</span>`;
        }
        return input_string;
    }
    function industry_output_string(industry) {
        let output_string = '';
        if (industry.output) {
            output_string = number_format(industry.output.amount) + ' ' + industry.output.label;
        }
        return output_string;
    }
</script>