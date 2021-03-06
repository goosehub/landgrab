<!-- Interface Script -->
<script>
loading();
render_law_preview();
ab_coin_toss();
handle_trade_tab_switch();
handle_law_ui();
handle_center_block_show_and_hide();
handle_open_create_world();
handle_create_new_world_button();
update_cycle_countdown();

// Removed in mapInit callback in map_script
function loading() {
    var over = `<div id="overlay"><p>
        <span class="glyphicon glyphicon-refresh spinning"></span>
    </p></div>`;
    $(over).appendTo('body');
};

function ab_coin_toss() {
    coin = (Math.floor(Math.random() * 2) == 0);
    if (coin) {
        // $('#ab_test').val('default_register_block');
    } else {
        // $('#ab_test').val('default_no_register_block');
    }
}

// Not reliable across browsers and situations, not used
function toggle_fullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    }
    else if (document.exitFullscreen) {
        document.exitFullscreen(); 
    }
}

function update_cycle_countdown() {
    $('.cycle_countdown').html(minutes_til_next_cycle())
    setInterval(function(){
        $('.cycle_countdown').html(minutes_til_next_cycle())
    }, 10 * 1000);
}

function minutes_til_next_cycle() {
    let d = new Date();
    let current_minute = d.getMinutes();
    if (current_minute < cycle_minutes * 1) {
        return (cycle_minutes * 1) - current_minute;
    }
    if (current_minute < cycle_minutes * 2) {
        return (cycle_minutes * 2) - current_minute;
    }
    if (current_minute < cycle_minutes * 3) {
        return (cycle_minutes * 3) - current_minute;
    }
    if (current_minute < cycle_minutes * 4) {
        return (cycle_minutes * 4) - current_minute;
    }
    if (current_minute < cycle_minutes * 5) {
        return (cycle_minutes * 5) - current_minute;
    }
    if (current_minute < cycle_minutes * 6) {
        return (cycle_minutes * 6) - current_minute;
    }
}

function next_cycle_time() {
    let next = new Date();
    next.setTime(next.getTime() + cycle_minutes * 60 * 1000);
    next.setMinutes(cycle_minutes * Math.floor(next.getMinutes() / cycle_minutes));
    next.setSeconds(0);
    next = moment(next).format('h:mm A')
    return next;
}

function handle_create_new_world_button() {
    $('#create_new_world_button').click(function(){
        create_new_world();
    });
}

function create_new_world() {
    let is_private = $('input[name="input_world_is_private"]:checked').val();
    let world_name = $('#world_name').val();
    let data = {
      world_key: world_key,
      world_name: world_name,
      is_private: is_private,
    };
    ajax_post('game/create_world', data, function(response) {
        window.location.replace(base_url + 'world/' + response.world_key);
    });
}

function handle_open_create_world() {
    $('#open_create_world').click(function(){
        $('.center_block').hide();
        $('#create_world_block').show();
    });
}

