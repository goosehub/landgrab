<!-- Interface Script -->
<script>
// Removed in mapInit callback in map_script
loading = function() {
    var over = '<div id="overlay"><p>Loading...</p></div>';
    $(over).appendTo('body');
};
loading();

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
<?php if (isset($_GET['after_first_claim'])) { ?>
    open_government_block();
<?php } ?>

// AB testing
coin = (Math.floor(Math.random() * 2) == 0);
if (coin) {
    // $('#ab_test').val('default_register_block');
} else {
    // $('#ab_test').val('default_no_register_block');
}

// Validation errors shown on page load if exist
<?php if ($failed_form === 'login') { ?>
$('#login_block').show();
<?php } else if ($failed_form === 'register') { ?> 
$('#register_block').show();
<?php } ?>

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

// 
// Laws
// 

render_law_preview();
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

// 
// Center block hide and show logic
// 

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
$('.register_button').click(function(){
    $('.center_block').hide();
    $('#register_block').show();
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
$('.register_button').click(function(){
    $('#register_input_username').focus();
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

// jQuery DataTables
$('.jquery-datatable').dataTable({
    // No paging of data
    paging: false,
    "order": [],
    // Hide Search
    bFilter: false,
    bInfo: false,
    // Sort by desc on click by default
    "aoColumns": [
        { "asSorting": [ "desc", "asc" ] },
        { "asSorting": [ "desc", "asc" ] },
        { "asSorting": [ "desc", "asc" ] },
        { "asSorting": [ "desc", "asc" ] },
        { "asSorting": [ "desc", "asc" ] },
        { "asSorting": [ "desc", "asc" ] },
        { "asSorting": [ "desc", "asc" ] },
        { "asSorting": [ "desc", "asc" ] },
    ],
    responsive: true,
    // Do not interpret numeric commas as decimals for sorting
    // "aoColumnDefs": [
    //     { "sType": "numeric-comma", "aTargets": [2,3] },
    //     { "sType": "numeric-comma", "aTargets": [2,3] },
    //     { "sType": "numeric-comma", "aTargets": [2,3] },
    //     { "sType": "numeric-comma", "aTargets": [2,3] },
    //     { "sType": "numeric-comma", "aTargets": [2,3] },
    //     { "sType": "numeric-comma", "aTargets": [2,3] },
    // ]
    // columnDefs: [
        // { type: 'numeric-comma', targets: 0 }
    // ]
});

</script>