<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th><?php _trans('label'); ?></th>
            <th><?php _trans('table'); ?></th>
            <th><?php _trans('position'); ?></th>
            <th><?php _trans('type'); ?></th>
            <th><?php _trans('order'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
<?php
foreach ($custom_fields as $custom_field)
{
    $alpha    = str_replace("-", "_", strtolower($custom_field->custom_field_type));
    $position = $positions[ $custom_field->custom_field_table ][ $custom_field->custom_field_location ];
?>
            <tr>
                <td><?php _htmlsc($custom_field->custom_field_label); ?></td>
                <td><?php _trans($custom_tables[$custom_field->custom_field_table]); ?></td>
                <td><?php echo $position; ?></td>
                <td><?php _trans($alpha); ?></td>
                <td><?php echo $custom_field->custom_field_order; ?></td>
                <td>
                    <div class="options btn-group btn-group-sm">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
<?php
    if (in_array($custom_field->custom_field_type, $custom_value_fields))
    {
?>
                        <a href="<?php echo site_url('custom_values/field/' . $custom_field->custom_field_id); ?>"
                           class="btn btn-default">
                            <i class="fa fa-list fa-margin"></i> <?php _trans('values'); ?>
                        </a>
<?php
    }
?>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('custom_fields/form/' . $custom_field->custom_field_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <form action="<?php echo site_url('custom_fields/delete/' . $custom_field->custom_field_id); ?>"
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
