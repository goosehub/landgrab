<script>
    activate_bootstrap_popovers();
    extend_date();

    function format_number(number) {
        return parseInt(number).toLocaleString('en-US');
    }

    function extend_date() {
        Date.prototype.addHours = function(h) {
            this.setTime(this.getTime() + (h*60*60*1000));
            return this;
        }
    }

    function activate_bootstrap_popovers() {
        $(function () {
            $('[data-toggle="popover"]').popover()
        })
    }

    // Abstract simple ajax calls
    function ajax_post(url, data, callback, request_name = false, fail_callback = null) {
        if (request_name) {
            active_requests[request_name] = true;
        }
        $.ajax({
            url: base_url + url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data['error']) {
                    swal.close();
                    setTimeout(function(){
                        swal('', data['error_message'], 'warning');
                    }, 300);
                    if (fail_callback) {
                        fail_callback();
                    }
                    return false;
                }

                // Do callback if provided
                if (callback && typeof(callback) === 'function') {
                    callback(data);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.error(xhr.status);
                console.error(thrownError);
            },
            complete: function(data) {
                if (request_name) {
                    active_requests[request_name] = false;
                }
            }
        });
    }

    // Abstract simple ajax calls
    function ajax_get(url, callback, request_name = false) {
        $.ajax({
            url: base_url + url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data['error']) {
                    swal.close();
                    setTimeout(function(){
                        swal('', data['error_message'], 'warning');
                    }, 100);
                    return false;
                }

                // Do callback if provided
                if (callback && typeof(callback) === 'function') {
                    callback(data);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.error(xhr.status);
                console.error(thrownError);
            },
            complete: function(data) {
                if (request_name) {
                    active_requests[request_name] = false;
                }
            }
        });
    }

    // https://stackoverflow.com/a/901144/3774582
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    function string_contains(string, sub_string) {
        if (string.indexOf(sub_string) !== -1) {
            return true;
        }
        return false;
    }

    // For rounding tile coords
    function round_down(n) {
        if (n > 0) {
            return Math.ceil(n / tile_size) * tile_size;
        } else if (n < 0) {
            return Math.ceil(n / tile_size) * tile_size;
        } else {
            return 0;
        }
    }

    // Sort by id column
    function sort_by_id(list) {
        list.sort((a, b) => (parseInt(a.id) > parseInt(b.id)) ? 1 : -1);
        return list;
    }

    // Upper case words
    function ucwords(str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function($1) {
            return $1.toUpperCase();
        });
    }

    function find_unit_color(unit_owner_key)
    {
        if (unit_owner_key == account.id) {
          return 'own';
        }
        let treaty_key = find_treaty_by_account_key(unit_owner_key);
        if (treaty_key == war_key) {
          return 'enemy';
        }
        if (treaty_key == passage_key) {
          return 'ally';
        }
        return 'neutral';
    }

    function settlement_is_township(settlement_key)
    {
        return settlement_key == <?= TOWN_KEY; ?> || settlement_key == <?= CITY_KEY; ?> || settlement_key == <?= METRO_KEY; ?>
    }

    function settlement_allowed_on_terrain(terrain_key, settlement)
    {
        if (terrain_key == fertile_key && parseInt(settlement.is_allowed_on_fertile)) {
            return true;
        }
        if (terrain_key == coastal_key && parseInt(settlement.is_allowed_on_coastal)) {
            return true;
        }
        if (terrain_key == barren_key && parseInt(settlement.is_allowed_on_barren)) {
            return true;
        }
        if (terrain_key == mountain_key && parseInt(settlement.is_allowed_on_mountain)) {
            return true;
        }
        if (terrain_key == tundra_key && parseInt(settlement.is_allowed_on_tundra)) {
            return true;
        }
        return false;
    }

    function get_defensive_bonus_of_tile(tile)
    {
        let defensive_bonus = 0;
        if (tile.terrain_key == tundra_key) {
            defensive_bonus += tundra_defensive_bonus;
        }
        if (tile.terrain_key == mountain_key) {
            defensive_bonus += mountain_defensive_bonus;
        }
        if (tile.settlement_key == town_key) {
            defensive_bonus += town_defensive_bonus;
        }
        if (tile.settlement_key == city_key) {
            defensive_bonus += city_defensive_bonus;
        }
        if (tile.settlement_key == metro_key) {
            defensive_bonus += metro_defensive_bonus;
        }
        return defensive_bonus;
    }

    function get_offensive_bonus_of_tile(tile)
    {
        let offensive_bonus = 0;
        if (tile.terrain_key == barren_key) {
            offensive_bonus += barren_offensive_bonus;
        }
        return offensive_bonus;
    }

    function trade_status(trade) {
        if (trade.is_declared && parseInt(trade.is_declared)) {
            return 'War';
        }
        if (trade.is_accepted && parseInt(trade.is_accepted)) {
            return 'Accepted';
        }
        if (trade.is_rejected && parseInt(trade.is_rejected)) {
            return 'Rejected';
        }
        return 'Pending';
    }

    function trade_status_class(trade) {
        if (trade.is_declared && parseInt(trade.is_declared)) {
            return 'text-danger';
        }
        if (trade.is_accepted && parseInt(trade.is_accepted)) {
            return 'text-success';
        }
        if (trade.is_rejected && parseInt(trade.is_rejected)) {
            return 'text-warning';
        }
        return 'text-primary';
    }

    function get_defensive_bonus_of_settlement_string(settlement_key)
    {
        if (settlement_key == town_key) {
            return '+' + town_defensive_bonus;
        }
        if (settlement_key == city_key) {
            return '+' + city_defensive_bonus;
        }
        if (settlement_key == metro_key) {
            return '+' + metro_defensive_bonus;
        }
        return false;
    }

    function get_settlement_terrain_string(settlement)
    {
        let string = '';
        if (settlement.is_allowed_on_fertile == 1 && settlement.is_allowed_on_coastal == 1 && settlement.is_allowed_on_barren == 1 && settlement.is_allowed_on_mountain == 1 && settlement.is_allowed_on_tundra == 1) {
            return 'Any';
        }
        if (settlement.is_allowed_on_fertile == 1) {
            string += 'Fertile, ';
        }
        if (settlement.is_allowed_on_coastal == 1) {
            string += 'Coastal, ';
        }
        if (settlement.is_allowed_on_barren == 1) {
            string += 'Barren, ';
        }
        if (settlement.is_allowed_on_mountain == 1) {
            string += 'Mountain, ';
        }
        if (settlement.is_allowed_on_tundra == 1) {
            string += 'Tundra, ';
        }
        return string.slice(0,-2);
    }

    function get_settlement_type_string(settlement)
    {
        if (settlement.is_township == 1) {
            return 'Township';
        }
        if (settlement.is_food == 1) {
            return 'Agriculture';
        }
        if (settlement.is_material == 1) {
            return 'Materials';
        }
        if (settlement.is_energy == 1) {
            return 'Energy';
        }
        if (settlement.is_cash_crop == 1) {
            return 'Luxuries';
        }
        return 'Other';
    }

    function get_industry_settlement_string(minimum_settlement_size)
    {
        if (town_key == minimum_settlement_size) {
            return 'Town or Larger';
        }
        if (city_key == minimum_settlement_size) {
            return 'City Or Larger';
        }
        if (metro_key == minimum_settlement_size) {
            return 'Metro';
        }
        return 'Any';
    }

    function find_treaty_by_account_key(account_key) {
        if (!account || !account.treaties || account_key == account.id) {
            return peace_key;
        }
        for (let i = 0; i < account.treaties.length; i++) {
            let aggrement = account.treaties[i];
            if (account_key == aggrement.a_account_key || account_key == aggrement.b_account_key) {
                return aggrement.treaty_key;
            }
        }
        return peace_key;
    }

    function get_treaty_name(treaty_key) {
        let treaty_name = '';
        if (treaty_key == war_key) {
            treaty_name = 'War';
        }
        else if (treaty_key == peace_key) {
            treaty_name = 'Peace';
        }
        else if (treaty_key == passage_key) {
            treaty_name = 'Passage';
        }
        return treaty_name;
    }

    function treaty_class(treaty_key) {
        let treaty_class = '';
        if (treaty_key == war_key) {
            treaty_class = 'text-danger';
        }
        else if (treaty_key == peace_key) {
            treaty_class = 'text-success';
        }
        else if (treaty_key == passage_key) {
            treaty_class = 'text-info';
        }
        return treaty_class;
    }


    function nl2br(str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

    function number_format(nStr) {
        if (!nStr) {
            return 0;
        }
        nStr = Math.round(nStr);
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function tiles_are_adjacent(start_lat, start_lng, end_lat, end_lng) {
    // Ignore if ending same place we started
    if (start_lat === end_lat && start_lng === end_lng) {
        return false;
    }
    // Check if one is changed by 1, and other is the same
    allowed_lats = [start_lat, start_lat + tile_size, start_lat - tile_size];
    allowed_lngs = [start_lng, correct_lng(start_lng + tile_size), correct_lng(start_lng - tile_size)];
    if (
        (allowed_lats.includes(end_lat) && start_lng === end_lng) || 
        (allowed_lngs.includes(end_lng) && start_lat === end_lat)
        ) {
        return true;
    }
    return false;
    }

    function tiles_are_adjacent_diagonal(start_lat, start_lng, end_lat, end_lng) {
        // Ignore if ending same place we started
        if (start_lat === end_lat && start_lng === end_lng) {
            return false;
        }
        // Check that nothing is more than 1 tile size away from standard
        allowed_lats = [start_lat, start_lat + tile_size, start_lat - tile_size];
        allowed_lngs = [start_lng, start_lng + tile_size, start_lng - tile_size];
        if (allowed_lats.includes(end_lat) && allowed_lngs.includes(end_lng)) {
            return true;
        }
        return false;
    }

    function correct_lng(lng) {
        // Hardcoded for tile size 2 worlds
        if (lng === 182) {
            lng = -178;
        }
        if (lng === -180) {
            lng = 180;
        }
        return lng;
    }

    function get_settlement_from_state(settlement_key) {
        for (let i = 0; i < settlements.length; i++) {
            if (settlements[i].id == settlement_key) {
                return settlements[i];
            }
        }
    }

    function get_industry_from_state(industry_key) {
        for (let i = 0; i < industries.length; i++) {
            if (industries[i].id == industry_key) {
                return industries[i];
            }
        }
    }

    function get_tile_border_color(tile) {
        let fill_color = "#FFFFFF";
        if (tile['account_key']) {
            fill_color = tile['color'];
        }
        return fill_color;
    }

    function update_all_units() {
        // Look away, this is an ugly ugly work around to re-render units after treaty update
        get_account_update();
        setTimeout(function(){
            get_map_update('update_units');
        }, 3 * 1000);
    }

    function get_tile_terrain_color(terrain_key) {
        let terrain_color = false;
        if (terrain_key == <?= FERTILE_KEY; ?>) {
            terrain_color = '<?= FERTILE_COLOR; ?>';
        }
        if (terrain_key == <?= BARREN_KEY; ?>) {
            terrain_color = '<?= BARREN_COLOR; ?>';
        }
        if (terrain_key == <?= MOUNTAIN_KEY; ?>) {
            terrain_color = '<?= MOUNTAIN_COLOR; ?>';
        }
        if (terrain_key == <?= TUNDRA_KEY; ?>) {
            terrain_color = '<?= TUNDRA_COLOR; ?>';
        }
        if (terrain_key == <?= COASTAL_KEY; ?>) {
            terrain_color = '<?= COASTAL_COLOR; ?>';
        }
        if (terrain_key == <?= OCEAN_KEY; ?>) {
            terrain_color = '<?= OCEAN_COLOR; ?>';
        }
        return terrain_color;
    }

    function setCookie(name, value) {
        let days = 30;
        var expires;
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
        document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = encodeURIComponent(name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
        }
        return null;
    }
</script>