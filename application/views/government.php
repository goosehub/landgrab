<?php if ($account) { ?>
<div id="government_block" class="center_block">
    <strong>Government Overview</strong>
    <button type="button" id="exit_government" class="exit_center_block btn btn-default btn-sm pull-right">
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
                        <?= generate_popover('Power Structure', 'Democracy provides low support per turn and moderate corruption.<br><br>Autocracy provides high support per turn and severe corruption.<br><br>Oligarchy is a balance of the two.<br><br>Play with the inputs to see the projections', 'right', 'pull-right'); ?>
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
                        <?= generate_popover('Economic Ideology', 'Marxist Socialism removes all income, but allows you to increase your max support and use support to create troops. Democracy will provide the highest maximum support and Autocracy the least', 'right', 'pull-right'); ?>
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
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-md-4">
                        <p class="text-info">
                            Laws can only be passed once an hour
                        </p>
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
                    <small id="budget_gdp_bonus_parent" class="text-action">
                        <span id="budget_gdp_bonus"></span>% Bonus
                    </small>
                    <?= generate_popover('GDP', 'The sum of the GDP from every settlement and every industry, plus any GDP Bonus Supplies.', 'right'); ?>
                </strong>
                <strong class="budget_info_value text-action">
                    $<span id="budget_gdp"></span> Billion
                </strong><br>
            </span>
            <span id="socialism_budget" class="show_if_socialism">
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        Socialism:
                        <?= generate_popover('Soclaism', 'Marxist Socialism removes all income, but allows you to increase your max support and use support to create troops.', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-red">
                        -$<span id="budget_socialism"></span> Billion
                    </strong><br>
                </span>
            </span>
            <span id="free_market_budget" class="show_if_free_market">
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        Tax Income:
                        <?= generate_popover('Tax Income', 'Update your tax rate to capture more GDP into income', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-success">
                        $<span id="budget_tax_income"></span> Billion
                    </strong><br>
                </span>
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        Corruption From Power:
                        <?= generate_popover('Corruption', 'Corruption From Power is caused by the power structure of your government', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-red">
                        -$<span id="budget_power_corruption"></span> Billion
                    </strong><br>
                </span>
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        Corruption From Size:
                        <?= generate_popover('Corruption', 'Corruption From Size is increased by 1 percent for every ' . TILES_PER_CORRUPTION_PERCENT . ' territories you control', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-red">
                        -$<span id="budget_size_corruption"></span> Billion
                    </strong><br>
                </span>
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        Federal Administrative:
                        <?= generate_popover('Federal Administrative', 'Costs for Federal Industry', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-red">
                        -$<span id="budget_federal"></span> Billion
                    </strong><br>
                </span>
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        Military Bases:
                        <?= generate_popover('Military Bases', 'Costs for Military Bases', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-red">
                        -$<span id="budget_bases"></span> Billion
                    </strong><br>
                </span>
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        University Education:
                        <?= generate_popover('University Education', 'Costs for Universities', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-red">
                        -$<span id="budget_education"></span> Billion
                    </strong><br>
                </span>
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        Subsidized Healthcare:
                        <?= generate_popover('University Education', 'Costs for Pharmaceuticals Industry', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-red">
                        -$<span id="budget_pharmaceuticals"></span> Billion
                    </strong><br>
                </span>
                <span class="budget_info_item_parent">
                    <strong class="budget_info_item_label">
                        Estimated Earnings:
                        <?= generate_popover('Earnings', 'Estimate for the Income remaining after accounting for all fixed costs, per cycle. This does not account for supply shortages which can affect your actual GDP vs the projected GDP.', 'right'); ?>
                    </strong>
                    <strong class="budget_info_value text-success">
                        $<span id="budget_earnings"></span> Billion
                    </strong><br>
                </span>
                <hr>
                <p class="text-center text-info lead">
                    Production cycle runs every <?= CYCLE_MINUTES ?> minutes
                </p>
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
                                <?php if ($supply['category_id'] == ENERGY_CATEGORY_ID) { ?>
                                    <?= generate_popover('Energy', 'Coal, Gas, Oil, and Uranium are high energy supplies that are scattered across the map. Each produces 2 per cycle. Trade or expand your borders to get access to these valuables resources', 'bottom', ''); ?>
                                <?php } ?>
                                <?php if ($supply['category_id'] == METALS_CATEGORY_ID) { ?>
                                    <?= generate_popover('Metals', 'Metal Alloys are scattered across the map. Each produces 3 per cycle. Trade or expand your borders to get access to these valuables resources', 'bottom', ''); ?>
                                <?php } ?>
                                <?php if ($supply['category_id'] == RICHES_CATEGORY_ID) { ?>
                                    <?= generate_popover('Riches', 'Riches are scattered across the map. Each produces 1 per cycle. They can be sold at market prices that fluctuate every cycle. Trade or expand your borders to get access to these valuables resources', 'bottom', ''); ?>
                                <?php } ?>
                            </h4>
                        </div>
                        <div class="col-md-3">
                        <?php if ($supply['category_id'] == 1) { ?>
                            Supply
                            <?= generate_popover('Supplies', 'Each cycle settlements & industries produce and consume supplies. Industries consume first, and produce second, so ensure you have supplies on hand before starting new industries.', 'bottom', ''); ?>
                        <?php } ?>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                            <p class="category_government_supply government_supply lead" id="government_supply_<?= FOOD_KEY; ?>" data-id="<?= FOOD_KEY; ?>"></p>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                            <p class="category_government_supply government_supply lead" id="government_supply_<?= CASH_CROPS_KEY; ?>" data-id="<?= CASH_CROPS_KEY; ?>"></p>
                        <?php } ?>
                        </div>
                        <div class="col-md-2">
                        <?php if ($supply['category_id'] == 1) { ?>
                            Production
                            <?= generate_popover('Supplies', 'Each cycle your settlements and industries will produce and consume supplies.', 'bottom', ''); ?>
                        <?php } ?>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                            <p class="category_output_projection output_projection text-success lead" id="output_projection_<?= FOOD_KEY; ?>" data-id="<?= FOOD_KEY; ?>"></p>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                            <p class="category_output_projection output_projection text-success lead" id="output_projection_<?= CASH_CROPS_KEY; ?>" data-id="<?= CASH_CROPS_KEY; ?>"></p>
                        <?php } ?>
                        </div>
                        <div class="col-md-2">
                        <?php if ($supply['category_id'] == 1) { ?>
                            Consumption
                            <?= generate_popover('Supplies', 'If you have a supply shortage, industries depending on that supply will not produce, and townships depending on that supply will lose population. Negative supplies reset to zero each cycle, so deficits won\'t run wild while you are away. Ensure a healthy buffer of supplies to keep your economy running smoothly.', 'bottom', ''); ?>
                        <?php } ?>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                            <p class="category_input_projection input_projection text-danger lead" id="input_projection_<?= FOOD_KEY; ?>" data-id="<?= FOOD_KEY; ?>"></p>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                            <p class="category_input_projection input_projection text-danger lead" id="input_projection_<?= CASH_CROPS_KEY; ?>" data-id="<?= CASH_CROPS_KEY; ?>"></p>
                        <?php } ?>
                        </div>
                        <div class="col-md-2">
                        <?php if ($supply['category_id'] == 1) { ?>
                            Surplus
                            <?= generate_popover('Supplies', 'Ensure you have a either a healthy surplus or a healthy buffer of each consumed supply to keep your economy running smoothly.', 'bottom', ''); ?>
                        <?php } ?>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                            <p class="category_sum_projection sum_projection text-danger lead" id="sum_projection_<?= FOOD_KEY; ?>" data-id="<?= FOOD_KEY; ?>"></p>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                            <p class="category_sum_projection sum_projection text-danger lead" id="sum_projection_<?= CASH_CROPS_KEY; ?>" data-id="<?= CASH_CROPS_KEY; ?>"></p>
                        <?php } ?>
                        </div>
                        <?php if ($supply['category_id'] == FOOD_CATEGORY_ID) { ?>
                        <div class="col-md-12">
                            <span id="diverse_diet_population_bonus" class="text-success"></span>X Diverse Diet Bonus
                            <?= generate_popover('Diverse Diet Population Bonus', '1 Types: 1X Population Growth<br>2 Types: 2X Population Growth<br>3 Types: 3X Population Growth<br>4 Types: 4X Population Growth<br>5 Types: 5X Population Growth', 'bottom', ''); ?>
                        </div>
                        <?php } ?>
                        <?php if ($supply['category_id'] == CASH_CROPS_CATEGORY_ID) { ?>
                        <div class="col-md-12">
                            <span id="cash_crops_support_bonus" class="text-danger"></span> Luxury Bonus
                            <?= generate_popover('Luxury Support Bonus', '
                                1 Types: +1 Support/Minute<br>
                                2 Types: +2 Support/Minute<br>
                                3 Types: +3 Support/Minute<br>
                                4 Types: +4 Support/Minute<br>
                                5 Types: +5 Support/Minute
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
                                        <?= generate_popover($supply['label'], $supply['meta'], 'right', ''); ?>
                                    <?php } ?>
                                </label>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <span class="government_supply_and_suffix">
                                <span class="government_supply" id="government_supply_<?= $supply['id']; ?>"></span>
                                <small class="government_supply_suffix"><?= $supply['suffix']; ?></small>
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
                            <a class="sell_button btn btn-success form-control" data-id="<?= $supply['id'] ?>">
                                <i class="fas fa-hand-holding-usd"></i>
                                Sell
                                <span class="sell_button_confirm_icon text-action" data-id="<?= $supply['id'] ?>">
                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                </span>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <p class="lead">
                                Current Price:
                                <strong class="text-success">
                                    $<span id="sell_supply_<?= $supply['id'] ?>"></span>
                                </strong>
                                <small>Billion</small>
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