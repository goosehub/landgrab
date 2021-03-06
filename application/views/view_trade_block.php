<div id="view_trade_block" class="center_block">
    <strong>Create a Diplomatic Offer</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <div class="row flex_columns">
        <div class="col-md-4 mobile_trade_middle">
            <h3 class="trade_request_partner_username text-center text-primary"></h3>
            <div class="view_trade_supplies_parent view_trade_supplies_of_partner well">
            <?php foreach ($this->supplies as $supply) { ?>
                <?php if (!$supply['can_trade']) { continue; } ?>
                <div class="view_trade_supply_parent row" data-id="<?= $supply['id']; ?>">
                    <div class="col-xs-5 col-xs-push-1">
                        <label><?= $supply['label']; ?></label>
                    </div>
                    <div class="col-xs-3">
                        <!-- <p class="partner_trade_supply_current_<?= $supply['slug']; ?>"></p> -->
                    </div>
                    <div class="col-xs-3">
                        <strong id="view_partner_trade_supply_proposal_<?= $supply['id']; ?>" class="pull-right"></strong>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
        <div class="col-md-4 mobile_trade_top">
            <h2 class="text-center">Diplomacy</h2>

            <p class="current_treaty_parent lead">
                <span class="text-primary">Current Treaty:</span>
                <span class="current_treaty"></span>
            </p>

            <p class="proposed_treaty_parent lead">
                <span class="text-primary">Proposed Treaty:</span>
                <span id="proposed_treaty" class=""></span>
            </p>

            <div id="view_trade_request_message">
                <p class="lead text-primary">Diplomatic Message:</p>
                <p id="request_message"></p>
            </div>

            <div id="view_trade_response_message">
                <p class="lead text-primary">Diplomatic Reply:</p>
                <p id="response_message"></p>
            </div>

            <div id="view_trade_your_reply">
                <p class="lead">Your reply:</p>
                <textarea class="form-control" id="view_input_trade_message_reply" name="input_trade_message_reply"></textarea>
            </div>

            <hr>

            <p id="offer_expire_hours_parent">
                This offer expires in <span id="offer_expire_hours" class="text-primary"></span> hours.
            </p>

            <div id="view_trade_actions" class="row">
                <div class="col-md-4 col-xs-5">
                    <button id="accept_trade_request" class="btn btn-success form-control" type="button">
                        <i class="fas fa-check"></i>
                        Accept Offer
                    </button>
                </div>
                <div class="col-md-4 col-xs-2">
                </div>
                <div class="col-md-4 col-xs-5">
                    <button id="reject_trade_request" class="btn btn-danger form-control" type="button">
                        <i class="fas fa-times"></i>
                        Reject Offer
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4 mobile_trade_bottom">
            <h3 class="text-center text-primary">Your Offer</h3>
            <div class="view_trade_supplies_parent view_trade_supplies_of_own well">
            <?php foreach ($this->supplies as $supply) { ?>
                <?php if (!$supply['can_trade']) { continue; } ?>
                <div class="view_trade_supply_parent row" data-id="<?= $supply['id']; ?>">
                    <div class="col-xs-5 col-xs-push-1">
                        <label><?= $supply['label']; ?></label>
                    </div>
                    <div class="col-xs-3">
                        <p class="our_trade_supply_current_<?= $supply['slug']; ?>"></p>
                    </div>
                    <div class="col-xs-3">
                        <strong id="view_our_trade_supply_offer_<?= $supply['id']; ?>" class="pull-right"></strong>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>
</div>