<?php
$href = site_url('custom_fields/form/' . $field->custom_field_id);
$link = anchor($href, '<i class="fa fa-edit fa-margin"></i> ' . htmlsc($field->custom_field_label), ' class="btn btn-default"');
$alpha = strtr(strtolower($field->custom_field_type), ['-' => '_']);
?>
<form method="post">

    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('custom_values_new'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
        <div class="visible-sm visible-md visible-lg headerbar-item pull-right">
            <div class="badge"><?php _trans('table'); ?>: <?php _trans($table); ?></div>
            <div class="badge"><?php _trans('position'); ?>: <?php echo $position; ?></div>
            <div class="badge"><?php _trans('type'); ?>: <?php _trans($alpha); ?></div>
            <?php _trans('field'); ?>: <?php echo $link; ?>
        </div>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

               <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <label for="custom_values_value"><?php _trans('value'); ?>:</label>
                    <input type="text" class="form-control" name="custom_values_value" id="custom_values_value" required>
                </div>

                <div class="row visible-xs">
                    <div class="col-xs-12">
                        <div class="form-group"><?php _trans('field'); ?>: <?php echo $link; ?></div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group badge"><?php _trans('table'); ?>: <?php _trans($table); ?></div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group badge"><?php _trans('position'); ?>: <?php echo $position; ?></div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group badge"><?php _trans('type'); ?>: <?php _trans($alpha); ?></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</form>
