<script type="text/javascript">
    $().ready(function () {
        $('#btn-submit').click(function () {
            $('#form-settings').submit();
        });
        $("[name='settings[default_country]']").select2({allowClear: true});
    });
</script>

<div id="headerbar">
    <h1><?php echo lang('settings'); ?></h1>
    <?php $this->layout->load_view('layout/header_buttons'); ?>
</div>

<ul id="settings-tabs" class="nav nav-tabs nav-tabs-noborder">
    <li class="active">
        <a data-toggle="tab" href="#settings-general"><?php echo lang('general'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-invoices"><?php echo lang('invoices'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-quotes"><?php echo lang('quotes'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-taxes"><?php echo lang('taxes'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-email"><?php echo lang('email'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-merchant"><?php echo lang('merchant_account'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-updates"><?php echo lang('updates'); ?></a>
    </li>
</ul>

<form method="post" id="form-settings" enctype="multipart/form-data">

    <div class="tabbable tabs-below">

        <div class="tab-content col-xs-12 col-md-8 col-lg-6">

            <div id="settings-general" class="tab-pane active">
                <?php $this->layout->load_view('layout/alerts'); ?>

                <?php $this->layout->load_view('settings/partial_settings_general'); ?>
            </div>

            <div id="settings-invoices" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_invoices'); ?>
            </div>

            <div id="settings-quotes" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_quotes'); ?>
            </div>

            <div id="settings-taxes" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_taxes'); ?>
            </div>

            <div id="settings-email" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_email'); ?>
            </div>

            <div id="settings-merchant" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_merchant'); ?>
            </div>

            <div id="settings-updates" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_updates'); ?>
            </div>

        </div>

    </div>

</form>
