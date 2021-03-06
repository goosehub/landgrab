<?php if ($account) { ?>
<div id="diplomacy_block" class="center_block">
    <strong>Diplomacy</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <hr>

    <div class="row">
        <div class="col-md-2">
            <select class="form-control" id="select_account_for_diplomacy">
                <?php foreach ($active_accounts as $an_account) { ?>
                    <?php if ($an_account['id'] === $account['id']) { continue; } ?>
                    <option value="<?= $an_account['id'] ?>"><?= $an_account['username'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <button id="start_new_diplomacy" class="btn btn-action form-control">
                <i class="fas fa-handshake"></i>
                Start Diplomacy
            </button>
        </div>
        <div class="col-md-8">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#trade_requests_received" aria-controls="home" role="tab" data-toggle="tab">Received</a></li>
                <li role="presentation"><a href="#trade_requests_sent" aria-controls="profile" role="tab" data-toggle="tab">Sent</a></li>
                <li role="presentation"><a href="#current_treaties" aria-controls="messages" role="tab" data-toggle="tab">Treaties</a></li>
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane well active" id="trade_requests_received">
            <small class="text-info">Showing the last <?= TRADE_SHOW_HOURS ?> hours of requests</small>
            <div class="row hidden-sm hidden-xs">
              <div class="col-md-2"><label>Leader</label></div>
              <div class="col-md-2"><label>Message</label></div>
              <div class="col-md-2"><label>Reply</label></div>
              <div class="col-md-2"><label>Treaty</label></div>
              <div class="col-md-2"><label>Created</label></div>
              <div class="col-md-1"><label>Status</label></div>
              <div class="col-md-1"></div>
            </div>
            <hr>
            <div id="trade_requests_received_listing"></div>
        </div>
        <div role="tabpanel" class="tab-pane well" id="trade_requests_sent">
            <small class="text-info">Showing the last <?= TRADE_SHOW_HOURS ?> hours of requests</small>
            <div class="row hidden-sm hidden-xs">
              <div class="col-md-2"><label>Leader</label></div>
              <div class="col-md-2"><label>Message</label></div>
              <div class="col-md-2"><label>Reply</label></div>
              <div class="col-md-2"><label>Treaty</label></div>
              <div class="col-md-2"><label>Created</label></div>
              <div class="col-md-1"><label>Status</label></div>
              <div class="col-md-1"></div>
            </div>
            <hr>
            <div id="trade_requests_sent_listing"></div>
        </div>
        <div role="tabpanel" class="tab-pane well" id="current_treaties">
            
        </div>
    </div>
</div>
<?php } ?>