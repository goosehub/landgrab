<!-- Interface Script -->
<script>
// 
// Loading Overlay
// 

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
// Show register form if not logged in and not failed to log in
<?php if ($failed_form != 'login') { ?>
  if (!log_check) {
    $('#ab_test').val('default_register_block');
    $('#register_block').show();
  }
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

// Show register if server passes register to url
if (window.location.href.indexOf('register') >= 0) {
    $('#register_block').show();
}

// Validation State of the State screen
$('#pass_new_laws_button').click(function(){
    if ( parseInt($('#military_budget').val()) + parseInt($('#entitlements_budget').val()) > 100) {
        alert('You\'re military budget plus your entitlements budget can not exceed 100%');
        return false;
    }
});

// 
// Center block hide and show logic
// 

$('.exit_center_block').click(function(){
  $('.center_block').hide();
});
$('.stat_dropdown').click(function(){
    $('.center_block').hide();
    $('#law_block').fadeIn();
});
$('.user_button').click(function(){
    $('.center_block').hide();
    $('#account_update_block').fadeIn();
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
$('#leaderboard_dropdown').click(function(){
    $('.center_block').hide();
    $('#leaderboard_block').show();
});
$('#worlds_dropdown').click(function(){
    $('.center_block').hide();
});
$('#site_dropdown').click(function(){
    $('.center_block').hide();
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