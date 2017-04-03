<div id="headerbar">
    <h1 class="headerbar-title"><?php echo trans('tasks'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('tasks/form'); ?>">
            <i class="fa fa-plus"></i> <?php echo trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('tasks/index'), 'mdl_tasks'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo trans('status'); ?></th>
                <th><?php echo trans('task_name'); ?></th>
                <th><?php echo trans('task_finish_date'); ?></th>
                <th><?php echo trans('project'); ?></th>
                <th><?php echo trans('task_price'); ?></th>
                <th><?php echo trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($tasks as $task) { ?>
                <tr>
                    <td>
                        <span class="label <?php echo $task_statuses["$task->task_status"]['class']; ?>">
                            <?php echo $task_statuses["$task->task_status"]['label']; ?>
                        </span>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($task->task_name); ?>
                    </td>
                    <td>
                        <div class="<?php if ($task->is_overdue) { ?>text-danger<?php } ?>">
                            <?php echo date_from_mysql($task->task_finish_date); ?>
                        </div>
                    </td>
                    <td>
                        <?php echo !empty($task->project_id) ? anchor('projects/view/' . $task->project_id, $task->project_name) : ''; ?>
                    </td>
                    <td>
                        <?php echo format_currency($task->task_price); ?>
                    </td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php echo trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('tasks/form/' . $task->task_id); ?>"
                                       title="<?php echo trans('edit'); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php echo trans('edit'); ?>
                                    </a>
                                </li>
                                <?php if (!($task->task_status == 4 && $this->config->item('enable_invoice_deletion') !== true)) : ?>
                                    <li>
                                        <a href="<?php echo site_url('tasks/delete/' . $task->task_id); ?>"
                                           title="<?php echo trans('delete'); ?>"
                                           onclick="return confirm('<?php echo $task->task_status == 4 ? trans('alert_task_delete') : trans('delete_record_warning') ?>')"
                                        >
                                            <i class="fa fa-trash-o fa-margin"></i> <?php echo trans('delete'); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>


                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>

    </div>

</div>
