    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th><?php _trans('status'); ?></th>
                <th><?php _trans('task_name'); ?></th>
                <th><?php _trans('task_finish_date'); ?></th>
                <th><?php _trans('project'); ?></th>
                <th class="amount last"><?php _trans('task_price'); ?></th>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
<?php
foreach ($tasks as $task) {
    $label_class = (isset($task_statuses[$task->task_status]['class'])) ? $task_statuses[$task->task_status]['class'] : '';
?>
                <tr>
                    <td>
                        <span class="label <?php echo $label_class; ?>">
                            <?php if (isset($task_statuses[$task->task_status]['label'])) {
                                echo $task_statuses[$task->task_status]['label'];
                            } ?>
                        </span>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($task->task_name, ENT_COMPAT); ?>
                    </td>
                    <td>
                        <div class="<?php echo $task->is_overdue ? 'text-danger' : ''; ?>">
                            <?php echo date_from_mysql($task->task_finish_date); ?>
                        </div>
                    </td>
                    <td>
                        <?php echo empty($task->project_id) ? '' : anchor('projects/view/' . $task->project_id, htmlsc($task->project_name)); ?>
                    </td>
                    <td class="amount last">
                        <?php echo format_currency($task->task_price); ?>
                    </td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('tasks/form/' . $task->task_id); ?>"
                                       title="<?php _trans('edit'); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                    </a>
                                </li>
<?php
    if ( ! ($task->task_status == 4 && $this->config->item('enable_invoice_deletion') !== true)) {
?>
                                <li>
                                    <form action="<?php echo site_url('tasks/delete/' . $task->task_id); ?>"
                                          method="POST">
                                        <?php _csrf_field(); ?>
                                        <button type="submit" class="dropdown-button"
                                                onclick="return confirm('<?php echo $task->task_status == 4 ? trans('alert_task_delete') : trans('delete_record_warning') ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                        </button>
                                    </form>
                                </li>
<?php
    } // end if
?>
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
