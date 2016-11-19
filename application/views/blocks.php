<!-- Tutorial Block -->
<div id="tutorial_block">
    <span class="exit_tutorial glyphicon glyphicon-remove-sign pull-right" aria-hidden="true"></span>
    <span id="tutorial_title"></span>
    <br>
    <span id="tutorial_text"></span>
</div>

<!-- Law Block -->
<?php if ($log_check) { ?>
<div id="law_block" class="center_block">
    <strong>State of the State</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Territories: </strong>
                        <strong class="law_info_value text-primary">
                            <span class="land_count_span"><?php echo $account['land_count']; ?></span>
                        </strong><br>
                    </span>
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Population: </strong>
                        <strong class="law_info_value text-purple">
                            <span class="population_span"><?php echo $account['stats']['population']; ?></span>K
                        </strong><br>
                    </span>
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Annual GDP: </strong>
                        <strong class="law_info_value text-action">
                            $<span class="gdp_span"><?php echo $account['stats']['gdp']; ?></span>M
                        </strong><br>
                    </span>
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Tax Revenue: </strong>
                        <strong class="law_info_value text-success">
                            <span class="tax_income_span"><?php echo $account['stats']['tax_income']; ?></span>M
                        </strong><br>
                    </span>
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Corruption: </strong>
                        <strong class="law_info_value text-red">
                            <span class="corruption_rate_span"><?php echo $account['stats']['corruption_total']; ?></span>M
                        </strong><br>
                    </span>
                </div>
                <div class="col-md-6">
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Miliary: </strong>
                        <strong class="law_info_value text-danger">
                            $<span class="military_span"><?php echo $account['stats']['military_after']; ?></span>M
                        </strong><br>
                    </span>
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Entitlements: </strong>
                        <strong class="law_info_value text-success">
                            $<span class="entitlements_span"><?php echo $account['stats']['entitlements']; ?></span>M
                        </strong><br>
                    </span>
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Treasury: </strong>
                        <strong class="law_info_value text-yellow">
                            $<span class="treasury_span"><?php echo $account['stats']['treasury_after']; ?></span>M
                        </strong><br>
                    </span>
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">War Weariness: </strong>
                        <strong class="law_info_value text-danger">
                            <span class="war_weariness_span"><?php echo $account['stats']['war_weariness']; ?></span>%
                        </strong><br>
                    </span>
                    <span class="law_info_item_parent">
                        <strong class="law_info_item_label">Political Support: </strong>
                        <strong class="law_info_value text-default">
                            <span class="political_support_span"><?php echo $account['stats']['support']; ?></span>%
                        </strong><br>
                    </span>
                </div>
            </div>

            <hr>
            <!-- Form -->
            <?php echo form_open('user/law_form'); ?>
                <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="input_government" class="pull-right">Form Of Government: </label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" id="input_government" name="input_government" value="<?php echo $account['government']; ?>">
                                <option value="<?php echo $account['government']; ?>"><?php echo $government_dictionary[$account['government']]; ?></option>
                                <?php if ($account['government'] != 1) { ?>
                                <option value="1">Democracy (Minimum Political Support at 50%)</option>
                                <?php } ?>
                                <?php if ($account['government'] != 2) { ?>
                                <option value="2">Oligarchy (25% Corruption, Min Support at 30%)</option>
                                <?php } ?>
                                <?php if ($account['government'] != 3) { ?>
                                <option value="3">Autocracy (50% Corruption, Min Support at 10%)</option>
                                <?php } ?>
                                <?php if ($account['government'] != 0) { ?>
                                    <option value="0">Anarchy (No Functioning Government)</option>
                                <?php } ?>
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
                <button type="submit" class="btn btn-primary form-control">Pass New Laws</button>
            </form>
        </div>
        <div class="col-md-4">
            <h3>How to Run a Nation</h3>
            <p class="lead">Governments offer a trade off of corruption that eats at the tax income, and minimum political support needed to function. Each percent increase in taxes decreased support, as does attacking temporarily. Taxes that aren't eaten by corruption then go the budget. Pick what percent goes toward Military Spending, and what goes towards Entitlements spending. Each percent of Entitlemnet Spending increases Support. What's left over is Discretionary Spending for building in your territories. What's left over from that is your Treasury.</p>
        </div>
    </div>
</div>
<?php } ?>

<!-- Account Update -->
<?php if ($log_check) { ?>
<div id="account_update_block" class="center_block">
    <strong>Nation Settings</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <!-- Form -->
    <?php echo form_open('user/update_account_info'); ?>
        <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_nation_name">Nation Name:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="nation_name" name="nation_name" value="<?php echo $account['nation_name']; ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_nation_flag">National Flag:</label>
                </div>
                <div class="col-md-8">
                    <input type="file" class="form-control" id="nation_flag" name="nation_flag" value="<?php echo $account['nation_flag']; ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_nation_color">National Color:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="jscolor color_input form-control" id="nation_color" name="nation_color" value="<?php echo $account['color']; ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_leader_name">Leader Name:</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="leader_name" name="leader_name" value="<?php echo $account['leader_name']; ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <label for="input_leader_portrait">Leader Portrait:</label>
                </div>
                <div class="col-md-8">
                    <input type="file" class="form-control" id="leader_portrait" name="leader_portrait" value="<?php echo $account['leader_portrait']; ?>">
                </div>
            </div>
        </div>
        <hr>
        <button type="submit" class="btn btn-success form-control">Update Nation</button>
    </form>
</div>
<?php } ?>

