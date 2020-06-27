<!-- Chat HTML -->
<div id="chat_parent">
  <div id="chat_expand_button" class="btn btn-sm btn-default">Expand</div>
  <div id="chat_hide_button" class="btn btn-sm btn-default">Hide</div>
  <div id="chat_messages_parent">
    <div id="chat_messages_box">
      Loading...
    </div>
  </div>

  <?php if ($account) { ?>
  <div id="chat_input_parent">
    <form name="new_chat" id="new_chat" onsubmit="return chat_submit_function()">
      <input type="text" name="chat_input" class="form-control" id="chat_input" autocomplete="off" value="" placeholder="chat" />
      <!-- submit button positioned off screen -->
      <input name="submit_chat" type="submit" id="submit_chat" value="true" style="position: absolute; left: -9999px">
    </form>
  </div>
  <?php } ?>
</div>

<!-- Chat Script -->
<script>
  var last_message_id = 0;
  var at_bottom = true;

  // Detect if user is at bottom
  var text_to_bottom_css = true;
  $('#chat_messages_box').scroll(function() {
    at_bottom = false;
    if ($('#chat_messages_box').prop('scrollHeight') - $('#chat_messages_box').scrollTop() <= Math.ceil($('#chat_messages_box').height())) {
      at_bottom = true;
    }
  });

  // Chat expand
  $('#chat_expand_button').click(function(){
    if ($('#chat_messages_box').hasClass('expanded')) {
      $('#chat_expand_button').html('Expand');
      $('#chat_messages_box').removeClass('expanded');
      $("#chat_messages_box").scrollTop($("#chat_messages_box")[0].scrollHeight);
    }
    else {
      $('#chat_messages_box').addClass('expanded');
      $('#chat_expand_button').html('Shrink');
    }
  });

  // Chat hide
  $('#chat_hide_button').click(function(){
    if ($('#chat_messages_box').hasClass('hiding')) {
      $('#chat_messages_box').removeClass('hiding');
      $("#chat_messages_box").scrollTop($("#chat_messages_box")[0].scrollHeight);
      $('#chat_hide_button').html('Hide');
      $('#chat_messages_box').show();
      $('#chat_expand_button').show();
    }
    else {
      $('#chat_messages_box').addClass('hiding');
      $('#chat_hide_button').html('Show chat');
      $('#chat_messages_box').hide();
      $('#chat_expand_button').hide();
    }
  });

  //Chat Load
  function chat_load(inital_load) {
    $.ajax(
    {
        url: "<?=base_url()?>chat/load",
        type: "POST",
        data: {
          world_key: world_key,
          inital_load: inital_load,
          last_message_id: last_message_id
        },
        cache: false,
        success: function(response)
        {
          // Parse
          messages = JSON.parse(response);
          if (!messages) {
            return false;
          }

          // Loop through to create html
          html = '';
          $.each(messages, function(i, message) {
            // Skip if we already have this message, although we really shouldn't
            if (parseInt(message.id) <= parseInt(last_message_id)) {
              return true;
            }
            // Update latest message id
            last_message_id = message.id;
            let glyphicon_type = message.username ? 'glyphicon-user' : 'glyphicon-exclamation-sign';
            let diplomacy_script = '';
            let href = '';
            if (account['id'] && message.account_key != account['id']) {
              diplomacy_script = 'onclick="new_diplomacy(' + message.account_key + ');"';
              href = 'href="#"';
            }
            html += `<div class="chat_message" title="${message.timestamp} ET"><a ${href} ${diplomacy_script}><span class="glyphicon ${glyphicon_type}" style="color: ${message.color}"></span></a>`;
            // let username = message.username ? message.username + ': ' : ' - ';
            html += message.username + ': ' + message.message + '</div>';
          });
        // Append to div
        html = convert_general_url(html)
        if (inital_load) {
          $("#chat_messages_box").html('');
        }
        $("#chat_messages_box").append(html);

        // Scrool to bottom
        if (at_bottom || inital_load) {
          $("#chat_messages_box").scrollTop($("#chat_messages_box")[0].scrollHeight);
        }
        }
    });
  }
  chat_load(true);

  // Chat Loop
  chat_interval = 5 * 1000;
  if (document.location.hostname == "localhost") {
    chat_interval = 10 * 1000;
  }
  setInterval(chat_load, chat_interval);

  // Called by form
  function chat_submit_function(e) {
    // Chat input
    var chat_input = $("#chat_input").val();
    $.ajax(
    {
        url: "<?=base_url()?>chat/new_chat",
        type: "POST",
        data: { 
          chat_input: chat_input,
          world_key: world_key
        },
        cache: false,
        success: function(html)
        {
          if (html.trim()) {
            swal('', html, 'warning');
          }
        }
    });

    $('#chat_input').val('');
    // Load log so user can instantly see his message
    chat_load();
    // Focus back on input
    $('#chat_input').focus();
    return false;
  }

  // http://stackoverflow.com/a/3890175/3774582
  function convert_general_url(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" class="message_link message_content" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" class="message_link message_content" target="_blank">$2</a>');

    return replacedText;
}

</script>