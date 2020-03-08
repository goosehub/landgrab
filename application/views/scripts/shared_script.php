<script>
    activate_bootstrap_popovers();

    function activate_bootstrap_popovers() {
        $(function () {
            $('[data-toggle="popover"]').popover()
        })
    }

    // Abstract simple ajax calls
    function ajax_post(url, data, callback) {
        $.ajax({
            url: base_url + url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data['error']) {
                    alert(data['error_message']);
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
            }
        });
    }

    // Abstract simple ajax calls
    function ajax_get(url, callback) {
        $.ajax({
            url: base_url + url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data['error']) {
                    alert(data['error_message']);
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

    // Upper case words
    function ucwords(str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function($1) {
            return $1.toUpperCase();
        });
    }

    function settlement_is_incorporated(settlement_key)
    {
        return settlement_key == <?= TOWN_KEY; ?> || settlement_key == <?= CITY_KEY; ?> || settlement_key == <?= METRO_KEY; ?>
    }
    function nl2br(str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

    function number_format(nStr) {
        if (!nStr) {
            return 0;
        }
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
        if (lng === 182) {
            lng = -178;
        }
        if (lng === -180) {
            lng = 180;
        }
        return lng;
    }

    function get_tile_border_color(tile) {
        let fill_color = "#FFFFFF";
        if (tile['account_key']) {
            fill_color = tile['color'];
        }
        return fill_color;
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
</script>