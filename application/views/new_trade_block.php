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
                            <label><?= $supply['label']; ?></label>
                        </div>
                        <div class="col-md-3">
                            <strong id="partner_trade_supply_current_<?= $supply['slug']; ?>"></strong>
                        </div>
                        <div class="col-md-3">
                            <input value="0" min="0" max="10000" data-id="<?= $supply['id']; ?>" id="partner_trade_supply_proposal_<?= $supply['slug']; ?>" class="partner_supply_trade trade_supply_change input form-control pull-right" type="number"/>
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

                <p class="lead">Proposed Treaty</p>
                <select class="form-control" id="input_treaty" name="input_treaty">
                    <option value="2">Peace</option>
                    <!-- <option value="3">Passage</option> -->
                </select>

                <p class="lead">Message</p>
                <textarea class="form-control" id="input_trade_message" name="input_trade_message"></textarea>
                
                <hr>

                <p>Offered supplies are held until the treaty is rejected or expires in <?= TRADE_EXPIRE_HOURS; ?> hours.</p>

                <div class="row">
                    <div class="col-md-4">
                        <button id="send_trade_request" class="btn btn-success form-control" type="button">
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
                            <label><?= $supply['label']; ?></label>
                        </div>
                        <div class="col-md-3">
                            <strong id="our_trade_supply_current_<?= $supply['slug']; ?>"></strong>
                        </div>
                        <div class="col-md-3">
                            <input value="0" min="0" max="" data-id="<?= $supply['id']; ?>" id="our_trade_supply_offer_<?= $supply['slug']; ?>" class="own_supply_trade trade_supply_change input form-control pull-right" type="number"/>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>

    </form>
</div>