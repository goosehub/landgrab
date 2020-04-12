<script>
	handle_new_diplomacy();

	function handle_new_diplomacy() {
		$('#start_new_diplomacy').click(function(){
		    $('.center_block').hide();
		    $('#trade_block').show();
		    let account_id = $('#select_account_for_diplomacy').val();
			ajax_get('game/get_account_with_supplies/' + account_id, function(response) {
			    render_new_diplomacy(response);
			});
		});
	}

	function render_new_diplomacy(their_account) {
		$('.trade_request_their_username').html(their_account.username);
		update_their_supplies(their_account.supplies);
	};

    function update_their_supplies(supplies) {
      Object.keys(supplies).forEach(function(key) {
        let supply = supplies[key];
        $('#their_trade_supply_current_' + supply['slug']).html(supply['amount']);
      });
    }
</script>