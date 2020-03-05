<script>
    $('#do_first_claim').click(function(){
        do_first_claim(function(){
            get_map_update();
        });
    });

    function do_first_claim(callback) {
        $.ajax({
            url: "<?=base_url()?>game/do_first_claim",
            type: "POST",
            data: {
                tile: current_tile,
            },
            cache: false,
            success: function(data) {
                callback(data);
            }
        });
    }

</script>