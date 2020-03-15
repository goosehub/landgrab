<div id="tile_block" class="center_block">
    <button id="exit_tile_block" type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <div class="coord_label pull-right">Coordinates: <a id="tile_coord_link" href=""></a></div>

    <div class="tile_name_label">
        <strong id="tile_name"></strong>
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
            <p class="tile_block_info_line">
                <labe>Population:</labe>
                <span id="tile_population" class="text-purple pull-right"></span>
            </p>
            <p class="tile_block_info_line">
                <labe>GDP:</labe>
                <span id="tile_gdp" class="text-success pull-right"></span>
            </p>
            <p id="tile_unit_parent" class="tile_block_info_line">
                <labe>Army:</labe>
                <span id="tile_unit" class="text-primary pull-right"></span>
            </p>
            <p id="tile_defensive_bonus_parent" class="tile_block_info_line">
                <labe>Defensive Bonus:</labe>
                <span id="tile_defensive_bonus" class="text-action pull-right"></span>
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
        <button class="btn btn-action form-control" id="do_first_claim">
            Claim
        </button>
        <p class="text-center">
            <strong class="text-action">
                Make this the Capitol of your new nation!
            </strong>
        </p>
    </div>
    <div id="tile_select_select">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" id="tab_select_settle">
                <a id="settle_tab_button" href="#settle" aria-controls="home" role="tab" data-toggle="tab">
                    <button class="tile_tab_button btn btn-success form-control">Settlement</button>
                </a>
            </li>
            <li role="presentation" id="tab_select_industry">
                <a id="industry_tab_button" href="#industry" aria-controls="home" role="tab" data-toggle="tab">
                    <button class="tile_tab_button btn btn-primary form-control">Industry</button>
                </a>
            </li>
            <li role="presentation" id="tab_select_enlist">
                <a id="enlist_tab_button" href="#enlist" aria-controls="home" role="tab" data-toggle="tab">
                    <button class="tile_tab_button btn btn-danger form-control">Enlistment</button>
                </a>
            </li>
        </ul>
    </div>
    <br>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="settle">
            <div id="settlement_extended_info" class="well">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-action"><strong id="select_settlement_header"></strong></div>
                    </div>
                    <div class="col-md-6">
                        <div id="settlement_confirm" class="btn btn-action form-control">
                            Establish
                        </div>
                    </div>
                </div>
                <div>
                    <label class="select_settlement_label">Classification:</label>
                    <span id="select_settlement_type" class="text-primary"></span>
                </div>
                <div>
                    <label class="select_settlement_label">Population:</label>
                    <span id="select_settlement_pop" class="text-primary"></span>
                </div>
                <div>
                    <label class="select_settlement_label">Terrain:</label>
                    <span id="select_settlement_terrain" class="text-primary"></span>
                </div>
                <div id="select_settlement_defensive_parent">
                    <label class="select_settlement_defensive_label">Defensive Bonus:</label>
                    <span id="select_settlement_defensive_bonus" class="text-primary"></span>
                </div>
                <div id="select_settlement_input_parent">
                    <label class="select_settlement_label">Requires:</label>
                    <span id="select_settlement_input" class="text-primary"></span>
                </div>
                <div>
                    <label class="select_settlement_label">Produces:</label>
                    <span id="select_settlement_output" class="text-primary"></span>
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
                        <button id="set_settlement_as_<?php echo $settlement['slug']; ?>" data-id="<?php echo $settlement['id']; ?>" class="set_settlement_button btn btn btn-default">
                            <?php echo $settlement['label']; ?>
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
                    <div class="col-md-6">
                        <div class="text-action"><strong id="select_industry_header"></strong></div>
                    </div>
                    <div class="col-md-6">
                        <div id="industry_confirm" class="btn btn-action form-control">
                            Establish
                        </div>
                    </div>
                </div>
                <div>
                    <label class="select_industry_label">Settlement:</label>
                    <span id="select_industry_settlement" class="text-primary"></span>
                </div>
                <div>
                    <label class="select_industry_label">Terrain:</label>
                    <span id="select_industry_terrain" class="text-primary"></span>
                </div>
                <div>
                    <label class="select_industry_label">GDP:</label>
                    <span id="select_industry_gdp" class="text-primary"></span>
                </div>
                <div>
                    <label class="select_industry_label">Requires:</label>
                    <span id="select_industry_input" class="text-primary"></span>
                </div>
                <div>
                    <label class="select_industry_label">Produces:</label>
                    <span id="select_industry_output" class="text-primary"></span>
                </div>
                <div id="select_industry_special_parent">
                    <label class="select_industry_label">Special Effect:</label>
                    <span id="select_industry_special" class="text-primary"></span>
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
                        <button id="set_industry_as_<?php echo $industry['slug']; ?>" data-id="<?php echo $industry['id']; ?>" class="set_industry_button btn btn btn-default">
                            <?php echo $industry['label']; ?>
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
                        <?= generate_popover('Units', 'Thse units have a rock paper scissors mechanism. Infantry are strong against Commandos, Tanks are strong against Infantry, Commandos are strong against Tanks. Units on ocean tiles become navy units, which are weak against all land units.', 'top', 'pull-right'); ?>
                    <ul class="enlist_button">
                        <button class="enlist_unit_button btn btn-success" data-id="<?= INFANTRY_KEY; ?>">
                            Infantry
                            <span class="badge">
                                $<?php echo $this->unit_types[INFANTRY_KEY - 1]['cost_base']; ?>M
                            </span>
                        </button>
                        <button class="enlist_unit_button btn btn-danger" data-id="<?= TANKS_KEY; ?>">
                            Tanks
                            <span class="badge">
                                $<?php echo $this->unit_types[TANKS_KEY - 1]['cost_base']; ?>M
                            </span>
                        </button>
                        <button class="enlist_unit_button btn btn-warning" data-id="<?= COMMANDOS_KEY; ?>">
                            Commandos
                            <span class="badge">
                                $<?php echo $this->unit_types[COMMANDOS_KEY - 1]['cost_base']; ?>M
                            </span>
                        </button>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>