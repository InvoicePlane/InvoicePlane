<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('custom_fields'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('custom_fields/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('custom_fields/index'), 'mdl_custom_fields'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php _trans('table'); ?></th>
            <th><?php _trans('label'); ?></th>
            <th><?php _trans('type'); ?></th>
            <th><?php _trans('order'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($custom_fields as $custom_field) { ?>
            <?php $alpha = str_replace("-", "_", strtolower($custom_field->custom_field_type)); ?>
            <tr>
                <td><?php echo lang($custom_tables[$custom_field->custom_field_table]); ?></td>
                <td><?php _htmlsc($custom_field->custom_field_label); ?></td>
                <td><?php _trans($alpha); ?></td>
                <td><?php echo $custom_field->custom_field_order; ?></td>
                <td>
                    <div class="options btn-group btn-group-sm">
                        <?php if (in_array($custom_field->custom_field_type, $custom_value_fields)) { ?>
                            <a href="<?php echo site_url('custom_values/field/' . $custom_field->custom_field_id); ?>"
                               class="btn btn-default">
                                <i class="fa fa-list fa-margin"></i> <?php _trans('values'); ?>
                            </a>
                        <?php } ?>
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">

                            <li>
                                <a href="<?php echo site_url('custom_fields/form/' . $custom_field->custom_field_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('custom_fields/delete/' . $custom_field->custom_field_id); ?>"
                                   onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>

    </table>

</div>
