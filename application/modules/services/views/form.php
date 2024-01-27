<form method="post" class="form-horizontal">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('service_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label class="control-label">
                    <?php _trans('service_name'); ?>
                </label>
            </div>
	    <div class="col-xs-12 col-sm-6">
                <?php if (isset($client_id) && $client_id) { ?>
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id; ?>">
                <?php } ?>
                <input type="text" name="service_name" id="service_name" class="form-control"
                       value="<?php echo $this->mdl_services->form_value('service_name', true); ?>">
            </div>
        </div>

    </div>

</form>
