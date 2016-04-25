<!-- Chat HTML -->
<div id="chat_parent">
  <div id="chat_messages_parent">
    <div id="chat_messages_box">
      Loading...
    </div>
  </div>

  <?php if ($log_check) { ?>
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

  var world_key = <?php echo $world['id']; ?>;

  //Chat Load
  function chat_load() {
    $.ajax(
    {
        url: "<?=base_url()?>chat/load",
        type: "POST",
        data: { world_key: world_key },
        cache: false,
        success: function(html)
        {
            console.log(html);
            $("#chat_messages_box").html(html);
            $("#chat_messages_box").scrollTop($("#chat_messages_box")[0].scrollHeight);
        }
    });
  }
  chat_load();

  // Chat Loop
  setInterval(chat_load, 3000);

  // Called by form
  function chat_submit_function(e) {
    // Chat input
    var chat_input = $("#chat_input").val();
    $.post("<?=base_url()?>chat/new_chat",
    {
      chat_input: chat_input,
      world_key: world_key
    });
    $('#chat_input').val('');
    // Load log so user can instantly see his message
    chat_load();
    // Focus back on input
    $('#chat_input').focus();
    return false;
  }
</script>