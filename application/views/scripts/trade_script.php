<script>
	main();

	function main() {
		handle_new_diplomacy();
		handle_input_change();
	}

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

	function handle_input_change() {
		$('.trade_supply_change').change(function(){
			let current_value = parseInt($(this).val());
			let max_value = parseInt($(this).prop('max'));
			$(this).removeClass('input-danger');
			if (current_value > max_value || current_value < 0) {
				$(this).addClass('input-danger');
			}
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
        $('#their_trade_supply_proposal_' + supply['slug']).prop('max', supply['amount']);
      });
    }
</script>