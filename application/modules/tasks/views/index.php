<div id="headerbar">
    <h1><?php echo lang('tasks'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('tasks/form'); ?>"><i
                class="fa fa-plus"></i> <?php echo lang('new'); ?></a>
    </div>

    <div class="pull-right">
        <?php echo pager(site_url('tasks/index'), 'mdl_tasks'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo lang('task_name'); ?></th>
                <th><?php echo lang('status'); ?></th>
                <th><?php echo lang('task_finish_date'); ?></th>
                <th><?php echo lang('project'); ?></th>
                <th><?php echo lang('task_price'); ?></th>
                <th><?php echo lang('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($tasks as $task) { ?>
                <tr>
                    <td><?php echo $task->task_name; ?></td>
                    <td><?php echo $task_statuses["$task->task_status"]["label"]; ?></td>
                    <td>
                    <span class="<?php if ($task->is_overdue) { ?>font-overdue<?php } ?>">
                    <?php echo date_from_mysql($task->task_finish_date); ?>
                    </span>
                    </td>
                    <td><?php echo $task->project_name; ?></td>
                    <td><?php echo format_currency($task->task_price); ?></td>
                    <td>
                        <a href="<?php echo site_url('tasks/form/' . $task->task_id); ?>"
                           title="<?php echo lang('edit'); ?>"><i class="fa fa-edit fa-margin"></i></a>
                        <a href="<?php echo site_url('tasks/delete/' . $task->task_id); ?>"
                           title="<?php echo lang('delete'); ?>"
                           onclick="return confirm('<?php if ($task->task_status == 4) {
                               echo lang('alert_task_delete') . ' ';
                           }
                           echo lang('delete_record_warning'); ?>');">
                            <i class="fa fa-trash-o fa-margin"></i></a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>
</div>