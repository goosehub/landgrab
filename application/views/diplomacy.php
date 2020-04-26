<?php if ($account) { ?>
<div id="diplomacy_block" class="center_block">
    <strong>Diplomacy</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <hr>

    <div class="row">
        <div class="col-md-9">
            <select class="form-control" id="select_account_for_diplomacy">
                <?php foreach ($active_accounts as $an_account) { ?>
                    <?php if ($an_account['id'] === $account['id']) { continue; } ?>
                    <option value="<?= $an_account['id'] ?>"><?= $an_account['username'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <button id="start_new_diplomacy" class="btn btn-action form-control">
                <i class="fas fa-plus"></i>
                Start Diplomacy
            </button>
        </div>
    </div>

    <div>
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#trade_requests_received" aria-controls="home" role="tab" data-toggle="tab">Received Proposals</a></li>
        <li role="presentation"><a href="#trade_requests_sent" aria-controls="profile" role="tab" data-toggle="tab">Sent Proposals</a></li>
        <li role="presentation"><a href="#current_treaties" aria-controls="messages" role="tab" data-toggle="tab">Current Treaties</a></li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane well active" id="trade_requests_received"></div>
        <div role="tabpanel" class="tab-pane well" id="trade_requests_sent"></div>
        <div role="tabpanel" class="tab-pane well" id="current_treaties"></div>
      </div>
    </div>
</div>
<?php } ?>