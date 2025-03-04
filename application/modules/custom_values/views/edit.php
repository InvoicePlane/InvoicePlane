<?php
$href = site_url('custom_fields/form/' . $value->custom_field_id);
$link = anchor($href, '<i class="fa fa-edit fa-margin"></i> ' . htmlsc($value->custom_field_label), ' class="btn btn-sm btn-default"');
$alpha = strtr(strtolower($value->custom_field_type), ['-' => '_']);
$table = strtr($value->custom_field_table, ['ip_' => '', '_custom' => '']);
?>
<form method="post">

    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('custom_values_edit'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
        <div class="headerbar-item pull-right">
            <a href="<?php echo site_url('custom_values/field/' . $value->custom_field_id) ?>" class="btn btn-sm btn-default">
                                <i class="fa fa-eye fa-margin"></i> <?php _trans('values'); ?></a>
        </div>
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
                    <label for="custom_values_value"><?php _trans('label'); ?>:</label>
                    <input type="text" name="custom_values_value" id="custom_values_value" class="form-control"
                           value="<?php _htmlsc($value->custom_values_value); ?>" required>
                </div>
                <hr>

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

<?php $this->layout->load_view('layout/partial/custom_field_usage_list', ['custom_field_table' => $value->custom_field_table]); ?>

        </div>
    </div>

</form>
