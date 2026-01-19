                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th><?php _trans('id'); ?></th>
                            <th><?php _trans('label'); ?></th>
                            <th><?php _trans('options'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
<?php
foreach ($elements as $element) {
?>
                        <tr>
                            <td><?php echo $element->custom_values_id; ?></td>
                            <td><?php _htmlsc($element->custom_values_value); ?></td>
                            <td>
                                <div class="options btn-group">
                                    <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                       href="#">
                                        <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?php echo site_url('custom_values/edit/' . $element->custom_values_id); ?>">
                                                <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <form action="<?php echo site_url('custom_values/delete/' . $element->custom_values_id); ?>"
                                                  method="POST">
                                                <?php _csrf_field(); ?>
                                                <input type="hidden" name="custom_field_id" value="<?php echo $id; ?>">
                                                <button type="submit" class="dropdown-button"
                                                        onclick="return confirm(`<?php _trans('delete_record_warning'); ?>`);">
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

