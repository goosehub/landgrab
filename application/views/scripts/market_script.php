<!-- Market Script -->
<script>

$('.sell_button_confirm_icon').fadeTo(1, 0);

handle_sell_request();

function handle_sell_request() {
    $('.sell_button').click(function(){
        let supply_key = $(this).data('id');
        sell_supply(supply_key, supply_key);
    })
}

function sell_supply(supply_key, supply_key) {
    let data = {
        world_key: world_key,
        supply_key: supply_key,
    };
    ajax_post('game/sell', data, function(response) {
        get_account_update();
        $('.sell_button_confirm_icon[data-id=' + supply_key + ']').fadeTo(500, 1);
        $('.sell_button_confirm_icon[data-id=' + supply_key + ']').fadeTo(2000, 0);
    });
}

</script>