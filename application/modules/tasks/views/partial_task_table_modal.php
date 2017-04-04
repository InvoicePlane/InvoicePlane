<div class="table-responsive">
    <table id="tasks_table" class="table table-bordered table-striped no-margin">
        <tr>
            <th>&nbsp;</th>
            <th><?php echo lang('project_name'); ?></th>
            <th><?php echo lang('task_name'); ?></th>
            <th><?php echo lang('task_finish_date'); ?></th>
            <th><?php echo lang('task_description'); ?></th>
            <th class="text-right">
                <?php echo lang('task_price'); ?></th>
        </tr>

        <?php foreach ($tasks as $task) { ?>
            <tr class="task-row">
                <td class="text-left">
                    <input type="checkbox" class="modal-task-id" name="task_ids[]"
                           id="task-id-<?php echo $task->task_id ?>" value="<?php echo $task->task_id; ?>">
                </td>
                <td nowrap class="text-left">
                    <b><?php echo isset($task->project_name) ? htmlsc($task->project_name) : ''; ?></b>
                </td>
                <td>
                    <b><?php _htmlsc($task->task_name); ?></b>
                </td>
                <td>
                    <b><?php echo date_from_mysql($task->task_finish_date); ?></b>
                </td>
                <td>
                    <?php echo nl2br(htmlsc($task->task_description)); ?>
                </td>
                <td class="amount">
                    <?php echo format_currency($task->task_price); ?>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>