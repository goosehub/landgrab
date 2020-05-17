<script>
    if (account) {
      get_account_update();
      setInterval(function() {
        if (!active_requests['account_update']) {
          get_account_update();
        }
      }, account_update_interval_ms);
    }

    function get_account_update() {
      ajax_get('game/get_user_full_account/' + world_key, function(response) {
        account = response;
        update_supplies(account.supplies);
        update_input_projections(account.input_projections);
        update_output_projections(account.output_projections);
        update_budget(account.budget);
        update_market_prices(account.market_prices);
        update_gdp_bonus_consume_supplies();
        update_sum_projections();
        update_max_support();
        update_law_wait();
        update_grouped_food_supply();
        update_grouped_cash_crops_supply();
        update_grouped_food_output();
        update_grouped_cash_crops_output();
        update_diverse_diet_population_bonus();
        update_cash_crops_support_bonus();
        update_stats_projections();
        update_ideology_ui()
        update_supply_alerts();
        update_unread_diplomacy();
        update_sent_trades();
        update_received_trades();
        update_current_treaties();
      }, 'account_update');
    }

    function update_received_trades() {
      $('#trade_requests_received_listing').html('');
      for (let key in account.received_trades) {
        let row_html = trade_requests_received_html(account.received_trades[key]);
        $('#trade_requests_received_listing').append(row_html);
      }
    }

    function update_sent_trades() {
      $('#trade_requests_sent_listing').html('');
      for (let key in account.sent_trades) {
        let row_html = trade_requests_sent_html(account.sent_trades[key]);
        $('#trade_requests_sent_listing').append(row_html);
      }
    }

    function update_current_treaties() {
      $('#current_treaties').html('');
      for (let key in account.treaties) {
        let row_html = current_treaty_html(account.treaties[key]);
        $('#current_treaties').append(row_html);
      }
    }

    function trade_requests_sent_html(trade) {
      let date_formatted = new Date(trade.modified).toLocaleDateString();
      return `
      <div class="row">
        <div class="col-md-2">
          <strong class="text-primary">${trade.username}</strong>
        </div>
        <div class="col-md-2">
          <small class="text-default">${trade.request_message.substring(0,100)}</small>
        </div>
        <div class="col-md-2">
          <small class="text-default">${trade.response_message.substring(0,100)}</small>
        </div>
        <div class="col-md-2">
          <strong class="${treaty_class(trade.treaty_key)}">${treaties[trade.treaty_key]}</strong>
        </div>
        <div class="col-md-2">
          <strong class="text-info">${date_formatted}</strong>
        </div>
        <div class="col-md-1">
          <strong class="${trade_status_class(trade)}">${trade_status(trade)}</strong>
        </div>
        <div class="col-md-1">
          <button class="open_trade_sent btn btn-primary ${trade.treaty_key == war_key ? 'hidden' : ''}" data-id="${trade.id}" data-trade-partner-account-key="${trade.receive_account_key}"><i class="fas fa-handshake"></i> Open</button>
        </div>
      </div>
      <hr>
      `
    }

    function trade_requests_received_html(trade) {
      let date_formatted = new Date(trade.modified).toLocaleDateString();
      return `
      <div class="row">
        <div class="col-md-2">
          <strong class="text-primary">${trade.username}</strong>
        </div>
        <div class="col-md-2">
          <small class="text-default">${trade.request_message.substring(0,100)}</small>
        </div>
        <div class="col-md-2">
          <small class="text-default">${trade.response_message.substring(0,100)}</small>
        </div>
        <div class="col-md-2">
          <strong class="${treaty_class(trade.treaty_key)}">${treaties[trade.treaty_key]}</strong>
        </div>
        <div class="col-md-2">
          <strong class="text-info">${date_formatted}</strong>
        </div>
        <div class="col-md-1">
          <strong class="${trade_status_class(trade)}">${trade_status(trade)}</strong>
        </div>
        <div class="col-md-1">
          <button class="open_trade_received btn btn-primary ${trade.treaty_key == war_key ? 'hidden' : ''}" data-id="${trade.id}" data-trade-partner-account-key="${trade.request_account_key}"><i class="fas fa-handshake"></i> Open</button>
        </div>
      </div>
      <hr>
      `
    }

    function current_treaty_html(treaty) {
      if (treaty.treaty_key == peace_key) {
        return '';
      }
      let date_formatted = new Date(treaty.modified).toLocaleDateString();
      return `
      <p class="lead">
        <strong class="text-primary">${treaty.a_username}</strong>
        and
        <strong class="text-primary">${treaty.b_username}</strong>
        have a treaty of
        <strong class="${treaty_class(treaty.treaty_key)}">${treaties[treaty.treaty_key]}</strong>
        last ratified on
        <strong class="info">${date_formatted}</strong>
      </p>
      <hr>
      `
    }

    function update_unread_diplomacy() {
      let unread = 0;
      for (let key in account.received_trades) {
        if (!parseInt(account.received_trades[key].request_seen)) {
          unread++;
        }
      }
      $('.diplomacy_unread_count').hide();
      if (unread) {
        $('.diplomacy_unread_count').html(unread);
        $('.diplomacy_unread_count').show();
      }
    }

    function update_ideology_ui() {
      $('.show_if_free_market, .show_if_socialism').hide();
      if (account.ideology == free_market_key) {
        $('.show_if_free_market').show();
      }
      if (account.ideology == socialism_key) {
        $('.show_if_socialism').show();
      }
    }

    function update_supply_alerts() {
      let count_negative_supplies = $('.government_supply.text-danger').not('.category_government_supply').length;
      if (count_negative_supplies == 0) {
        $('.menu_supply_negative_supplies_badge').hide();
        return;
      }
      $('.menu_supply_negative_supplies_badge').show();
      let plural = count_negative_supplies > 1 ? 's' : '';
      $('#count_negative_supplies').html(count_negative_supplies + ' Shortage' + plural);
    }

    function update_gdp_bonus_consume_supplies() {
      let port_supply = parseInt($('#government_supply_' + port_key).html());
      if (port_supply) {
        $('#input_projection_' + port_key).html('-1');
      }
      let machinery_supply = parseInt($('#government_supply_' + machinery_key).html());
      if (machinery_supply) {
        $('#input_projection_' + machinery_key).html('-1');
      }
      let automotive_supply = parseInt($('#government_supply_' + automotive_key).html());
      if (automotive_supply) {
        $('#input_projection_' + automotive_key).html('-1');
      }
      let aerospace_supply = parseInt($('#government_supply_' + aerospace_key).html());
      if (aerospace_supply) {
        $('#input_projection_' + aerospace_key).html('-1');
      }
      let entertainment_supply = parseInt($('#government_supply_' + entertainment_key).html());
      if (entertainment_supply) {
        $('#input_projection_' + entertainment_key).html('-1');
      }
      let financial_supply = parseInt($('#government_supply_' + financial_key).html());
      if (financial_supply) {
        $('#input_projection_' + financial_key).html('-1');
      }
    }
    
    function update_diverse_diet_population_bonus() {
      let bonus = !!parseInt(account.supplies.grain.amount) + !!parseInt(account.supplies.vegetables.amount) + !!parseInt(account.supplies.fruit.amount) + !!parseInt(account.supplies.livestock.amount) + !!parseInt(account.supplies.fish.amount);
      $('#diverse_diet_population_bonus').html(bonus);
    }
    function update_cash_crops_support_bonus() {
      let variety = !!parseInt(account.supplies.coffee.amount) + !!parseInt(account.supplies.tea.amount) + !!parseInt(account.supplies.alcohol.amount) + !!parseInt(account.supplies.cannabis.amount) + !!parseInt(account.supplies.tobacco.amount);
      let bonus = Math.pow(base_support_bonus, variety);
      $('#cash_crops_support_bonus').html(bonus);
    }
    function update_grouped_food_output() {
      let food = 0;
      for (let key in food_key_array) {
        let this_food = $('#output_projection_' + food_key_array[key]).html();
        food += parseInt(this_food);
      }
      $('#output_projection_' + food_key).html(food);
    }

    function update_grouped_cash_crops_output() {
      let cash_crops = 0;
      for (let key in cash_crops_key_array) {
        let this_cash_crop = $('#output_projection_' + cash_crops_key_array[key]).html();
        cash_crops += parseInt(this_cash_crop);
      }
      $('#output_projection_' + cash_crops_key).html(cash_crops);
    }

    function update_grouped_food_supply() {
      let food = 0;
      for (let key in food_slug_array) {
        let this_food = $('#government_supply_' + food_key_array[key]).html();
        if (this_food) {
          food += parseInt(this_food);
        }
      }
      $('#government_supply_' + food_key).html(food);
    }

    function update_grouped_cash_crops_supply() {
      let cash_crops = 0;
      for (let key in cash_crops_slug_array) {
        let this_cash_crop = $('#government_supply_' + cash_crops_key_array[key]).html();
        if (this_cash_crop) {
          cash_crops += parseInt(this_cash_crop);
        }
      }
      $('#government_supply_' + cash_crops_key).html(cash_crops);
    }

    function update_market_prices(market_prices) {
      for (let key in market_prices) {
        $('#sell_supply_' + market_prices[key].supply_key).html(market_prices[key].amount)
      }
    }

    function update_law_wait() {
      let last_date = new Date(account.last_law_change);
      let current_date = new Date();
      let milliseconds = Math.abs(current_date - last_date);
      let minutes = milliseconds / 1000 / 60;
      if (minutes > 60 || isNaN(NaN)) {
        $('#pass_new_laws_button_text').html('Pass New Laws');
        $('#pass_new_laws_button').removeClass('disabled');
      }
      else {
        let wait_message = '' + (60 - parseInt(minutes)) + ' Minutes';
        $('#pass_new_laws_button_text').html(wait_message);
        $('#pass_new_laws_button').addClass('disabled');
      }
    }

    function update_input_projections(input_projections) {
      $('.input_projection').html('');
      for (let key in input_projections) {
        if (input_projections[key]) {
          $('#input_projection_' + key).html('-' + input_projections[key]);
        }
      }
    }
    function update_output_projections(output_projections) {
      $('.output_projection').html('');
      for (let key in output_projections) {
        if (output_projections[key]) {
          $('#output_projection_' + key).html('+' + output_projections[key]);
        }
      }
      sum_food_output();
      sum_cash_crops_output();
    }

    function update_stats_projections() {
      cash_stats();
    }

    function cash_stats() {
      let earnings = $('#budget_earnings').html();
      earnings = earnings ? earnings : 0;
      let cash_input = $('#input_projection_' + cash_key).html();
      cash_input = cash_input ? cash_input : 0;
      let cash_output = parseInt(earnings) + Math.abs(parseInt(cash_input));
      $('#output_projection_' + cash_key).html('+' + cash_output);

      $('#sum_projection_' + cash_key).removeClass('text-danger').removeClass('text-success');
      if (earnings >= 0) {
        $('#sum_projection_' + cash_key).addClass('text-success');
        $('#sum_projection_' + cash_key).html('+' + earnings);
      }
      else {
        $('#sum_projection_' + cash_key).addClass('text-danger');
        $('#sum_projection_' + cash_key).html(earnings);
      }
    }

    function sum_food_output() {
      $('#output_projection_' + food_key).html('');
      let sum_food_output = 0;
      $(".output_projection[data-category-id='" + food_category_id + "']").each(function(){
        this_value = $(this).html();
        if (this_value) {
          sum_food_output += parseInt(this_value);
        }
      });
      if (sum_food_output) {
        $('#output_projection_' + food_key).html('+' + sum_food_output);
      }
    }

    function sum_cash_crops_output() {
      $('#output_projection_' + cash_crops_key).html('');
      let sum_cash_crops_output = 0;
      $(".output_projection[data-category-id='" + cash_crops_category_id + "']").each(function(){
        this_value = $(this).html();
        if (this_value) {
          sum_cash_crops_output += parseInt(this_value);
        }
      });
      if (sum_cash_crops_output) {
        $('#output_projection_' + cash_crops_key).html('+' + sum_cash_crops_output);
      }
    }

    function update_sum_projections() {
      $('.sum_projection').html('');
      $('.sum_projection').each(function(){
        $(this).removeClass('text-danger').removeClass('text-success');
        let id = $(this).data('id');
        input = $('#input_projection_' + id).html();
        output = $('#output_projection_' + id).html();
        if (!input && !output) {
          return;
        }
        if (input && !output) {
          sum = input;
        }
        else if (!input && output) {
          sum = output;
        }
        else {
          sum = parseInt(output) + parseInt(input);
        }
        if (sum === 0) {
          return;
        }
        if (sum > 0) {
          $(this).addClass('text-success');
        }
        else if (sum < 0) {
          $(this).addClass('text-danger');
        }
        sum = parseInt(sum);
        prefix = sum > 0 ? '+' : '';
        $(this).html(prefix + sum);
      });
    }

    function update_supplies(supplies) {
      Object.keys(supplies).forEach(function(key) {
        let supply = supplies[key];
        $('#menu_supply_' + supply['slug']).html(supply['amount']);
        $('#government_supply_' + supply['supply_key']).html(supply['amount']).removeClass('text-danger');
        if (supply['amount'] < 0) {
          $('#government_supply_' + supply['supply_key']).addClass('text-danger');
        }
        $('.our_trade_supply_current_' + supply['slug']).html(supply['amount']);
        $('#our_trade_supply_offer_' + supply['slug']).prop('max', supply['amount'] > 0 ? supply['amount'] : 0);
        let current_value = parseInt($('#our_trade_supply_offer_' + supply['slug']).val());
        let max_value = parseInt($('#our_trade_supply_offer_' + supply['slug']).prop('max'));
        $('#our_trade_supply_offer_' + supply['slug']).removeClass('input-danger');
        if (current_value > max_value || current_value < 0) {
          $('#our_trade_supply_offer_' + supply['slug']).addClass('input-danger');
        }
      });
    }

    function update_max_support() {
      if (account.ideology == free_market_key) {
        $('#menu_max_support').html(100 - account['tax_rate']);
      }
      else {
        if (account.power_structure == democracy_key) {
          $('#menu_max_support').html(democracy_socialism_max_support);
        }
        if (account.power_structure == oligarchy_key) {
          $('#menu_max_support').html(oligarchy_socialism_max_support);
        }
        if (account.power_structure == autocracy_key) {
          $('#menu_max_support').html(autocracy_socialism_max_support);
        }
      }
    }

    function update_budget(budget){
      $('#budget_gdp').html(number_format(budget.gdp));
      $('#budget_gdp_bonus_parent').hide()
      if (budget.gdp_bonus) {
        $('#budget_gdp_bonus').html(budget.gdp_bonus);
        $('#budget_gdp_bonus_parent').show();
      }
      $('#budget_socialism').html(number_format(budget.gdp));
      $('#budget_tax_income').html(number_format(budget.tax_income));
      $('#budget_power_corruption').html(number_format(budget.power_corruption));
      $('#budget_size_corruption').html(number_format(budget.size_corruption));
      $('#budget_federal').html(number_format(budget.federal));
      $('#budget_bases').html(number_format(budget.bases));
      $('#budget_education').html(number_format(budget.education));
      $('#budget_pharmaceuticals').html(number_format(budget.pharmaceuticals));
      $('#budget_earnings').html(number_format(budget.earnings));
    }
</script>