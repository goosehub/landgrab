<?php if ($account) { ?>
<div id="diplomacy_block" class="center_block">
    <strong>Diplomacy</strong>
    <button type="button" class="exit_center_block btn btn-default btn-sm pull-right">
      <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
    </button>

    <hr>

    <div class="row">
        <div class="col-md-9">
            <select class="form-control" id="input_agreement" name="input_agreement"">
                <option value="1">Thatcher</option>
                <option value="2">Modi</option>
                <option value="3">Elizabeth</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="open_trade_request btn btn-action form-control" trade-id="0">
                <i class="fas fa-plus"></i>
                Start Diplomacy
            </button>
        </div>
    </div>

    <hr>

    <p class="lead">Unread</p>
    <div class="row">
        <div class="col-md-3">
            Request by Thatcher to You
        </div>
        <div class="col-md-3">
            <span class="text-primary">Trade</span>
        </div>
        <div class="col-md-3">
            <span class="text-warning">Pending</span>
        </div>
        <div class="col-md-3">
            <button class="open_trade_request btn btn-primary form-control" trade-id="0">
                <i class="fas fa-sign-out-alt"></i>
                Open
            </button>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-3">
            Request by You to Modi
        </div>
        <div class="col-md-3">
            <span class="text-primary">Trade and Rights of Passge</span>
        </div>
        <div class="col-md-3">
            <span class="text-danger">Rejected</span>
        </div>
        <div class="col-md-3">
            <button class="open_trade_request btn btn-primary form-control" trade-id="0">
                <i class="fas fa-sign-out-alt"></i>
                Open
            </button>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-3">
            Request by John to You
        </div>
        <div class="col-md-3">
            <span class="text-danger">WAR</span>
        </div>
        <div class="col-md-3">
            <span class="text-danger">Declared</span>
        </div>
        <div class="col-md-3">
            <button class="open_trade_request btn btn-primary form-control" trade-id="0">
                <i class="fas fa-sign-out-alt"></i>
                Open
            </button>
        </div>
    </div>
    <hr>
    <p class="lead">Read</p>
    <div class="row">
        <div class="col-md-3">
            Request by You to Trudo
        </div>
        <div class="col-md-3">
            <span class="text-primary">Rights of Passge</span>
        </div>
        <div class="col-md-3">
            <span class="text-success">Accepted</span>
        </div>
        <div class="col-md-3">
            <button class="open_trade_request btn btn-primary form-control" trade-id="0">
                <i class="fas fa-sign-out-alt"></i>
                Open
            </button>
        </div>
    </div>
</div>
<?php } ?>