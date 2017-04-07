<div id="headerbar">
    <h1 class="headerbar-title"><?php echo $project->project_name; ?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a href="<?php echo site_url('tasks/form/'); ?>" class="btn btn-default">
                <i class="fa fa-check-square-o fa-margin"></i><?php _trans('new_task'); ?>
            </a>
            <a href="<?php echo site_url('projects/form/' . $project->project_id); ?>" class="btn btn-default">
                <i class="fa fa-edit"></i> <?php _trans('edit'); ?>
            </a>
            <a class="btn btn-danger"
               href="<?php echo site_url('projects/delete/' . $project->project_id); ?>"
               onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                <i class="fa fa-trash-o"></i> <?php _trans('delete'); ?>
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
                        <strong><?php echo format_client($project); ?></strong>
                    </div>
                    <div class="panel-body">
                        <div class="client-address">
                            <?php $this->layout->load_view('clients/partial_client_address', array('client' => $project)); ?>
                        </div>
                    </div>
                </div>

            <?php else : ?>

                <div class="alert alert-info"><?php _trans('alert_no_client_assigned'); ?></div>

            <?php endif; ?>

        </div>
        <div class="col-xs-12 col-md-8">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php _trans('tasks'); ?>
                </div>
                <div class="panel-body no-padding">

                    <div class="table-responsive">
                        <table class="table table-striped no-margin">

                            <thead>
                            <tr>
                                <th><?php _trans('task_name'); ?></th>
                                <th><?php _trans('status'); ?></th>
                                <th><?php _trans('task_finish_date'); ?></th>
                                <th><?php _trans('project'); ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($tasks as $task) { ?>
                                <tr>
                                    <td>
                                        <?php echo anchor('tasks/form/' . $task->task_id, htmlsc($task->task_name)) ?>
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
                                    <td><?php _htmlsc($task->project_name); ?></td>
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
    </div>

</div>
<?php var_dump($task); ?>
