<div id="new_trade_block" class="center_block">
    <form id="trade_form" action="<?=base_url()?>trade_form" method="post">

        <strong>Create a Trade Proposal</strong>
        <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>
        <hr>

        <div class="row">
            <div class="col-md-4">
                <h3 class="trade_request_partner_username text-center text-primary"></h3>
                <div class="trade_supplies_parent">
                <?php foreach ($this->supplies as $supply) { ?>
                    <?php if (!$supply['can_trade']) { continue; } ?>
                    <div class="trade_supply_parent row">
                        <div class="col-md-5 col-md-push-1">
                            <label><?php echo $supply['label']; ?></label>
                        </div>
                        <div class="col-md-3">
                            <strong id="partner_trade_supply_current_<?php echo $supply['slug']; ?>"></strong>
                        </div>
                        <div class="col-md-3">
                            <input value="0" min="0" max="10000" id="partner_trade_supply_proposal_<?php echo $supply['slug']; ?>" class="trade_supply_change input form-control pull-right" type="number"/>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
            <div class="col-md-4">
                <h2 class="text-center">Proposed Agreement</h2>
                <p class="lead">
                    Current Treaty:
                    <strong class="current_treaty"></strong>
                </p>
                <p class="lead">Proposed Treaty</p>
                <select class="form-control" id="input_agreement" name="input_agreement">
                    <option value="2">Peace</option>
                    <!-- <option value="3">Passage</option> -->
                </select>

                <p class="lead">Message</p>
                <textarea class="form-control" id="my_message" name="my_message"></textarea>
                
                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <button class="accept_trade_request btn btn-success form-control" type="button">
                            <i class="fas fa-envelope-square"></i>
                            Send Proposal
                        </button>
                    </div>
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <button id="declare_war" class="btn btn-danger form-control" type="button">
                            <i class="fas fa-skull"></i>
                            Declare War
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h3 class="text-center text-primary">Your Supplies</h3>
                <div class="trade_supplies_parent">
                <?php foreach ($this->supplies as $supply) { ?>
                    <?php if (!$supply['can_trade']) { continue; } ?>
                    <div class="trade_supply_parent row">
                        <div class="col-md-5 col-md-push-1">
                            <label><?php echo $supply['label']; ?></label>
                        </div>
                        <div class="col-md-3">
                            <strong id="our_trade_supply_current_<?php echo $supply['slug']; ?>"></strong>
                        </div>
                        <div class="col-md-3">
                            <input value="0" min="0" max="" id="our_trade_supply_offer_<?php echo $supply['slug']; ?>" class="trade_supply_change input form-control pull-right" type="number"/>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>

    </form>
</div>