<div id="headerbar">
    <h1 class="headerbar-title"><?php echo $project->project_name; ?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a href="<?php echo site_url('tasks/form/'); ?>" class="btn btn-default">
                <i class="fa fa-check-square-o fa-margin"></i><?php echo trans('new_task'); ?>
            </a>
            <a href="<?php echo site_url('projects/form/' . $project->project_id); ?>" class="btn btn-default">
                <i class="fa fa-edit"></i> <?php echo trans('edit'); ?>
            </a>
            <a class="btn btn-danger"
               href="<?php echo site_url('projects/delete/' . $project->project_id); ?>"
               onclick="return confirm('<?php echo trans('delete_record_warning'); ?>');">
                <i class="fa fa-trash-o"></i> <?php echo trans('delete'); ?>
            </a>
        </div>
    </div>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-4">

            <?php if (!empty($project->client_name)) : ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo format_client($project); ?>
                    </div>
                    <div class="panel-body">
                        <p>
                            <?php echo ($project->client_address_1) ? $project->client_address_1 . '<br>' : ''; ?>
                            <?php echo ($project->client_address_2) ? $project->client_address_2 . '<br>' : ''; ?>
                            <?php echo ($project->client_city) ? $project->client_city : ''; ?>
                            <?php echo ($project->client_state) ? $project->client_state : ''; ?>
                            <?php echo ($project->client_zip) ? $project->client_zip : ''; ?>
                            <?php echo ($project->client_country) ? '<br>' . $project->client_country : ''; ?>
                        </p>
                    </div>
                </div>

            <?php else : ?>

                <div class="alert alert-info"><?php echo trans('alert_no_client_assigned'); ?></div>

            <?php endif; ?>

        </div>
        <div class="col-xs-12 col-md-8">

            <div class="table-responsive">
                <table class="table table-striped">

                    <thead>
                    <tr>
                        <th><?php echo trans('task_name'); ?></th>
                        <th><?php echo trans('status'); ?></th>
                        <th><?php echo trans('task_finish_date'); ?></th>
                        <th><?php echo trans('project'); ?></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($tasks as $task) { ?>
                        <tr>
                            <td>
                                <?php echo anchor('tasks/form/' . $task->task_id, $task->task_name) ?>
                            </td>
                            <td>
                                <span class="label <?php echo $task_statuses["$task->task_status"]['class']; ?>">
                                    <?php echo $task_statuses["$task->task_status"]['label']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?php if ($task->is_overdue) { ?>text-danger<?php } ?>">
                                    <?php echo date_from_mysql($task->task_finish_date); ?>
                                </span>
                            </td>
                            <td><?php echo $task->project_name; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>

                </table>

                <?php if (empty($tasks)) : ?>
                    <br>
                    <div class="alert alert-info"><?php echo trans('alert_no_tasks_found') ?></div>
                <?php endif; ?>

            </div>

        </div>
    </div>

</div>
<?php var_dump($task); ?>
