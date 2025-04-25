<div class="table-responsive">

    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th><?php _trans('id'); ?></th>
            <th><?php _trans('field'); ?></th>
            <th><?php _trans('elements'); ?></th>
            <th><?php _trans('table'); ?></th>
            <th><?php _trans('position'); ?></th>
            <th><?php _trans('type'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
<?php
foreach ($custom_values as $custom_values) {
    $href     = site_url('custom_fields/form/' . $custom_values->custom_field_id);
    $alpha    = str_replace("-", "_", strtolower($custom_values->custom_field_type));
    $position = $positions[ $custom_values->custom_field_table ][ $custom_values->custom_field_location ];
?>
            <tr>
                <td><?php echo anchor($href, $custom_values->custom_field_id, ' title="' . trans('edit') . '"'); ?></td>
                <td><?php echo anchor($href, '<i class="fa fa-edit fa-margin"></i> ' . htmlsc($custom_values->custom_field_label), ' class="btn btn-sm btn-default"'); ?></td>
                <td><?php echo $custom_values->count; ?></td>
                <td><?php _trans($custom_tables[$custom_values->custom_field_table]); ?></td>
                <td><?php echo $position; ?></td>
                <td><?php _trans($alpha); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('custom_values/field/' . $custom_values->custom_field_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?> (<?php _trans('values'); ?>)
                                </a>
                            </li>
                            <li>
                                <form action="<?php echo site_url('custom_fields/delete/' . $custom_values->custom_field_id); ?>"
                                      method="POST">
                                    <?php _csrf_field(); ?>
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
<?php
}
?>
        </tbody>
    </table>
</div>
