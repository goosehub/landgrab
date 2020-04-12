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

    <hr>

    <p class="lead">Unread</p>
    <section id="unread_trade_requests"></section>
    <hr>
    <p class="lead">Read</p>
    <section id="unread_trade_requests"></section>
</div>
<?php } ?>