<!-- How To Play Block -->
<div id="how_to_play_block" class="center_block">
    <strong>How To Play</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p>
        <strong>Landgrab is a game of fighting for control of the world.</strong>
    </p>
    <blockquote>
        The world is yours.
    </blockquote>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <p>
                This game is in beta, so feel free to point out bugs or give suggestions.
                Contact me at <a href="mailto:goosepostbox@gmail.com" target="_blank">goosepostbox@gmail.com </a>.
            </p>
        </div>
        <div class="col-md-6">
        </div>
    </div>
</div>

<!-- Update Block -->
<div id="update_info_block" class="center_block">
    <strong>Recent Updates</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <hr>

    <small>Version 4.0.0</small>
    <ul>
        <li></li>
    </ul>
</div>

<!-- About Block -->
<div id="about_block" class="center_block">
    <strong>About LandGrab</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p>LandGrab is a game developed by Goose.</p>
    <strong> <a href="http://gooseweb.io/" target="_blank">gooseweb.io</a></strong>
    <br>
    <br>
    <p>Developed in PHP with CodeIgniter 3 and the Google Maps API. You can view and contribute to this project on GitHub. All Rights Reserved.</p>
    <strong> <a href="http://github.com/goosehub/landgrab/" target="_blank">github.com/goosehub/landgrab</a></strong>
    <br>
    <br>
    <p>Special Thanks goes to Google Maps, The StackExchange Network, <a href="https://css-tricks.com/" target="_blank">CSS-Tricks</a>, <a href="https://www.youtube.com/user/ExtraCreditz" target="_blank">Extra Credits</a>
    <a href="http://ithare.com/" target="_blank">itHare</a>, EllisLabs and British Columbia Institute of Technology for providing CodeIgniter, me on the left, /s4s/, llamaseatsocks, Anonymous, Ricky,
    the rest of the Beta Testers, and all my users. Thank you!</p>
</div>

<!-- About Block -->
<div id="faq_block" class="center_block">
    <strong>Frequently Asked Questions</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>
    <p><strong>Why does it say I claimed the land, but it's the wrong color?</strong></p>
    <p>This happens when you get a land and update your map at the exact same time. It is somewhat rare and difficult to fix, but I am working on it.</p>
    <p><strong>Can you make a map with smaller squares?</strong></p>
    <p>The map currently consists of 15,000+ squares. Any more would be pushing most computers and browsers beyond its limits.</p>
    <p><strong>Can water squares be seperate from land squares?</strong></p>
    <p>This game is built on the Google Maps API. It does not tell me if a square of coords have land or not. It would need to be manually entered for each of the squares. There's also the sticky case of what counts as land or water (islands). At the moment, there's no plans to seperate the two.</p>
    <p><strong>Can you make it so new users can't start inside my land?</strong></p>
    <p>New users have to start somewhere. They can take any unfortified land. Also, calculating connected walls for each action requires a large amount of path finding logic that is currently not possible if it was desired.</p>
</div>

<!-- Report Bugs Block -->
<div id="report_bugs_block" class="center_block">
    <strong>Report Bugs</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <p>Please report all bugs to 
        <strong>
            <a href="mailto:goosepostbox@gmail.com" target="_blank">goosepostbox@gmail.com </a>
        </strong>
    </p>
</div>

<!-- Error Block -->
<div id="error_block" class="center_block">
    <strong>There was an issue</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'error_block') { echo $validation_errors; } ?>
</div>

<!-- Login Block -->
<div id="login_block" class="center_block">
    <strong>Login</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'login') { echo $validation_errors; } ?>
    <!-- Form -->
    <?php echo form_open('user/login'); ?>
      <div class="form-group">
        <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
        <label for="input_username">Username</label>
        <input type="username" class="form-control" id="login_input_username" name="username" placeholder="Username">
      </div>
      <div class="form-group">
        <label for="input_password">Password</label>
        <input type="password" class="form-control" id="login_input_password" name="password" placeholder="Password">
      </div>
      <button type="submit" class="btn btn-action form-control">Login</button>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-4">
            <p class="lead">Not registered?</p>
        </div>
        <div class="col-md-2">
            <button class="register_button btn btn-success form-control">Join</button>
        </div>
    </div>
</div>

<!-- Join Block -->
<div id="register_block" class="center_block">
    <strong>Start Playing</strong>

    <button type="button" class="exit_center_block btn btn-default btn-sm">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <!-- Validation Errors -->
    <?php if ($failed_form === 'register') { echo $validation_errors; } ?>
    <!-- Form -->
    <?php echo form_open('user/register'); ?>
      <div class="form-group">
        <input type="hidden" name="world_key" value="<?php echo $world['id']; ?>">
        <label for="input_username">Username</label>
        <input type="username" class="form-control" id="register_input_username" name="username" placeholder="Username">
      </div>
      <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label for="input_password">Password <small>(Optional)</small></label>
                <input type="password" class="form-control" id="register_input_password" name="password" placeholder="Password">
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label for="input_confirm">Confirm <small>(Optional)</small></label>
                <input type="password" class="form-control" id="register_input_confirm" name="confirm" placeholder="Confirm">
              </div>
          </div>
      </div>
      <button type="submit" class="btn btn-action form-control">Start Playing</button>
    </form>
    <hr>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-4">
            <p class="lead">Already a user?</p>
        </div>
        <div class="col-md-2">
            <button class="login_button btn btn-info form-control">Login</button>
        </div>
    </div>
</div>