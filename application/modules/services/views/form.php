<form method="post">

    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('service_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <?php if (isset($client_id) && $client_id) { ?>
                <input type="hidden" name="client_id" id="client_id" value="<?php echo html_escape($client_id); ?>">
                <?php } ?>

                <div class="form-group">
                    <label for="service_name">
                        <?php _trans('service_name'); ?>
                    </label>
                    <input type="text" name="service_name" id="service_name" class="form-control"
                           value="<?php echo $this->mdl_services->form_value('service_name', true); ?>" required>
                </div>

            </div>
        </div>

    </div>

</form>


