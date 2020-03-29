<?php if ($account) { ?>
<div id="government_block" class="center_block">
    <strong>Government Overview</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <div class="row">
        <div class="col-md-4 government_card">
            <h3 class="text-center text-primary">Laws</h3>
            <hr>
            <!-- Form -->
            <?= form_open('laws_form', array('id' => 'laws_form', 'method' => 'post')); ?>
                <input type="hidden" name="world_key" value="<?= $world['id']; ?>">
                <div class="row">
                    <div class="col-md-4">
                        <?= generate_popover('Power Structure', 'Democracy provides low support per turn, moderate corruption, and under Marxist Socialism higher max support. Autocracy provides high support per turn, severe corruption, and under Marxist Socialism lower max support. Oligarchy is a balance of the two. Play with the inputs to see the projections', 'right', 'pull-right'); ?>
                        <label for="input_power_structure" class="pull-right">Power Structure: </label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control" id="input_power_structure" name="input_power_structure" value="<?= $account['power_structure']; ?>">
                            <option value="1" title="Difficult Support, Moderate Corruption" <?php if ((int)$account['power_structure'] === DEMOCRACY_KEY) { echo 'selected'; } ?>>Democracy</option>
                            <!-- Great for economic growth -->
                            <option value="2" title="Moderate Support, Harsh Corruption" <?php if ((int)$account['power_structure'] === OLIGARCHY_KEY) { echo 'selected'; } ?>>Oligarchy</option>
                            <!-- Great for flexibility -->
                            <option value="3" title="Easy Support, Severe Corruption" <?php if ((int)$account['power_structure'] === AUTOCRACY_KEY) { echo 'selected'; } ?>>Autocracy</option>
                            <!-- Great for early expansion and war -->
                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <?= generate_popover('Taxes', 'Each 1% of taxes reduces your maximum support by 1', 'right', 'pull-right'); ?>
                        <label for="input_tax_rate" class="pull-right">Tax Rate: (%)</label>
                    </div>
                    <div class="col-md-4">
                        <input type="range" min="0" max="50" required class="form-control" id="input_tax_rate" name="input_tax_rate" value="<?= $account['tax_rate']; ?>">
                    </div>
                    <div class="col-md-4">
                        <strong id="display_input_tax_rate"></strong> %
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <?= generate_popover('Economic Ideology', 'Marxist Socialism removes all income, but allows you to increase your max support and use support to create troops.', 'right', 'pull-right'); ?>
                        <label for="input_power_structure" class="pull-right">Economic Ideology: </label>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-3">
                            <label for="input_ideology" class="pull-right text-success">Free Market</label>
                        </div>
                        <div class="col-md-3">
                            <input type="radio" class="form-control" id="free_market" name="input_ideology" value="<?= FREE_MARKET_KEY; ?>" <?= $account['ideology'] == FREE_MARKET_KEY ? 'checked' : ''; ?> >
                        </div>
                        <div class="col-md-3">
                            <label for="input_ideology" class="pull-right text-danger">Marxist Socialism</label>
                        </div>
                        <!-- Socialism eliminates all profit, units cost support instead of money -->
                        <div class="col-md-3">
                            <input type="radio" class="form-control" id="socialism" name="input_ideology" value="<?= SOCIALISM_KEY; ?>" <?= $account['ideology'] == SOCIALISM_KEY ? 'checked' : ''; ?> >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-md-4">
                        <p class="text-center">
                            <strong id="projected_support_per_minute" class="text-success"></strong>
                            Support/Minute
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-center">
                            <strong id="projected_max_support" class="text-primary"></strong>
                            Max Support
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-center">
                            <strong id="projected_corruption" class="text-danger"></strong>
                            % Corruption
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-1">
                        <?= generate_popover('Passing Laws', 'Laws can only be passed once an hour.', 'right', 'pull-right'); ?>
                    </div>
                    <div class="col-md-8">
                        <div id="pass_new_laws_button" class="btn btn-action form-control text-is-bold">
                            <span id="pass_new_laws_button_text"></span>
                            <span id="laws_passed_confirm_icon" class="text-success">
                                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4 government_card">
            <h3 class="text-center text-primary">Budget</h3>
            <hr>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    GDP:
                    <?= generate_popover('GDP', 'The sum of the GDP from from every territory', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-action">
                    $<span id="budget_gdp"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    Tax Income:
                    <?= generate_popover('Tax Income', 'The percentage of GDP captured by the Tax Rate', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-success">
                    $<span id="budget_tax_income"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    Corruption From Power:
                    <?= generate_popover('Corruption', 'Corruption From Power is caused by the power structure of your government', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-red">
                    -$<span id="budget_power_corruption"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    Corruption From Size:
                    <?= generate_popover('Corruption', 'Corruption From Size is increased by 1 percent for every ' . TILES_PER_CORRUPTION_PERCENT . ' territories you control', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-red">
                    -$<span id="budget_size_corruption"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    Federal Administrative:
                    <?= generate_popover('Federal Administrative', 'Costs for Federal Industry', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-red">
                    -$<span id="budget_federal"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    Military Bases:
                    <?= generate_popover('Military Bases', 'Costs for Military Bases', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-red">
                    -$<span id="budget_bases"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    University Education:
                    <?= generate_popover('University Education', 'Costs for Universities', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-red">
                    -$<span id="budget_education"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    Subsidized Healthcare:
                    <?= generate_popover('University Education', 'Costs for Pharmaceuticals Industry', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-red">
                    -$<span id="budget_pharmaceuticals"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent" style="display: none">
                <strong class="budget_info_item_label">
                    Socialism:
                    <?= generate_popover('Soclaism', 'Socialism consumes all remaining income. But the upside is Socialism allows you to enlist using support instead of cash', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-red">
                    -$<span id="budget_socialism"></span> Million
                </strong><br>
            </span>
            <span class="budget_info_item_parent">
                <strong class="budget_info_item_label">
                    Projected Hourly Earnings:
                    <?= generate_popover('Hourly Earnings', 'Income remaining estimate after accounting for all fixed costs. This does not account for supply shortages.', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-success">
                    $<span id="budget_earnings"></span> Million
                </strong><br>
            </span>
        </div>
        <div class="col-md-4 government_card">
            <div class="government_supplies_parent row">
            <?php $current_category_id = 0; ?>
            <?php $odd = false; ?>
            <?php foreach ($this->supplies as $key => $supply) {
                $odd = !$odd;
                if ($current_category_id !== $supply['category_id']) {
                    $odd = false;
                    $current_category_id = $supply['category_id'];
                    ?>
                <div class="col-md-12">
                    <div class="government_supply_header_parent row">
                        <div class="col-md-3">
                            <h4 class="text-purple">
                                <?= $this->supplies_category_labels[$supply['category_id']]; ?>
                            </h4>
                        </div>
                        <div class="col-md-3">
                        <?php if ($supply['category_id'] == 1) { ?>
                            Supply
                            <?= generate_popover('Supplies', 'Supplies are the heart of your economy. Each hour your settlements and industries will produce and consume supplies. If a supply runs negative, industries depending on that supply will not produce output, and townships depending on that supply will _____ _____ _____ _____. Negative supplies are reset to zero before the hourly cycle, so deficits won\'t run wild while you are away. Most supplies can be traded with diplomacy. Ensure you have a healthy buffer of supplies to keep your economy running smoothly.', 'bottom', ''); ?>
                        <?php } ?>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                            <p class="government_supply lead" id="government_supply_<?= FOOD_KEY; ?>" data-id="<?= FOOD_KEY; ?>"></p>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                            <p class="government_supply lead" id="government_supply_<?= CASH_CROPS_KEY; ?>" data-id="<?= CASH_CROPS_KEY; ?>"></p>
                        <?php } ?>
                        </div>
                        <div class="col-md-2">
                        <?php if ($supply['category_id'] == 1) { ?>
                            Hourly Production
                            <?= generate_popover('Supplies', 'Each hour your settlements and industries will produce and consume supplies.', 'bottom', ''); ?>
                        <?php } ?>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                            <p class="output_projection text-success lead" id="output_projection_<?= FOOD_KEY; ?>" data-id="<?= FOOD_KEY; ?>"></p>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                            <p class="output_projection text-success lead" id="output_projection_<?= CASH_CROPS_KEY; ?>" data-id="<?= CASH_CROPS_KEY; ?>"></p>
                        <?php } ?>
                        </div>
                        <div class="col-md-2">
                        <?php if ($supply['category_id'] == 1) { ?>
                            Hourly Consumption
                            <?= generate_popover('Supplies', 'If a supply runs negative, industries depending on that supply will not produce output, and townships depending on that supply will _____ _____ _____ _____. Negative supplies reset to zero each cycle.', 'bottom', ''); ?>
                        <?php } ?>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                            <p class="input_projection text-danger lead" id="input_projection_<?= FOOD_KEY; ?>" data-id="<?= FOOD_KEY; ?>"></p>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                            <p class="input_projection text-danger lead" id="input_projection_<?= CASH_CROPS_KEY; ?>" data-id="<?= CASH_CROPS_KEY; ?>"></p>
                        <?php } ?>
                        </div>
                        <div class="col-md-2">
                        <?php if ($supply['category_id'] == 1) { ?>
                            Hourly Surplus
                            <?= generate_popover('Supplies', 'Ensure you have a either a healthy surplus or a healthy buffer of each consumed supply to keep your economy running smoothly.', 'bottom', ''); ?>
                        <?php } ?>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                            <p class="sum_projection text-danger lead" id="sum_projection_<?= FOOD_KEY; ?>" data-id="<?= FOOD_KEY; ?>"></p>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                            <p class="sum_projection text-danger lead" id="sum_projection_<?= CASH_CROPS_KEY; ?>" data-id="<?= CASH_CROPS_KEY; ?>"></p>
                        <?php } ?>
                        </div>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                        <div class="col-md-12">
                            <span id="diverse_diet_population_bonus" class="text-success">2</span>X Growth Bonus
                            <?= generate_popover('Diverse Diet Population Bonus', '1 Types: 1X Population Growth | 2 Types: 2X Population Growth | 3 Types: 3X Population Growth | 4 Types: 4X Population Growth | 5 Types: 5X Population Growth', 'bottom', ''); ?>
                        </div>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                        <div class="col-md-12">
                            <span id="cash_crops_support_bonus" class="text-danger">16</span> Support Bonus
                            <?= generate_popover('Diverse Imports Support Bonus', '
                                1 Types:' . (BASE_SUPPORT_BONUS) . ' Support/Hour |
                                2 Types:' . (BASE_SUPPORT_BONUS * 2) . ' Support/Hour |
                                3 Types:' . (BASE_SUPPORT_BONUS * 4) . ' Support/Hour |
                                4 Types:' . (BASE_SUPPORT_BONUS * 8) . ' Support/Hour |
                                5 Types:' . (BASE_SUPPORT_BONUS * 16) . ' Support/Hour
                            ', 'bottom', ''); ?>
                        </div>
                        <?php } ?>
                    </div>

                <?php } ?>

                    <div class="government_supply_parent row <?= $odd ? 'odd_row' : 'even_row' ?>">
                        <div class="col-md-3">
                            <span class="text-left">
                                <label class="text-primary" title="<?= $supply['meta']; ?>">
                                    <?= $supply['label']; ?>
                                    <?php if ($supply['meta']) { ?>
                                        <?= generate_popover('Supplies', $supply['meta'], 'right', ''); ?>
                                    <?php } ?>
                                </label>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <span class="text-right">
                                <span class="government_supply" id="government_supply_<?= $supply['slug']; ?>"></span>
                                <?= $supply['suffix']; ?>
                            </span>
                        </div>
                        <div class="col-md-2">
                            <span class="text-right text-success">
                                <span class="output_projection" id="output_projection_<?= $supply['id']; ?>" data-id="<?= $supply['id']; ?>" data-category-id="<?= $supply['category_id']; ?>"></span>
                            </span>
                        </div>
                        <div class="col-md-2">
                            <span class="text-right text-danger">
                                <span class="input_projection" id="input_projection_<?= $supply['id']; ?>" data-id="<?= $supply['id']; ?>" data-category-id="<?= $supply['category_id']; ?>"></span>
                            </span>
                        </div>
                        <div class="col-md-2">
                            <span class="text-right">
                                <span class="sum_projection" id="sum_projection_<?= $supply['id']; ?>" data-id="<?= $supply['id']; ?>" data-category-id="<?= $supply['category_id']; ?>"></span>
                            </span>
                        </div>
                        <?php if ($supply['market_price_key']) { ?>
                        <div class="col-md-12">
                        </div>
                        <div class="col-md-6">
                            <a class="sell_button btn btn-action form-control">
                                <i class="fas fa-hand-holding-usd"></i>
                                Sell
                            </a>
                        </div>
                        <div class="col-md-6">
                            <p class="lead">
                                Current Price:
                                <strong class="text-success">
                                    $<span id="sell_supply_<?= $supply['id'] ?>"></span>M
                                </strong>
                            </p>
                        </div>
                        <?php } ?>
                    </div>
                <?php if (!isset($this->supplies[$key + 1]) || $current_category_id !== $this->supplies[$key + 1]['category_id']) {
                    ?>
                    </div>
                    <?php 
                    }
                ?>
            <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>