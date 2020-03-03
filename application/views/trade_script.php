<script>
$('#diplomacy_block').on('click', '.open_trade_request', function(e){
    console.log('marco');
    console.log(e);
    console.log($(this).attr('trade-id'));
    $('.center_block').hide();
    $('#trade_block').show();
});
</script>