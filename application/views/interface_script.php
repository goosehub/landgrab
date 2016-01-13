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
    $('#register_block').show();
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

// Stop dropdown closing when clicking color input
$('#account_input_primary_color').click(function(e) {
    e.stopPropagation();
});

// 
// Center block hide and show logic
// 

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
$('.about_button').click(function(){
    $('.center_block').hide();
    $('#about_block').show();
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
$('.market_order_button').click(function(){
  $('.center_block').hide();
  $('#market_order_block').show();
});
$('#leaderboard_net_value_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_net_value_block').show();
});
$('#leaderboard_land_owned_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_land_owned_block').show();
});
$('#leaderboard_cash_owned_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_cash_owned_block').show();
});
$('#leaderboard_highest_valued_land_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_highest_valued_land_block').show();
});
$('.leaderboard_cheapest_land_button').click(function(){
    $('.center_block').hide();
    $('#leaderboard_cheapest_land_block').show();
});
// hide button and reset number to 0
$('.sold_lands_button').click(function(){
  $('#recently_sold_alert').hide();
  $('#sales_since_last_update_number').html('0');
  $('.center_block').hide();
  $('#sales_since_last_update_block').show();
});

// Show recently sold alert if exists on page load
<?php if ($log_check && $sales['sales_since_last_update']) { ?>
$('.recently_sold_alert').show();
<?php } ?>

// 
// Preset Logic
// 

function set_market_order_preset(latmin, latmax, lngmin, lngmax) {
  $('#min_lat_input').val(latmin);
  $('#max_lat_input').val(latmax);
  $('#min_lng_input').val(lngmin);
  $('#max_lng_input').val(lngmax);
}

$('#north_america_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#south_america_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#europe_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#africa_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#russia_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#asia_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#middle_east_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});
$('#australia_preset').click(function(e){
  set_market_order_preset($(this).attr('latmin'), $(this).attr('latmax'), $(this).attr('lngmin'), $(this).attr('lngmax'));
});

</script>