<div id="land_block" class="center_block">
    <form id="land_form" action="<?=base_url()?>land_form" method="post">

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <div id="land_form_result" class="land_block_toggle">
            <br><div class="alert alert-wide alert-error"><strong id="land_form_result_message"></strong></div>
        </div>

        <input type="hidden" id="input_world_key" name="world_key_input" value="<?php echo $world['id']; ?>">
        <input type="hidden" id="input_id" name="id_input" value="">
        <input type="hidden" id="input_coord_slug" name="coord_slug_input" value="">
        <input type="hidden" id="form_type_input" name="form_type_input" value="">

        <div class="coord_label pull-right">Coordinates: <a id="coord_link" href=""></a></div>

        <div id="land_form_unclaimed_parent" class="land_block_toggle">
            <div><strong>Unclaimed Land</strong></div>
            <br>
            <button type="button" id="land_form_submit_claim" value="claim" class="submit_land_form btn btn-action land_block_toggle">
                Claim <span class="weariness_outer_span">(+<span class="weariness_attack_span" class="text-danger">0</span> weariness)</span>
            </button>
            <button type="button" id="land_form_submit_claim_tutorial" value="claim" class="submit_land_form btn btn-action land_block_toggle">
                Build your Capitol Here
            </button>
        </div>

        <span id="land_form_info_parent" class="land_block_toggle">
            <strong id="land_name_label"></strong>
            <div id="land_content_label"></div>
            <div id="username" class="text-success">Led by <span id="username_label"></span></div>

            <div id="capitol_info" class="land_block_toggle">
                <a class="land_leader_portrait_image_link" href="<?=base_url()?>uploads/default_leader_portrait.png" target="_blank">
                    <img class="land_leader_portrait_image" src="<?=base_url()?>uploads/default_leader_portrait.png"/>
                </a>
                <a class="land_nation_flag_image_link" href="<?=base_url()?>uploads/default_leader_portrait.png" target="_blank">
                    <img class="land_nation_flag_image" src="<?=base_url()?>uploads/default_nation_flag.png"/>
                </a>
                <div id="capitol_label"><span class="text-red">Capitol</span> of the <span id="government_label" class="text-warning"></span> of <span id="nation_label" class="text-warning"></span></div>
            </div>

            <div><span id="land_type_label" class="text-primary"></span></div>
            <div class="text-info">Population of <span id="land_population_label"></span><small>K</small></div>
            <div class="text-action">GDP: $<span id="land_gdp_label"></span><small>M</small></div>
            <button type="button" id="land_form_submit_attack" value="attack" class="submit_land_form btn btn-action land_block_toggle">
                Attack  <span class="weariness_outer_span">(+<span class="weariness_attack_span" class="text-danger">0</span> weariness)</span>
            </button>
            <button type="button" id="land_form_submit_attack_tutorial" value="attack" class="submit_land_form btn btn-action land_block_toggle">
                Build your Capitol Here
            </button>
        </span>

        <button id="land_form_support_too_low" class="submit_land_form btn btn-danger land_block_toggle disabled">
            Support too low to attack
        </button>

        <div id="not_in_range" class="land_block_toggle">
            <p class="text-warning">Land not in range</p>
        </div>

        <span id="land_form_update_parent" class="land_block_toggle">
            <br>
            <input type="text" class="form-control" id="input_land_name" name="land_name" placeholder="Land Name" value="">
            <textarea class="form-control" id="input_content" name="content" placeholder="Description"></textarea>
            <br>
            <button type="button" id="land_form_submit_update" value="update" class="submit_land_form btn btn-action land_block_toggle">Update</button>
        </span>

        <button id="button_expand_info" class="expand_land_form btn btn-primary land_block_toggle" type="button" data-toggle="collapse" data-target="#land_info_dropdown" aria-expanded="false" aria-controls="land_info_dropdown">
            Buildings
            <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
        </button>

        <div id="embassy_list_dropdown_button" class="btn btn-info" type="button" data-toggle="collapse" data-target="#embassy_list_dropdown" aria-expanded="false" aria-controls="embassy_list_dropdown">
            Embassies
            <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
        </div>

        <div id="sanctions_list_dropdown_button" class="btn btn-info" type="button" data-toggle="collapse" data-target="#sanctions_list_dropdown" aria-expanded="false" aria-controls="sanctions_list_dropdown">
            Sanctions
            <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
        </div>

        <button id="button_expand_upgrade" class="expand_land_form btn btn-success land_block_toggle" type="button" data-toggle="collapse" data-target="#upgrade_dropdown" aria-expanded="false" aria-controls="upgrade_dropdown">
            Build
            <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
        </button>

        <br> <br>

        <div id="embassy_list_dropdown" class="info_details_parent collapse">
            <div id="embassy_list" class="well">
            </div>
        </div>

        <div id="sanctions_list_dropdown" class="info_details_parent collapse">
            <div id="sanctions_list" class="well">
            </div>
        </div>

        <div id="land_form_more_info_parent" class="land_block_toggle">
            <div id="land_info_dropdown" class="collapse">
                <div id="land_info_div" class="well">
                </div>
            </div>
        </div>

        <div id="need_previous_lands">
        </div>

        <div id="land_form_upgrade_parent" class="land_block_toggle">
            <p id="land_form_low_treasury" class="land_block_toggle text-warning">Revenue too low to build</p>
            <p id="lands_needed_for_upgrade" class="land_block_toggle"></p>
            <div id="upgrade_dropdown" class="collapse">
                <div class="form-group">
                    <div class="row">
                    <?php foreach ($modify_effect_dictionary as $effect) { ?>
                        <?php if ($effect['is_embassy']) { continue; } ?>
                        <?php if ($effect['is_sanctions']) { continue; } ?>
                        <?php $button_color = $effect['id'] <= 10 ? 'action' : 'success' ?>
                        <div id="<?php echo $effect['name']; ?>_info_parent" class="effect_info_item col-md-6 land_block_toggle">
                            <button type="button" class="land_form_submit_upgrade upgrade_submit submit_land_form btn btn-<?php echo $button_color; ?>" value="<?php echo $effect['id']; ?>">
                                <?php echo ucwords(str_replace('_', ' ', $effect['name'])); ?>
                            </button>
                            <div class="expand_land_type_info btn btn-info" type="button" data-toggle="collapse" data-target="#<?php echo $effect['name']; ?>_info_dropdown" aria-expanded="false" aria-controls="<?php echo $effect['name']; ?>_info_dropdown">
                                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                            </div>
                            <div id="<?php echo $effect['name']; ?>_info_dropdown" class="info_details_parent collapse">
                                <?php if ($effect['treasury'] > 0) {
                                    $this_class = 'success';
                                } else if ($effect['treasury'] < 0) {
                                    $this_class = 'danger';
                                } else {
                                    $this_class = 'primary';
                                } ?>
                                <div class="effect_pair">
                                    <span class="effect_label text-danger">Revenue: </span>
                                    <span class="effect_value pull-right text-<?php echo $this_class; ?>">
                                        <?php echo $effect['treasury']; ?>M
                                    </span>
                                </div>
                                <?php if ($effect['gdp'] > 0) {
                                    $this_class = 'success';
                                } else if ($effect['gdp'] < 0) {
                                    $this_class = 'danger';
                                } else {
                                    $this_class = 'primary';
                                } ?>
                                <div class="effect_pair">
                                    <span class="effect_label text-action">GDP: </span>
                                    <span class="effect_value pull-right text-<?php echo $this_class; ?>">
                                        <?php echo $effect['gdp']; ?>M
                                    </span>
                                </div>
                                <?php if ($effect['population'] > 0) {
                                    $this_class = 'success';
                                } else if ($effect['population'] < 0) {
                                    $this_class = 'danger';
                                } else {
                                    $this_class = 'primary';
                                } ?>
                                <div class="effect_pair">
                                    <span class="effect_label text-info">Population: </span>
                                    <span class="effect_value pull-right text-<?php echo $this_class; ?>">
                                        <?php echo $effect['population']; ?>K
                                    </span>
                                </div>
                                <?php if ($effect['culture'] > 0) {
                                    $this_class = 'success';
                                } else if ($effect['culture'] < 0) {
                                    $this_class = 'danger';
                                } else {
                                    $this_class = 'primary';
                                } ?>
                                <div class="effect_pair">
                                    <span class="effect_label text-purple">Culture: </span>
                                    <span class="effect_value pull-right text-<?php echo $this_class; ?>">
                                        <?php echo $effect['culture']; ?>
                                    </span>
                                </div>
                                <?php if ($effect['military'] > 0) {
                                $this_class = 'success';
                                } else if ($effect['military'] < 0) {
                                $this_class = 'danger';
                                } else {
                                $this_class = 'primary';
                                } ?>
                                <div class="effect_pair">
                                    <span class="effect_label text-warning">Military: </span>
                                    <span class="effect_value pull-right text-<?php echo $this_class; ?>">
                                        <?php echo $effect['military']; ?>M
                                    </span>
                                </div>
                                <div class="effect_pair">
                                    <span class="effect_label text-text-default">Defense Bonus: </span>
                                    <span class="effect_value pull-right text-action">
                                        <?php echo $effect['defense']; ?>X
                                    </span>
                                </div>
                                <?php if ($effect['support'] > 0) {
                                    $this_class = 'success';
                                } else if ($effect['support'] < 0) {
                                    $this_class = 'danger';
                                } else {
                                    $this_class = 'primary';
                                } ?>
                                <div class="effect_pair">
                                    <span class="effect_label text-default">Support: </span>
                                    <span class="effect_value pull-right text-<?php echo $this_class; ?>">
                                        <?php echo $effect['support']; ?>
                                    </span>
                                </div> 
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="embassy_parent">

            <button type="button" value="build_embassy" id="build_embassy" class="btn btn-success">Build Embassy</button>
            <button type="button" value="remove_embassy" id="remove_embassy" class="btn btn-danger">Remove Embassy</button>
            <div id="embassy_info_dropdown_button" class="btn btn-info" type="button" data-toggle="collapse" data-target="#embassy_info_dropdown" aria-expanded="false" aria-controls="embassy_info_dropdown">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            </div>
            <div id="embassy_info_dropdown" class="info_details_parent collapse">
                <div class="well">
                    <p class="">
                        You can build an Embassy on another nations Capitol to altruistically (or strategically) assist another nation. An Embassy boosts this nations Population by <?php echo $embassy_effect['population'] ?>K, Culture by <?php echo $embassy_effect['culture'] ?>, GDP by $<?php echo $embassy_effect['gdp'] ?>M, Military by $<?php echo $embassy_effect['military'] ?>M, and Support by <?php echo $embassy_effect['support'] ?>.
                    </p>
                </div>
            </div>
        </div> 

        <div class="sanctions_parent">

            <button type="button" value="build_sanctions" id="build_sanctions" class="btn btn-danger">Impose Sanctions</button>
            <button type="button" value="remove_sanctions" id="remove_sanctions" class="btn btn-success">Remove Sanctions</button>
            <div id="sanctions_info_dropdown_button" class="btn btn-info" type="button" data-toggle="collapse" data-target="#sanctions_info_dropdown" aria-expanded="false" aria-controls="sanctions_info_dropdown">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            </div>
            <div id="sanctions_info_dropdown" class="info_details_parent collapse">
                <div class="well">
                    <p class="">
                        You can impose sanctions on your enemies to hamper their economy. Sanctions will decrease a nations GDP by <?php echo abs($sanctions_effect['culture']) ?> and reduce their support by <?php echo abs($sanctions_effect['support']) ?>. But the consequence of imposing sanctions is that is will cause <?php echo $weariness_from_building_sanctions; ?> weariness to yourself.
                    </p>
                </div>
            </div>
        </div> 

        <div id="join_to_play_button" class="land_block_toggle">
            <a class="register_to_play btn btn-default" href="<?=base_url()?>world/1?register">Join to Play!</a>
        </div>

    </form>

</div>