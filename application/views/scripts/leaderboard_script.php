<script>
    handle_open_leaderboard();

    function handle_open_leaderboard() {
        $('#leaderboard_dropdown').click(function(){
            $('.center_block').hide();
            $('#leaderboard_block').show();
            get_leaderboard();
        });
    }

    function get_leaderboard() {
        ajax_get('game/leaderboard/' + world_key + '/' + current_leaderboard_supply, function(response) {
            render_leaderboard(response);
        });
    }
    function render_leaderboard(leaders) {
        console.log('marco');
        console.log(leaders);
    }
</script>