<div id="land_block" class="center_block">
    <form id="land_form" action="<?=base_url()?>land_form" method="post">

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <div id="land_form_result">
            <br><div class="alert alert-wide alert-error"><strong id="land_form_result_message"></strong></div>
        </div>

        <input type="hidden" id="input_world_key" name="world_key_input" value="<?php echo $world['id']; ?>">
        <input type="text" id="input_id" name="id_input" value="">
        <input type="hidden" id="input_coord_slug" name="coord_slug_input" value="">
        <input type="hidden" id="form_type_input" name="form_type_input" value="">

        <div class="coord_label pull-right">Coordinates: <a id="coord_link" href=""></a></div>

        <div id="land_form_unclaimed_parent" class="land_form_subparent">
            <div><strong>Unclaimed Land</strong></div>
            <br>
            <button type="button" id="land_form_submit_claim" value="claim" class="submit_land_form btn btn-success">Claim This Land</button>
        </div>

        <div id="land_form_info_parent" class="land_form_subparent">
            <strong id="land_name_label"></strong>
            <div id="land_content_label"></div>
            <div id="leader_name">Led by <strong id="leader_name_label"></strong></div>

            <div id="capitol_info">
                <div id="capitol_label">Capitol of the <span id="government_label" class="text-primary"></span> of <span id="nation_label" class="text-primary"></span></div>
            </div>

            <div><span id="land_type_label" class="text-primary"></span> with a population <span id="land_population_label"></span><small>,000</small></div>
            <div>GDP: $<span id="land_gdp_label"></span><small>,000</small></div>
            <div>Defense: <span id="land_defense_label"></span></div>
            <button type="button" id="land_form_submit_attack" value="attack" class="submit_land_form btn btn-success">Attack this land</button>
        </div>

        <div id="land_form_update_parent" class="land_form_subparent">
            <br>
            <input type="text" class="form-control" id="input_land_name" name="land_name" placeholder="Land Name" value="">
            <textarea class="form-control" id="input_content" name="content" placeholder="Description"></textarea>
            <br>
            <button type="button" id="land_form_submit_update" value="update" class="submit_land_form btn btn-success">Update This Land</button>
        </div>

        <br>

        <div id="land_form_upgrade_parent" class="land_form_subparent">
            <button class="expand_land_form btn btn-primary" type="button" data-toggle="collapse" data-target="#upgrade_dropdown" aria-expanded="false" aria-controls="upgrade_dropdown">
                Build on this Land
                <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
            </button>
            <div id="upgrade_dropdown" class="collapse">
                <div class="form-group">
                    <div class="row">
                        <?php foreach ($modify_effect_dictionary as $effect) { ?>
                        <div class="col-md-6">
                            <button type="button" id="land_form_submit_upgrade" class="upgrade_submit submit_land_form btn btn-success" value="<?php echo $effect['id']; ?>">
                                <?php echo ucwords(str_replace('_', ' ', $effect['name'])); ?>
                            </button>
                            <div class="expand_land_type_info btn btn-info" type="button" data-toggle="collapse" data-target="#<?php echo $effect['name']; ?>_info_dropdown" aria-expanded="false" aria-controls="<?php echo $effect['name']; ?>_info_dropdown">
                                Info 
                                <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
                            </div>
                            <div id="<?php echo $effect['name']; ?>_info_dropdown" class="info_details_parent collapse">
                                Population: <span class="pull-right"><strong class="text-primary"><?php echo $effect['population']; ?></strong></span><br>
                                GDP: <span class="pull-right"><strong class="text-primary"><?php echo $effect['gdp']; ?></strong></span><br>
                                Treasury: <span class="pull-right"><strong class="text-primary"><?php echo $effect['treasury']; ?></strong></span><br>
                                Defense: <span class="pull-right"><strong class="text-primary"><?php echo $effect['defense']; ?></strong></span><br>
                                Military: <span class="pull-right"><strong class="text-primary"><?php echo $effect['military']; ?></strong></span><br>
                                Support: <span class="pull-right"><strong class="text-primary"><?php echo $effect['support']; ?></strong></span><br>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="join_to_play_button" class="land_form_subparent">
            <a class="register_to_play btn btn-default" href="<?=base_url()?>world/1?register">Join to Play!</a>
        </div>

    </form>

</div>