function handle_trade_tab_switch() {
    // Tabs
    $('#trade_requests_received a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
    $('#trade_requests_sent a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
    $('#current_treaties a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
}

function handle_law_ui() {
    $(document).ready(function(){
        $('#input_tax_rate').change(function() {
            render_law_preview();
        });
        $('#input_power_structure').change(function() {
            render_law_preview();
        });
        $('input[name=input_ideology]').click(function() {
            render_law_preview();
        });
    });
    $('input[type=range]').on('input', function () {
        $(this).trigger('change');
    });
}

function default_open_block() {
    <?php if (isset($_GET['show_government'])) { ?>
        open_government_block();
    <?php } ?>
    // Validation errors shown on page load if exist
    <?php if ($failed_form === 'login') { ?>
        $('#login_block').show();
    <?php } else if ($failed_form === 'register') { ?> 
        $('#register_block').show();
    <?php } ?>

    // Error reporting
    <?php if ($failed_form === 'error_block') { ?>
        $('#error_block').show();
    <?php } ?>
    <?php if ($failed_form === 'register') { ?>
        $('#register_block').show();
    <?php } ?>
    <?php if ($failed_form === 'login' && $account) { ?>
      // Show login form if not logged in and not failed to log in
      $('#login_block').show();
    <?php } else if (isset($_GET['login'])) { ?>
      // Show login form if URL calls for it
      $('#login_block').show();
    <?php } else if (!$account) { ?>
      // Show register block rest of the time
      // $('#register_block').show();
    <?php } ?>
}

function render_law_preview() {
    power_structure = $('#input_power_structure').val();
    tax_rate = $('#input_tax_rate').val();
    ideology = $('input[name=input_ideology]:checked').val();
    $('#display_input_tax_rate').html(tax_rate);
    // Convert tax rate to hex, multiply, and use for red value 
    $('#display_input_tax_rate').css('color', '#' + (tax_rate * 4).toString(16) + '0000');

    set_tax_rate_disabled();
    set_max_support();
    set_support_per_minute();
    set_corruption();
}

function set_tax_rate_disabled() {
    if (ideology == socialism_key) {
        $('#input_tax_rate').prop('disabled', true);
        $('#display_input_tax_rate').html('100');
    }
    else {
        $('#input_tax_rate').removeAttr('disabled');
        $('#display_input_tax_rate').html(tax_rate);
    }
}

function set_max_support() {
    if (ideology == socialism_key) {
        if (power_structure == democracy_key) {
            $('#projected_max_support').html(democracy_socialism_max_support);
        }
        else if (power_structure == oligarchy_key) {
            $('#projected_max_support').html(oligarchy_socialism_max_support);
        }
        else if (power_structure == autocracy_key) {
            $('#projected_max_support').html(autocracy_socialism_max_support);
        }
    }
    else {
        $('#projected_max_support').html(100 - tax_rate);
    }
}

function set_support_per_minute() {
    if (power_structure == democracy_key) {
        $('#projected_support_per_minute').html(democracy_support_regen);
    }
    else if (power_structure == oligarchy_key) {
        $('#projected_support_per_minute').html(oligarchy_support_regen);
    }
    else if (power_structure == autocracy_key) {
        $('#projected_support_per_minute').html(autocracy_support_regen);
    }
}

function set_corruption() {
    if (ideology == socialism_key) {
        $('#projected_corruption').html('100');
    }
    else {
        $('#projected_corruption').html(power_structure * 10);
    }
}

function open_government_block() {
    $('#input_power_structure').val(account.power_structure).trigger('change');
    $('#input_tax_rate').val(account.tax_rate).trigger('change');
    if (account.ideology == 1) {
        $('#free_market').prop('checked', account.ideology);
    }
    else {
        $('#socialism').prop('checked', account.ideology);
    }
    unhighlight_all_squares();
    $('.center_block').hide();
    $('#government_block').fadeIn();
}

function handle_center_block_show_and_hide() {
    $('.exit_center_block').click(function(){
      $('.center_block').hide();
    });
    $('.government_dropdown').click(function(){
        open_government_block();
    });
    $('.diplomacy_dropdown').click(function(){
        unhighlight_all_squares();
        mark_war_declare_as_seen();
        $('.center_block').hide();
        $('#diplomacy_block').fadeIn();
    });
    $('.open_terms_block').click(function(){
        $('.center_block').hide();
        $('#terms_block').fadeIn();
    });
    $('.user_button').click(function(){
        $('.center_block').hide();
        $('#account_update_block').fadeIn();
        $('#account_update_form').data('first-claim', false);
        $('#update_nation_button').html('Update National Charter');
        $('#cash_crop_key_parent').hide();
    });
    $('.login_button').click(function(){
        $('.center_block').hide();
        $('#login_block').show();
    });
    $('body').on('click', '.register_button', function(){
        $('.center_block').hide();
        $('#register_block').show();
        $('#register_input_username').focus();
    });
    $('.update_info_button').click(function(){
        $('.center_block').hide();
        $('#update_info_block').show();
    });
    $('.about_button').click(function(){
        $('.center_block').hide();
        $('#about_block').show();
    });
    $('.faq_button').click(function(){
        $('.center_block').hide();
        $('#faq_block').show();
    });
    $('.report_bugs_button').click(function(){
        $('.center_block').hide();
        $('#report_bugs_block').show();
    });
    $('.login_button').click(function(){
        $('#login_input_username').focus();
    });
    $('#worlds_dropdown').click(function(){
        $('.center_block').hide();
    });
    $('#site_dropdown').click(function(){
        $('.center_block').hide();
    });
    $('.update_password_button').click(function(){
        $('.center_block').hide();
        $('#update_password_block').show();
    });
}

</script>