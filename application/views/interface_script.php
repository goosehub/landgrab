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

// Show how to play after registering
<?php if ($just_registered) { ?>
$('#how_to_play_block').show();
<?php } ?>

// Show register form if not logged in and not failed to log in
<?php if ($failed_form != 'login') { ?>
  if (!log_check) {
    // $('#register_block').show();
  }
<?php } ?>

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

// 
// Center block hide and show logic
// 

$('#stat_dropdown').click(function(){
    $('#management_block').show();
});
$('.user_button').click(function(){
    $('#account_update_block').show();
});
$('.exit_center_block').click(function(){
  $('.center_block').hide();
});
$('.login_button').click(function(){
    $('.center_block').hide();
    $('#login_block').show();
});
$('.register_button').click(function(){
    $('.center_block').hide();
    $('#register_block').show();
});
$('.how_to_play_button').click(function(){
    $('.center_block').hide();
    $('#how_to_play_block').show();
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
$('.leaderboard_land_owned_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_land_owned_block').show();
});
$('.leaderboard_cities_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_cities_block').show();
});
$('.leaderboard_strongholds_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_strongholds_block').show();
});
$('.leaderboard_army_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_army_block').show();
});
$('.leaderboard_population_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_population_block').show();
});

</script>