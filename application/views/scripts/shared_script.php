<script>
    function settlement_is_incorporated(settlement_key)
    {
        return settlement_key == <?= TOWN_KEY; ?> || settlement_key == <?= CITY_KEY; ?> || settlement_key == <?= METRO_KEY; ?>
    }
    function nl2br(str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

    // Abstract simple ajax calls
    function ajax_post(url, data, callback) {
        $.ajax({
            url: base_url + url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                // Handle errors
                // console.log(url);
                // console.log(data);
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
                // Handle errors
                // console.log(url);
                // console.log(data);
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
</script>