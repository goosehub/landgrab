<div id="view_trade_block" class="center_block">
    <strong>Create a Trade Proposal</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>
    <hr>

    <div class="row">
        <div class="col-md-4">
            <h3 class="trade_request_partner_username text-center text-primary"></h3>
            <div class="view_trade_supplies_parent">
            <?php foreach ($this->supplies as $supply) { ?>
                <?php if (!$supply['can_trade']) { continue; } ?>
                <div class="view_trade_supply_parent row" data-id="<?= $supply['id']; ?>">
                    <div class="col-md-5 col-md-push-1">
                        <label><?= $supply['label']; ?></label>
                    </div>
                    <div class="col-md-3">
                        <strong id="view_partner_trade_supply_current_<?= $supply['slug']; ?>"></strong>
                    </div>
                    <div class="col-md-3">
                        <p id="view_partner_trade_supply_proposal_<?= $supply['slug']; ?>" class="pull-right"></p>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
        <div class="col-md-4">
            <h2 class="text-center">Proposed treaty</h2>

            <p class="lead">
                Current Treaty:
                <span class="current_treaty"></span>
            </p>

            <p class="lead">Proposed Treaty: <span id="proposed_treaty" class=""></span></p>

            <p class="lead">Message:</p>
            <p id="request_message"></p>

            <p class="lead">Your reply:</p>
            <textarea class="form-control" id="view_input_trade_message_reply" name="input_trade_message_reply"></textarea>
            <hr>

            <p>this offer expires in <span id="offer_expire_hours" class="text-primary"></span> hours.</p>

            <div id="view_trade_actions" class="row">
                <div class="col-md-4">
                    <button id="view_send_trade_request" class="btn btn-success form-control" type="button">
                        <i class="fas fa-check"></i>
                        Accept Proposal
                    </button>
                </div>
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                    <button id="view_send_trade_request" class="btn btn-danger form-control" type="button">
                        <i class="fas fa-times"></i>
                        Reject Proposal
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <h3 class="text-center text-primary">Your Supplies</h3>
            <div class="view_trade_supplies_parent">
            <?php foreach ($this->supplies as $supply) { ?>
                <?php if (!$supply['can_trade']) { continue; } ?>
                <div class="view_trade_supply_parent row" data-id="<?= $supply['id']; ?>">
                    <div class="col-md-5 col-md-push-1">
                        <label><?= $supply['label']; ?></label>
                    </div>
                    <div class="col-md-3">
                        <strong id="view_our_trade_supply_current_<?= $supply['slug']; ?>"></strong>
                    </div>
                    <div class="col-md-3">
                        <p id="view_our_trade_supply_offer_<?= $supply['slug']; ?>" class="pull-right"></p>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>
</div>