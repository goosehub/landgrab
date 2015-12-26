

    // 
    // Attempts to run javascript from iframe click
    // 

    // Striaghtforward | Failed
    $('#submit_land_form').on('click', function(){
        $.ajax({
            type: 'POST',
            url: '<?=base_url()?>land_form',
            data: $('#land_form').serialize(), 
            cache: false,
            success: function(response) {
                alert('Submitted comment'); 
            },
            error: function() {
                alert('There was an error submitting comment');
            }
         });
    });

    // Permmission denied
    var iframe = $('iframe').contents();
    iframe.find("#submit_land_form").on('click', function(){
       alert();
    });

    // Permission denied
    $('#google_maps_iframe').load(function(){
            var iframe = $('#google_maps_iframe').contents();
            iframe.find('#submit_land_form').on('click', function(){
                   alert();
            });
    });

    // Failed | With onclick=""
    function submit_land_form()
    {
        $.ajax({
            type: 'POST',
            url: '<?=base_url()?>land_form',
            data: $('#land_form').serialize(), 
            cache: false,
            success: function(response) {
                alert('Submitted comment'); 
            },
            error: function() {
                alert('There was an error submitting comment');
            }
         });
    }

    // adding javascript to infowindow causes unterminated string

    // Form with action works