<div id="tile_block" class="center_block">
    <form id="tile_form" action="<?=base_url()?>tile_form" method="post">

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <div id="tile_form_result" class="tile_block_toggle">
            <br><div class="alert alert-wide alert-error"><strong id="tile_form_result_message"></strong></div>
        </div>

        <div class="coord_label pull-right">Coordinates: <a id="coord_link" href=""></a></div>
    </form>
</div>