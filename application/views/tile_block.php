<div id="tile_block" class="center_block">
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <div class="coord_label pull-right">Coordinates: <a id="tile_coord_link" href=""></a></div>

    <div class="tile_name_label">
        <strong id="tile_name">Paris</strong>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <p id="tile_owner_info" class="tile_block_info_line">
                <labe>Led by:</labe>
                <span class="text-info pull-right"><span id="tile_owner_username">Elizabeth</span> of <span id="tile_owner_country_name">France</span></span>
            </p>
            <p class="tile_block_info_line">
                <labe>Terrain:</labe>
                <span id="tile_terrain" class="text-warning pull-right"></span>
            </p>
            <p class="tile_block_info_line">
                <labe>Resource:</labe>
                <span class="pull-right">
                    <img id="tile_resource_icon" src=""/>
                    <span id="tile_resource" class="text-warning"></span>
                </span>
            </p>
            <p class="tile_block_info_line">
                <labe>Settlement:</labe>
                <span id="tile_settlement_label" class="text-primary pull-right">City</span>
            </p>
            <p class="tile_block_info_line">
                <labe>Industry:</labe>
                <span id="tile_industry_label" class="text-primary pull-right">Automotive</span>
            </p>
            <p class="tile_block_info_line">
                <labe>Population:</labe>
                <span id="tile_population" class="text-purple pull-right">8,200K</span>
            </p>
            <p class="tile_block_info_line">
                <labe>GDP:</labe>
                <span id="tile_gdp" class="text-success pull-right">$560M</span>
            </p>
        </div>
        <div class="col-md-6">
            <p id="tile_desc">
                Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.
            </p>
        </div>
    </div>
    <div id="settlement_select">
        <hr>
        <h3>Select Setttlement</h3>
        <div class="row">
            <?php $current_category_id = 0;
            foreach ($this->settlements as $key => $settlement) {
                if ($current_category_id !== $settlement['category_id']) {
                    $current_category_id = $settlement['category_id'];
                    ?>
                    <div class="col-md-3">
                        <label><?php echo $this->settlement_category_labels[$settlement['category_id']]; ?></label>
                    </div>
                    <div class="col-md-9">
                    <?php 
                } ?>
                <button id="set_tile_as_<?php echo $settlement['slug']; ?>" data-id="<?php echo $settlement['id']; ?>" class="set_settlement_button btn btn btn-default">
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
    <div id="industry_select">
        <hr>
        <h3>Select Industry</h3>
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
                <button id="set_tile_as_<?php echo $industry['slug']; ?>" class="set_industry_button btn btn btn-default">
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