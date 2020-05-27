<script>
	main();

	function main() {
		handle_new_diplomacy();
		handle_input_change();
		handle_declare_war();
		handle_send_trade_request();
		handle_open_trade_sent();
		handle_open_trade_received();
		handle_accept_trade_request();
		handle_reject_trade_request();
	}

	function update_offer_expire_hours() {
		$('#offer_expire_hours_parent').hide();
		if (parseInt(view_trade.trade_request.is_accepted) || parseInt(view_trade.trade_request.is_rejected)) {
			return;
		}
		let now = moment();
		let created_date = moment(view_trade.trade_request.created);
		let expired_date = moment(view_trade.trade_request.created).add(trade_expire_hours, 'hours');
		let diff_hours = expired_date.diff(now, 'hours');
		if (diff_hours >= 0) {
			let offer_expire_hours = diff_hours;
			if (offer_expire_hours == 0) {
				offer_expire_hours = 'less than 1';
			}
			$('#offer_expire_hours_parent').show();
			$('#offer_expire_hours').html(offer_expire_hours);
		}
	}

	function handle_accept_trade_request() {
		$('#accept_trade_request').click(function(){
			send_accept_trade_request();
		});
	}

	function send_accept_trade_request() {
		let data = {
			world_key: world_key,
			response_message: $('#view_input_trade_message_reply').val(),
		};
		ajax_post('game/accept_trade_request/' + view_trade.trade_request.id, data, function(response) {
			$('.center_block').hide();
			$('#view_input_trade_message_reply').val('');
			update_all_units();
		});
	}

	function mark_war_declare_as_seen() {
		for (let key in account.received_trades) {
			if (!parseInt(account.received_trades[key].request_seen) && account.received_trades[key].treaty_key == war_key) {
				mark_trade_received(account.received_trades[key].id, account.received_trades[key].request_account_key);
			}
		}
	}

	function handle_reject_trade_request() {
		$('#reject_trade_request').click(function(){
			send_reject_trade_request();
		});
	}

	function send_reject_trade_request() {
		let data = {
			world_key: world_key,
			response_message: $('#view_input_trade_message_reply').val(),
		};
		ajax_post('game/reject_trade_request/' + view_trade.trade_request.id, data, function(response) {
			$('.center_block').hide();
			$('#view_input_trade_message_reply').val('');
		});
	}

	function handle_open_trade_sent() {
		$('#trade_requests_sent').on('click', '.open_trade_sent', function(){
			let trade_request_key = $(this).data('id');
			let trade_partner_key = $(this).data('trade-partner-account-key');
			open_trade_sent(trade_request_key, trade_partner_key);
		});
	}
	function handle_open_trade_received() {
		$('#trade_requests_received').on('click', '.open_trade_received', function(){
			let trade_request_key = $(this).data('id');
			let trade_partner_key = $(this).data('trade-partner-account-key');
			open_trade_received(trade_request_key, trade_partner_key);
		});
	}

	function handle_new_diplomacy() {
		$('#start_new_diplomacy').click(function(){
			$('.center_block').hide();
			let account_id = $('#select_account_for_diplomacy').val();
			new_diplomacy(account_id);
		});
	}

	function new_diplomacy(account_id) {
		if (!account || account_id == account['id']) {
			return;
		}
		ajax_get('game/get_account_with_supplies/' + account_id, function(response) {
			trade_partner = response;
			render_new_diplomacy();
			$('#new_trade_block').show();
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
			if (confirm('Are you sure you wish to confirm war?')) {
				declare_war();
			}
		});
	}

	function handle_send_trade_request() {
		$('#send_trade_request').click(function(){
			send_trade_request();
		});
	}

	function open_trade_sent(trade_request_key, trade_partner_key) {
		ajax_get(`game/get_trade_request/${world_key}/${trade_request_key}/${trade_partner_key}`, function(response) {
			view_trade = response;
			trade_partner = view_trade.trade_partner;
			render_open_trade_sent();
		});
	}

	function open_trade_received(trade_request_key, trade_partner_key) {
		ajax_get(`game/get_trade_request/${world_key}/${trade_request_key}/${trade_partner_key}`, function(response) {
			view_trade = response;
			trade_partner = view_trade.trade_partner;
			render_open_trade_received();
		});
	}

	function mark_trade_received(trade_request_key, trade_partner_key) {
		ajax_get(`game/get_trade_request/${world_key}/${trade_request_key}/${trade_partner_key}`, function(response) {
		});
	}

	function render_open_trade_sent() {
		$('.center_block').hide();
		update_partner_username();
		update_partner_treaty();
		update_proposed_treaty();
		update_request_message();
		render_trade_buttons();
		update_partner_supplies_view_trade();
		update_own_supplies_view_trade();
		update_partner_supplies_new_trade();
		render_messages_for_trade_sent();
		update_offer_expire_hours();
		$('#view_trade_block').show();
	}
	function render_open_trade_received() {
		$('.center_block').hide();
		update_partner_username();
		update_partner_treaty();
		update_proposed_treaty();
		update_request_message();
		render_trade_buttons();
		update_partner_supplies_view_trade();
		update_own_supplies_view_trade();
		update_partner_supplies_new_trade();
		render_messages_for_trade_received();
		update_offer_expire_hours();
		$('#view_trade_block').show();
	}

	function render_messages_for_trade_sent() {
		$('#view_trade_your_reply').hide();
		$('#view_trade_request_message').hide();

	}

	function render_messages_for_trade_received() {
		$('#view_trade_your_reply').hide();
		$('#view_trade_response_message').hide();
		if (view_trade.trade_request.request_account_key != account.id && !parseInt(view_trade.trade_request.is_accepted) && !parseInt(view_trade.trade_request.is_rejected)) {
			$('#view_trade_your_reply').show();
		}
		else if (view_trade.trade_request.response_message) {
			$('#response_message').html(view_trade.trade_request.response_message);
			$('#view_trade_response_message').show();
		}
	}
	
	function render_messages_for_trade_sent() {
		$('#view_trade_your_reply').hide();
		$('#view_trade_response_message').hide();
		if (view_trade.trade_request.response_message) {
			$('#response_message').html(view_trade.trade_request.response_message);
			$('#view_trade_response_message').show();
		}
	}

	function render_trade_buttons() {
		$('#view_trade_actions').hide();
		if (view_trade.trade_request.request_account_key != account.id && !parseInt(view_trade.trade_request.is_accepted) && !parseInt(view_trade.trade_request.is_rejected)) {
			$('#view_trade_actions').show();
		}
	}

	function update_proposed_treaty() {
		let treaty = treaties[view_trade.trade_request.treaty_key];
		$('#proposed_treaty').html(treaty).removeClass('text-danger', 'text-success', 'text-info').addClass(treaty_class(view_trade.trade_request.treaty_key));
		$('#proposed_treaty').html(treaty);
	}
	function update_request_message() {
		$('#request_message').html(view_trade.trade_request.request_message);
	}

	function update_partner_supplies_view_trade() {
		$('.view_trade_supplies_of_partner .view_trade_supply_parent').hide();
		let partner_supplies = view_trade.trade_request.receive_account_key == account.id ? view_trade.receive_supplies : view_trade.request_supplies;
		for (let key in partner_supplies) {
			let supply = partner_supplies[key];
			$('.view_trade_supplies_of_partner .view_trade_supply_parent[data-id="' + supply.supply_key + '"').show();
			$('#view_partner_trade_supply_proposal_' + supply.supply_key).html(format_number(supply.amount));
		}
	}

	function update_own_supplies_view_trade() {
		$('.view_trade_supplies_of_own .view_trade_supply_parent').hide();
		let own_supplies = view_trade.trade_request.receive_account_key == account.id ? view_trade.request_supplies : view_trade.receive_supplies;
		for (let key in own_supplies) {
			let supply = own_supplies[key];
			$('.view_trade_supplies_of_own .view_trade_supply_parent[data-id="' + supply.supply_key + '"').show();
			$('#view_our_trade_supply_offer_' + supply.supply_key).html(format_number(supply.amount));
		}
	}

	function send_trade_request() {
		let data = {
			world_key: world_key,
			trade_partner_key: trade_partner.id,
			supplies_offered: JSON.stringify(supplies_offered()),
			supplies_demanded: JSON.stringify(supplies_demanded()),
			message: $('#input_trade_message').val(),
			treaty: $('#input_treaty').val(),
		};
		ajax_post('game/send_trade_request', data, function(response) {
			$('.center_block').hide();
			reset_new_trade_form();
			open_trade_received(response.trade_request_key, trade_partner.id);
		});
	}

	function reset_new_trade_form() {
		$('#input_trade_message').val('');
		$('.trade_supply_change').val(0);
	}

	function supplies_offered() {
		let supplies_offered = [];
		$('.own_supply_trade').each(function(){
			if ($(this).val() > 0) {
				supplies_offered.push(
					{
						'supply_key': $(this).data('id'),
						'amount': $(this).val(),
					}
				)
			}
		});
		return supplies_offered;
	}

	function supplies_demanded() {
		let supplies_demanded = [];
		$('.partner_supply_trade').each(function(){
			if ($(this).val() > 0) {
				supplies_demanded.push(
					{
						'supply_key': $(this).data('id'),
						'amount': $(this).val(),
					}
				)
			}
		});
		return supplies_demanded;
	}

	function declare_war() {
		let data = {
			world_key: world_key,
			message: $('#input_trade_message').val(),
			trade_partner_key: trade_partner.id,
		};
		ajax_post('game/declare_war', data, function(response) {
			$('.center_block').hide();
			update_all_units();
		});
	}

	function render_new_diplomacy() {
		update_partner_username();
		update_partner_treaty();
		update_partner_supplies_new_trade();
		$('#input_trade_message').val('');
	}

	function update_partner_username() {
		$('.trade_request_partner_username').html(trade_partner.username + ' Offer');
	}

	function update_partner_treaty() {
		$('#declare_war').show();
		let treaty_key = find_treaty_by_account_key(trade_partner.id)
		if (treaty_key == war_key) {
			$('#declare_war').hide();
		}
		let treaty = treaties[treaty_key];
		$('.current_treaty').html(treaty).removeClass('text-danger', 'text-success', 'text-info').addClass(treaty_class(treaty_key));
	}

	function update_partner_supplies_new_trade() {
	  Object.keys(trade_partner.supplies).forEach(function(key) {
		let supply = trade_partner.supplies[key];
		$('.partner_trade_supply_current_' + supply['slug']).html(format_number(supply['amount']));
		$('#partner_trade_supply_proposal_' + supply['slug']).prop('max', supply['amount'] > 0 ? supply['amount'] : 0);
	  });
	}
</script>