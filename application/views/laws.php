<!-- laws Block -->
<?php if ($account) { ?>
<div id="laws_block" class="center_block">
    <strong>Government and laws</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <span class="laws_info_item_parent">
                        <strong class="laws_info_item_label">Territories: </strong>
                        <strong class="laws_info_value text-primary">
                            ...
                        </strong><br>
                    </span>
                    <span class="laws_info_item_parent">
                        <strong class="laws_info_item_label">Population: </strong>
                        <strong class="laws_info_value text-info">
                            ...
                        </strong><br>
                    </span>
                    <span class="laws_info_item_parent">
                        <strong class="laws_info_item_label">Political Support: </strong>
                        <strong class="laws_info_value text-default">
                            ...
                        </strong><br>
                    </span>
                </div>
                <div class="col-md-6">
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
                    <span class="laws_info_item_parent">
                        <strong class="laws_info_item_label">Hourly Profit: </strong>
                        <strong class="laws_info_value text-info">
                            ...
                        </strong><br>
                    </span>
                </div>
            </div>

            <hr>
            <!-- Form -->
            <?php echo form_open('laws_form', array('id' => 'laws_form', 'method' => 'post')); ?>
                <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="input_government" class="pull-right">Form Of Government: </label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" id="input_government" name="input_government" value="<?php echo $account['government']; ?>">
                                <option value="1" <?php if ((int)$account['government'] === DEMOCRACY_KEY) { echo 'selected'; } ?>>Democracy (Difficult Political Support, Minimal Corruption, Great for Economic Players)</option>
                                <option value="2" <?php if ((int)$account['government'] === OLIGARCHY_KEY) { echo 'selected'; } ?>>Oligarchy (Moderate Political Support, Problematic Corruption, Great for Flexibiility)</option>
                                <option value="3" <?php if ((int)$account['government'] === AUTOCRACY_KEY) { echo 'selected'; } ?>>Autocracy (Easy Political Support, Severe Corruption, Great for Early Players and War)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="input_tax_rate" class="pull-right text-success">Tax Rate: (%)</label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="0" max="100" required class="form-control" id="tax_rate" name="input_tax_rate" value="<?php echo $account['tax_rate']; ?>">
                        </div>
                    </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 col-md-push-6">
                        <div id="pass_new_laws_button" class="btn btn-action form-control text-is-bold">Pass New Laws</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <strong>Gameplay Guide</strong>
            <ul id="useful_info_list">
                <li>...</li>
            </ul>
        </div>
    </div>
</div>
<?php } ?>