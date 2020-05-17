<div id="tile_block" class="center_block">
    <button id="exit_tile_block" type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <div class="coord_label pull-right">Coordinates: <a id="tile_coord_link" href=""></a></div>

    <div class="tile_name_label">
        <strong id="tile_name"></strong>
        <?php if (DEBUG_SHOW_TILE_ID) { ?><strong id="tile_id"></strong><?php } ?>
        <input type="tile_name" id="tile_name_input" class="form-control" style="display: none;"/>
        <button id="edit_tile_name" class="btn btn-sm btn-default btn-round">
            <span class="fa fa-edit" title="Edit"></span>
        </button>
        <button id="submit_tile_name" class="btn btn-sm btn-default btn-round" style="display: none;">
            <span class="fa fa-save" title="Edit"></span>
        </button>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <p id="tile_owner_info" class="tile_block_info_line">
                <labe>Led by:</labe>
                <span class="text-info pull-right"><span id="tile_owner_username"></span> of <span id="tile_owner_country_name"></span></span>
            </p>
            <p class="tile_block_info_line">
                <labe>Terrain:</labe>
                <span id="tile_terrain" class="text-warning pull-right"></span>
                <span id="tile_resource" class="text-success pull-right"></span>
                <!-- <img id="tile_resource_icon" src=""/> -->
            </p>
            <p class="tile_block_info_line">
                <labe>Settlement:</labe>
                <span id="tile_settlement_label" class="text-primary pull-right"></span>
            </p>
            <p id="tile_industry_parent" class="tile_block_info_line">
                <labe>Industry:</labe>
                <span id="tile_industry_label" class="text-primary pull-right"></span>
            </p>
            <p id="tile_unit_parent" class="tile_block_info_line">
                <labe>Army:</labe>
                <span id="tile_unit" class="text-primary pull-right"></span>
            </p>
            <p id="tile_defensive_bonus_parent" class="tile_block_info_line">
                <labe>Defensive Bonus:</labe>
                <strong id="tile_defensive_bonus" class="text-success pull-right"></strong>
            </p>
            <p id="tile_offensive_bonus_parent" class="tile_block_info_line">
                <labe>Offensive Bonus:</labe>
                <strong id="tile_offensive_bonus" class="text-danger pull-right"></strong>
            </p>
            <p id="tile_population_parent" class="tile_block_info_line">
                <labe>Population:</labe>
                <strong id="tile_population" class="text-purple pull-right"></strong>
            </p>
            <p id="tile_gdp_parent" class="tile_block_info_line">
                <labe>GDP:</labe>
                <strong id="tile_gdp" class="text-success pull-right"></strong>
            </p>
        </div>
        <div class="col-md-6">
            <p id="tile_desc"></p>
            <textarea type="text" id="tile_desc_input" class="form-control" style="display: none;"></textarea>
            <button id="edit_tile_desc" class="btn btn-sm btn-default btn-round">
                <span class="fa fa-edit" title="Edit"></span>
            </button>
            <button id="submit_tile_desc" class="btn btn-sm btn-default btn-round" style="display: none;">
                <span class="fa fa-save" title="Edit"></span>
            </button>
        </div>
    </div>
    <div id="tile_register_plea">
        <p>
            <strong class="text-action">
                <button class="register_button btn btn-action form-control">Start Playing</button>
            </strong>
        </p>
    </div>
    <div id="tile_first_claim_invalid_ocean">
        <hr>
        <p>
            <strong class="text-danger">
                You can not claim ocean tiles.
            </strong>
        </p>
    </div>
    <div id="tile_first_claim_invalid_township">
        <hr>
        <p>
            <strong class="text-danger">
                You can not claim towns, cities, or metros.
            </strong>
        </p>
    </div>
    <div id="tile_first_claim">
        <hr>
        <div class="row">
            <div class="col-md-6 well">
                <p class="text-center">
                    <strong class="text-action">
                        Make this the Capitol of your new nation!
                    </strong>
                </p>
            </div>
            <div class="col-md-6">
                <!-- For some reason this button didn't want to be styled, so I'm forcing this inline style on it -->
                <button class="btn btn-action form-control" id="do_first_claim" style="height: 6em !important">
                    Claim
                </button>
            </div>
        </div>
    </div>
    <div id="tile_select_select">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" id="tab_select_settle">
                <a id="settle_tab_button" href="#settle" aria-controls="home" role="tab" data-toggle="tab">
                    <button class="tile_tab_button btn btn-success btn-lg">Settlement</button>
                </a>
            </li>
            <li role="presentation" id="tab_select_industry">
                <a id="industry_tab_button" href="#industry" aria-controls="home" role="tab" data-toggle="tab">
                    <button class="tile_tab_button btn btn-primary btn-lg">Industry</button>
                </a>
            </li>
            <li role="presentation" id="tab_select_enlist">
                <a id="enlist_tab_button" href="#enlist" aria-controls="home" role="tab" data-toggle="tab">
                    <button class="tile_tab_button btn btn-danger btn-lg">Enlistment</button>
                </a>
            </li>
        </ul>
    </div>
    <br>
    <div id="tile_select_extended" class="tab-content">
        <div role="tabpanel" class="tab-pane" id="settle">
            <div id="settlement_extended_info" class="well">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-primary">
                            <img id="settlement_selection_icon_preview" src=""/>
                            <strong id="select_settlement_header"></strong>
                            <button id="set_settlement_button" class="btn btn-lg btn-action pull-right">
                                Establish
                                <span class="fa fa-industry"></span>
                            </button>
                        </div>
                        <div style="display: none;">
                            <label class="select_settlement_label">Classification:</label>
                            <span id="select_settlement_type" class="text-primary"></span>
                        </div>
                        <div>
                            <label class="select_settlement_label">Population:</label>
                            <span id="select_settlement_pop" class="text-purple"></span>
                        </div>
                        <div>
                            <label class="select_settlement_label">Terrain:</label>
                            <span id="select_settlement_terrain" class=""></span>
                        </div>
                        <div id="select_settlement_defensive_parent">
                            <label class="select_settlement_defensive_label">Defensive Bonus:</label>
                            <span id="select_settlement_defensive_bonus" class="text-action"></span>
                        </div>
                        <div>
                            <label class="select_settlement_label">GDP:</label>
                            <span id="select_settlement_gdp" class="text-success"></span>
                        </div>
                        <div id="select_settlement_input_parent">
                            <label class="select_settlement_label">Requires:</label>
                            <span id="select_settlement_input" class="text-danger"></span>
                        </div>
                        <div id="select_settlement_output_parent">
                            <label class="select_settlement_label">Produces:</label>
                            <span id="select_settlement_output" class="text-action"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="settlement_select">
                <div class="row">
                    <?php $current_category_id = 0;
                    foreach ($this->settlements as $key => $settlement) {
                        if ($settlement['id'] == UNCLAIMED_KEY) {
                            continue;
                        }
                        if ($current_category_id !== $settlement['category_id']) {
                            $current_category_id = $settlement['category_id'];
                            ?>
                            <div class="col-md-3">
                                <label><?php echo $this->settlement_category_labels[$settlement['category_id']]; ?></label>
                            </div>
                            <div class="col-md-9">
                            <?php 
                        } ?>
                        <button id="preview_settlement_as_<?php echo $settlement['slug']; ?>" data-id="<?php echo $settlement['id']; ?>" class="preview_settlement_button btn btn-default">
                            <?php echo $settlement['label']; ?>
                            <img class="settlement_selection_icon" src="<?=base_url()?>resources/icons/settlements/<?php echo $settlement['id'] ?>.png"/>
                        </button>
                        <?php if (!isset($this->settlements[$key + 1]) || $current_category_id !== $this->settlements[$key + 1]['category_id']) {
                            ?>
                            </div>
                            <?php 
                        }
                    ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="industry">
            <div id="industry_extended_info" class="well">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-primary">
                            <img id="industry_selection_icon_preview" src=""/>
                            <strong id="select_industry_header"></strong>
                            <button id="set_industry_button" class="btn btn-lg btn-action pull-right">
                                Establish
                                <span class="fa fa-city"></span>
                            </button>
                        </div>
                        <div id="select_industry_settlement_parent">
                            <label class="select_industry_label">Settlement:</label>
                            <span id="select_industry_settlement" class="text-primary"></span>
                        </div>
                        <div id="select_industry_terrain_parent">
                            <label class="select_industry_label">Terrain:</label>
                            <span id="select_industry_terrain" class=""></span>
                        </div>
                        <div id="select_industry_gdp_parent">
                            <label class="select_industry_label">GDP:</label>
                            <span id="select_industry_gdp" class="text-success"></span>
                        </div>
                        <div id="select_industry_input_parent">
                            <label class="select_industry_label">Requires:</label>
                            <span id="select_industry_input" class="text-danger"></span>
                        </div>
                        <div id="select_industry_output_parent">
                            <label class="select_industry_label">Produces:</label>
                            <span id="select_industry_output" class="text-action"></span>
                        </div>
                        <div id="select_industry_special_parent">
                            <label class="select_industry_label">Special Effect:</label>
                            <span id="select_industry_special" class="text-action"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="industry_select">
                <div class="row">
                    <?php $current_category_id = 0;
                    foreach ($this->industries as $key => $industry) {
                        if ($current_category_id !== $industry['category_id']) {
                            $current_category_id = $industry['category_id'];
                            ?>
                            <div class="col-md-3">
                                <label><?php echo $this->industry_category_labels[$industry['category_id']]; ?></label>
                            </div>
                            <div class="col-md-9">
                            <?php 
                        } ?>
                        <button id="preview_industry_as_<?php echo $industry['slug']; ?>" data-id="<?php echo $industry['id']; ?>" class="preview_industry_button btn btn-default">
                            <?php echo $industry['label']; ?>
                            <img class="industry_selection_icon" src="<?=base_url()?>resources/icons/industries/<?php echo $industry['id'] ?>.png"/>
                        </button>
                        <?php if (!isset($this->industries[$key + 1]) || $current_category_id !== $this->industries[$key + 1]['category_id']) {
                            ?>
                            </div>
                            <?php 
                        }
                    ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="enlist">
            <div id="enlist_select">
                <div id="enlist_disabled">
                    <p>
                        <strong class="text-danger">
                            Move Current Unit To Enlist A New Unit
                        </strong>
                    </p>
                </div>
                <div id="enlist_enabled">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="text-primary">Support Costs</span>
                            <?= generate_popover('Support Costs', '
                                Moving Unit: ' . SUPPORT_COST_MOVE_UNIT . ' Support <br>
                                Capture Land: ' . SUPPORT_COST_CAPTURE_LAND . ' Support <br>
                                Declare War: ' . SUPPORT_COST_DECLARE_WAR . ' Support
                            ', 'top', 'pull-right'); ?>
                        </div>
                        <div class="col-md-4">
                            <span class="text-primary">Defense Bonuses</span>
                            <?= generate_popover('Defense Bonus', '
                                Mountain Defensive Bonus: X' . (1 + MOUNTAIN_DEFENSIVE_BONUS) . '<br>
                                Tundra Defensive Bonus: X' . (1 + TUNDRA_DEFENSIVE_BONUS) . '<br>
                                Barren Offensive Bonus: X' . (1 + BARREN_OFFENSIVE_BONUS) . '<br>
                                <br>
                                Town Defensive Bonus: X' . (1 + TOWN_DEFENSIVE_BONUS) . '<br>
                                City Defensive Bonus: X' . (1 + CITY_DEFENSIVE_BONUS) . '<br>
                                Metro Defensive Bonus: X' . (1 + METRO_DEFENSIVE_BONUS) . '
                            ', 'top', 'pull-right'); ?>
                        </div>
                        <div class="col-md-4">
                            <span class="text-primary">Situational Units</span>
                            <?= generate_popover('Special Units', '
                                Units transform into Navy units on ocean and will transform back after returning to land.
                                <br><br>
                                Townships without units form a Militia to defend themselves when attacked.
                            ', 'top', 'pull-right'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 well">
                            <button class="enlist_unit_button btn btn-success form-control" data-id="<?= INFANTRY_KEY; ?>">
                                Infantry
                                <img class="unit_preview_icon_preview" src="<?=base_url()?>resources/icons/units/<?=INFANTRY_KEY;?>-neutral.png">
                            </button>
                            <p class="text-primary">
                                Cost:
                                <strong class="show_if_free_market">
                                    $<?php echo $this->unit_types[INFANTRY_KEY - 1]['cash_cost']; ?> Billion
                                </strong>
                                <strong class="show_if_socialism">
                                    Cost: <?php echo $this->unit_types[INFANTRY_KEY - 1]['support_cost']; ?> Support
                                </strong>
                            </p>
                            <p class="text-success">
                                Strong against <span>Airforce, Navy, & Militia</span>
                            </p>
                            <p class="text-danger">
                                Weak against <span>Tanks</span>
                            </p>
                            <p class="text-purple">
                                Can take <span>Territories, Towns, & Cities</span>
                            </p>
                            <p class="text-info">
                                It's cheap price makes it a great foundation for your armies
                            </p>
                        </div>
                        <div class="col-md-4 well">
                            <button class="enlist_unit_button btn btn-danger form-control" data-id="<?= TANKS_KEY; ?>">
                                Tanks
                                <img class="unit_preview_icon_preview" src="<?=base_url()?>resources/icons/units/<?=TANKS_KEY?>-neutral.png">
                            </button>
                            <p class="text-primary">
                                Cost:
                                <strong class="show_if_free_market">
                                    $<?php echo $this->unit_types[TANKS_KEY - 1]['cash_cost']; ?> Billion
                                </strong>
                                <strong class="show_if_socialism">
                                    <?php echo $this->unit_types[TANKS_KEY - 1]['support_cost']; ?> Support
                                </strong>
                            </p>
                            <p class="text-success">
                                Strong against <span>Infantry, Navy, & Militia</span>
                            </p>
                            <p class="text-danger">
                                Weak against <span>Airforce</span>
                            </p>
                            <p class="text-purple">
                                Can take <span>Territories, Towns, Cities, & Metros</span>
                            </p>
                            <p class="text-info">
                                A great counter to infantry armies without air support, and is the only unit able to capture a Metro
                            </p>
                        </div>
                        <div class="col-md-4 well">
                            <button class="enlist_unit_button btn btn-warning form-control" data-id="<?= AIRFORCE_KEY; ?>">
                                Airforce
                                <img class="unit_preview_icon_preview" src="<?=base_url()?>resources/icons/units/<?=AIRFORCE_KEY?>-neutral.png">
                            </button>
                            <p class="text-primary">
                                Cost:
                                <strong class="show_if_free_market">
                                    $<?php echo $this->unit_types[AIRFORCE_KEY - 1]['cash_cost']; ?> Billion
                                </strong>
                                <strong class="show_if_socialism">
                                    Cost: <?php echo $this->unit_types[AIRFORCE_KEY - 1]['support_cost']; ?> Support
                                </strong>
                            </p>
                            <p class="text-success">
                                Strong against <span>Tanks, Navy, & Militia</span>
                            </p>
                            <p class="text-danger">
                                Weak against <span>Infantry</span>
                            </p>
                            <p class="text-purple">
                                Can take <span>Territories</span>
                            </p>
                            <p class="text-info">
                                It's expensive price and inability to take townships means it should be used primarily as support against Tanks.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>