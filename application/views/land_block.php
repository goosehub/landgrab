<div id="land_block" class="center_block">
    <form id="land_form" action="<?=base_url()?>land_form" method="post">

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <div id="land_form_result">
            <br><div class="alert alert-wide alert-error"><strong id="land_form_result_message"></strong></div>
        </div>

        <input type="hidden" id="input_world_key" name="world_key_input" value="<?php echo $world['id']; ?>">
        <input type="hidden" id="input_id" name="id_input" value="">
        <input type="hidden" id="input_coord_slug" name="coord_slug_input" value="">

        <div class="coord_label pull-right">Coordinates: <a id="coord_link" href=""></a></div>

        <div id="join_to_play_button" class="land_form_subparent">
            <a class="register_to_play btn btn-default" href="http://landgrab.xyz/world/1?register">Join to Play!</a>
        </div>

        <div id="land_form_unclaimed_parent" class="land_form_subparent">
            <div><strong>Unclaimed Land</strong></div>
            <button type="button" id="land_form_claim" class="submit_land_form btn btn-success">Claim This Land</button>
        </div>

        <div id="land_form_info_parent" class="land_form_subparent">
            <div id="land_name_label"></div>
            <div id="land_content_label"></div>
            <div id="leader_name">Led by <strong id="leader_name_label"></strong></div>

            <div id="capitol_info">
                <div id="capitol_label">Capitol of the <strong id="government_label"></strong> of <strong id="nation_label"></strong></div>
            </div>

            <div><span id="land_type_label"></span> with a population <span id="land_population_label"></span><small>,000</small></div>
            <div>GDP: $<span id="land_gdp_label"></span><small>,000</small></div>
            <div>Defense: <span id="land_defense_label"></span></div>
            <button type="button" id="land_form_attack" class="submit_land_form btn btn-success">Attack this land</button>
        </div>

        <div id="land_form_update_parent" class="land_form_subparent">
            <hr>
            <input type="text" class="form-control" id="input_land_name" name="land_name" placeholder="Land Name" value="">
            <textarea class="form-control" id="input_content" name="content" placeholder="Description"></textarea>
            <br>
            <button type="button" id="land_form_update" class="submit_land_form btn btn-success">Update Land</button>
        </div>

    </form>
</div>