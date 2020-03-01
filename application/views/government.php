<?php if ($account) { ?>
<div id="government_block" class="center_block">
    <strong>Government Overview</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <div class="row">
        <div class="col-md-4 government_card">
            <h3 class="text-center text-primary">Laws</h3>
            <!-- Form -->
            <?php echo form_open('laws_form', array('id' => 'laws_form', 'method' => 'post')); ?>
                <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
                <div class="row">
                    <div class="col-md-4">
                        <label for="input_government" class="pull-right">Form Of Government: </label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control" id="input_government" name="input_government" value="<?php echo $account['government']; ?>">
                            <option value="1" <?php if ((int)$account['government'] === DEMOCRACY_KEY) { echo 'selected'; } ?>>Democracy (Difficult Support, Ordinary Corruption)</option>
                            <!-- Great for economic growth -->
                            <option value="2" <?php if ((int)$account['government'] === OLIGARCHY_KEY) { echo 'selected'; } ?>>Oligarchy (Moderate Support, Harsh Corruption)</option>
                            <!-- Great for flexibility -->
                            <option value="3" <?php if ((int)$account['government'] === AUTOCRACY_KEY) { echo 'selected'; } ?>>Autocracy (Easy Support, Severe Corruption)</option>
                            <!-- Great for early expansion and war -->
                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <label for="input_tax_rate" class="pull-right text-success">Tax Rate: (%)</label>
                    </div>
                    <div class="col-md-8">
                        <input type="number" min="0" max="100" required class="form-control" id="tax_rate" name="input_tax_rate" value="<?php echo $account['tax_rate']; ?>">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-3">
                            <label for="input_ideology" class="pull-right text-success">Free Market</label>
                        </div>
                        <div class="col-md-3">
                            <input type="radio" class="form-control" id="free_market" name="input_ideology" value="<?php echo FREE_MARKET_KEY; ?>" <?php echo $account['ideology'] == FREE_MARKET_KEY ? 'checked' : ''; ?> >
                        </div>
                        <div class="col-md-3">
                            <label for="input_ideology" class="pull-right text-danger">Full Socialism</label>
                        </div>
                        <!-- Socialism eliminates all profit, units cost support instead of money -->
                        <div class="col-md-3">
                            <input type="radio" class="form-control" id="socialism" name="input_ideology" value="<?php echo SOCIALISM_KEY; ?>" <?php echo $account['ideology'] == SOCIALISM_KEY ? 'checked' : ''; ?> >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 col-md-push-4">
                        <hr>
                        <div id="pass_new_laws_button" class="btn btn-action form-control text-is-bold">Pass New Laws</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4 government_card">
            <h3 class="text-center text-primary">Budget</h3>
            <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">GDP: </strong>
                <strong class="laws_info_value text-action">
                    ...
                </strong><br>
            </span>
            <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">Tax Income: </strong>
                <strong class="laws_info_value text-success">
                    ...
                </strong><br>
            </span>
            <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">Corruption: </strong>
                <strong class="laws_info_value text-red">
                    ...
                </strong><br>
            </span>
            <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">Federal Administrative: </strong>
                <strong class="laws_info_value text-red">
                    ...
                </strong><br>
            </span>
            <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">Military Bases: </strong>
                <strong class="laws_info_value text-red">
                    ...
                </strong><br>
            </span>
            <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">University Education: </strong>
                <strong class="laws_info_value text-red">
                    ...
                </strong><br>
            </span>
            <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">Subsidized Healthcare: </strong>
                <strong class="laws_info_value text-red">
                    ...
                </strong><br>
            </span>
            <!-- <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">Socialism: </strong>
                <strong class="laws_info_value text-red">
                    ...
                </strong><br>
            </span> -->
            <span class="laws_info_item_parent">
                <strong class="laws_info_item_label">Hourly Profit: </strong>
                <strong class="laws_info_value text-info">
                    ...
                </strong><br>
            </span>
        </div>
        <div class="col-md-4 government_card">
            <h3 class="text-center text-primary">Supplies</h3>
            <div id="account_supply_list" class="row"></div>
        </div>
    </div>
</div>
<?php } ?>