<script>
	main();

	function main() {
		handle_new_diplomacy();
		handle_input_change();
		handle_declare_war();
	}

	function handle_new_diplomacy() {
		$('#start_new_diplomacy').click(function(){
		    $('.center_block').hide();
		    let account_id = $('#select_account_for_diplomacy').val();
			ajax_get('game/get_account_with_supplies/' + account_id, function(response) {
				trade_partner = response;
			    render_new_diplomacy();
			    $('#new_trade_block').show();
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

	function handle_declare_war() {
		$('#declare_war').click(function(){
			declare_war();
		});
	}

	function declare_war() {
		let data = {
			world_key: world_key,
			trade_partner_key: trade_partner.id,
		};
		ajax_post('game/declare_war', data, function(response) {
			$('.center_block').hide();
		});
	}

	function render_new_diplomacy() {
		update_partner_username();
		update_partner_treaty();
		update_partner_supplies();
	}

	function update_partner_username() {
		$('.trade_request_partner_username').html(trade_partner.username);
	}

	function update_partner_treaty() {
		$('#declare_war').show();
		let treaty_key = find_agreement_by_account_key(trade_partner.id)
		let treaty_class = '';
		if (treaty_key == war_key) {
			treaty_class = 'text-danger';
			$('#declare_war').hide();
		}
		else if (treaty_key == peace_key) {
			treaty_class = 'text-success';
		}
		else if (treaty_key == passage_key) {
			treaty_class = 'text-info';
		}
		let treaty = treaties[treaty_key];
		$('.current_treaty').html(treaty).removeClass('text-danger', 'text-success', 'text-info').addClass(treaty_class);
	}

    function update_partner_supplies() {
      Object.keys(trade_partner.supplies).forEach(function(key) {
        let supply = trade_partner.supplies[key];
        $('#partner_trade_supply_current_' + supply['slug']).html(supply['amount']);
        $('#partner_trade_supply_proposal_' + supply['slug']).prop('max', supply['amount'] > 0 ? supply['amount'] : 0);
      });
    }
</script>