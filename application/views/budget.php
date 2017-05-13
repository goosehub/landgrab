<!-- Budget Block -->
<?php if ($log_check) { ?>
<div id="budget_block" class="center_block">
    <strong>Government and Budget</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <div class="row">
        <div class="col-md-7">
            <div class="row">
                <div class="col-md-6">
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Territories: </strong>
                        <strong class="budget_info_value text-primary">
                            <span class="land_count_span"><?php echo number_format($account['land_count']); ?></span>
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Population: </strong>
                        <strong class="budget_info_value text-info">
                            <span class="population_span"><?php echo number_format($account['stats']['population']); ?></span>K
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Culture: </strong>
                        <strong class="budget_info_value text-purple">
                            <span class="culture_span"><?php echo number_format($account['stats']['culture']); ?></span>
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Military Bonus: </strong>
                        <strong class="budget_info_value text-danger">
                            $<span class="military_stats_span"><?php echo number_format($account['stats']['military']); ?></span>M
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">War Weariness: </strong>
                        <strong class="budget_info_value text-red">
                            <span class="weariness_span"><?php echo number_format($account['stats']['weariness']); ?></span>
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Political Support: </strong>
                        <strong class="budget_info_value text-default">
                            <span class="political_support_span"><?php echo number_format($account['stats']['support']); ?></span>
                        </strong><br>
                    </span>
                </div>
                <div class="col-md-6">
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">GDP: </strong>
                        <strong class="budget_info_value text-action">
                            $<span class="gdp_span"><?php echo number_format($account['stats']['gdp']); ?></span>M
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Tax Income: </strong>
                        <strong class="budget_info_value text-success">
                            $<span class="tax_income_span"><?php echo number_format($account['stats']['tax_income_total']); ?></span>M
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Corruption: </strong>
                        <strong class="budget_info_value text-red">
                            $<span class="corruption_rate_span"><?php echo number_format($account['stats']['corruption_total']); ?></span>M
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Military Spending: </strong>
                        <strong class="budget_info_value text-danger">
                            $<span class="military_spending_span"><?php echo number_format($account['stats']['military_spending']); ?></span>M
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Entitlements: </strong>
                        <strong class="budget_info_value text-info">
                            $<span class="entitlements_span"><?php echo number_format($account['stats']['entitlements']); ?></span>M
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Building Maintenance: </strong>
                        <strong class="budget_info_value text-warning">
                            $<span class="building_maintenance_span"><?php echo number_format($account['stats']['building_maintenance']); ?></span>M
                        </strong><br>
                    </span>
                    <span class="budget_info_item_parent">
                        <strong class="budget_info_item_label">Available Revenue: </strong>
                        <strong class="budget_info_value text-default">
                            $<span class="treasury_span"><?php echo number_format($account['stats']['treasury_after']); ?></span>M
                        </strong><br>
                    </span>
                </div>
            </div>

            <hr>
            <!-- Form -->
            <?php echo form_open('budget_form', array('id' => 'budget_form', 'method' => 'post')); ?>
                <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="input_government" class="pull-right">Form Of Government: </label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" id="input_government" name="input_government" value="<?php echo $account['government']; ?>">
                                <option value="1" <?php if ((int)$account['government'] === 1) { echo 'selected'; } ?>>Democracy (High Tax Weariness, <?php echo $democracy_corruption_rate ?>% Corruption)</option>
                                <option value="2" <?php if ((int)$account['government'] === 2) { echo 'selected'; } ?>>Oligarchy (Low Tax Weariness, <?php echo $oligarchy_corruption_rate ?>% Corruption)</option>
                                <option value="3" <?php if ((int)$account['government'] === 3) { echo 'selected'; } ?>>Autocracy (No Tax Weariness, <?php echo $autocracy_corruption_rate ?>% Corruption)</option>
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
                    <div class="row">
                        <div class="col-md-6">
                            <label for="input_military_budget" class="pull-right text-danger">Military Budget: (%)</label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="0" max="100" required class="form-control" id="military_budget" name="input_military_budget" value="<?php echo $account['military_budget']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="input_entitlements_budget" class="pull-right text-info">Entitlements Budget: (%)</label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="0" max="100" required class="form-control" id="entitlements_budget" name="input_entitlements_budget" value="<?php echo $account['entitlements_budget']; ?>">
                        </div>
                    </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 col-md-push-6">
                        <div id="pass_new_budget_button" class="btn btn-action form-control">Apply New Budget</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-5">
            <strong>Useful Information</strong>
            <ul id="useful_info_list">
                <li>For every 5 villages, you can build a Town, every 5 Towns a City, and every 5 Cities a Metropolis</li>
                <li>Taxes allow you to increase your budgets and build on your lands</li>
                <li>Corruption Eats at your Tax Income</li>
                <li>Entitlments give you more Support</li>
                <li>Weariness decreases your Support</li>
                <li>Weariness decreases by 5 every minute</li>
                <li>When you have no Support left, your government can no longer function</li>
                <li>A larger Military means a smaller weariness penalty on attacking and more weariness on those who attack you</li>
                <li>Form alliances and build Embassies on other players Capitols to help them against shared enemies.</li>
                <li>Revenue is what's left over from your budgets and buildings</li>
                <li>Revenue doesn't increase over time, but is instead a static number</li>
                <li>If you get stuck in debt or in low support, consider adjusting your taxes or removing buildings</li>
                <li>The player with the largest population gets a 50% Defensive Bonus</li>
                <li>The player with the most culture gets a 50% Offensive Bonus</li>
                <li>You need to own at least <?php echo $sniper_land_minimum; ?> lands to destroy a Metropolis</li>
                <li>For every <?php echo $weariness_increase_land_count; ?> lands you own, weariness increases by 1 for each attack</li>
                <li>To conquest quicker, hold the <kbd>a</kbd> key on click to blindly attack without launching a window</li>
                <li>Explore the other worlds in the Worlds tab</li>
                <li>Try turning on Satellite Mode</li>
                <li>Check out <a href="https://www.reddit.com/r/LandGrab/" target="_blank">/r/LandGrab</a> for discussion and updates</li>
            </ul>
        </div>
    </div>
</div>
<?php } ?>