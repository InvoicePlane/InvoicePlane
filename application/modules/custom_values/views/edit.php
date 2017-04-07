<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('custom_values_edit'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <label for="custom_values_label"><?php _trans('field'); ?></label>
                    <input type="text" class="form-control" id="custom_values_label"
                           value="<?php _htmlsc($value->custom_field_label); ?>" disabled="disabled"/>
                </div>

                <div class="form-group">
                    <label for="custom_values_value"><?php _trans('label'); ?></label>
                    <input type="text" name="custom_values_value" id="custom_values_value" class="form-control"
                           value="<?php _htmlsc($value->custom_values_value); ?>">
                </div>
            </div>
        </div>

    </div>

</form>
