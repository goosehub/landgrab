<div id="trade_block" class="center_block">
    <form id="trade_form" action="<?=base_url()?>trade_form" method="post">

        <button type="button" class="exit_center_block btn btn-default btn-sm">
          <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        </button>

        <div id="trade_form_result" class="trade_block_toggle">
            <br><div class="alert alert-wide alert-error"><strong id="trade_form_result_message"></strong></div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <h3 class="text-center text-primary">Thatcher's Supplies</h3>
                <div id="their_trade_supply_list" class="row"></div>
            </div>
            <div class="col-md-4">
                <h2>Proposed Agreement</h2>
                <p class="lead">
                    Current Diplomatic Agreement:
                    <strong id="current_agreement" class="text-danger">War!</strong>
                </p>
                <p class="lead">Proposed Diplomatic Agreement</p>
                <select class="form-control" id="input_agreement" name="input_agreement">
                    <option value="2">Peace</option>
                    <option value="1">War!</option>
                    <option value="3">Passage</option>
                </select>
                <p class="lead">Thatcher's Message</p>
                <textarea class="form-control" id="their_message" name="their_message">Accept or I will destroy you!</textarea>

                <p class="lead">Message</p>
                <textarea class="form-control" id="my_message" name="my_message"></textarea>
                
                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <button class="accept_trade_request btn btn-success form-control">
                            <i class="fas fa-check"></i>
                            Accept
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button class="reject_trade_request btn btn-warning form-control">
                            <i class="fas fa-times"></i>
                            Reject
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button class="reject_trade_request btn btn-danger form-control">
                            <i class="fas fa-skull"></i>
                            War
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h3 class="text-center text-primary">Your Supplies</h3>
                <div id="my_trade_supply_list" class="row"></div>
            </div>
        </div>

    </form>
</div>