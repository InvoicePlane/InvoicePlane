<script>
    $().ready(function () {
        $('#btn-submit').click(function () {
            $('#form-settings').submit();
        });
        $("[name='settings[default_country]']").select2({
            placeholder: "<?php _trans('country'); ?>",
            allowClear: true
        });
    });
</script>

<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('settings'); ?></h1>
    <?php $this->layout->load_view('layout/header_buttons', array('hide_cancel_button' => true)); ?>
</div>

<ul id="settings-tabs" class="nav nav-tabs nav-tabs-noborder">
    <li class="active">
        <a data-toggle="tab" href="#settings-general"><?php _trans('general'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-invoices"><?php _trans('invoices'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-quotes"><?php _trans('quotes'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-taxes"><?php _trans('taxes'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-email"><?php _trans('email'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-online-payment"><?php echo lang('online_payment'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-projects-tasks"><?php _trans('projects'); ?></a>
    </li>
    <li>
        <a data-toggle="tab" href="#settings-updates"><?php _trans('updates'); ?></a>
    </li>
</ul>

<form method="post" id="form-settings" enctype="multipart/form-data">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div class="tabbable tabs-below">

        <div class="tab-content">

            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <?php $this->layout->load_view('layout/alerts'); ?>
            </div>

            <div id="settings-general" class="tab-pane active">
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

            <div id="settings-online-payment" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_online_payment'); ?>
            </div>

            <div id="settings-projects-tasks" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_projects_tasks'); ?>
            </div>

            <div id="settings-updates" class="tab-pane">
                <?php $this->layout->load_view('settings/partial_settings_updates'); ?>
            </div>

        </div>

    </div>

</form>
