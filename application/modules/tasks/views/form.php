<?php
if ($this->mdl_tasks->form_value('task_id') && $this->mdl_tasks->form_value('task_status') == 4) :
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#task-form').find(':input').prop('disabled', 'disabled');
            $('#btn-submit').hide();
            $('#btn-cancel').prop('disabled', false);
        });
    </script>
<?php endif ?>

<form method="post" id="task-form">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('tasks_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <?php if ($this->mdl_tasks->form_value('task_id') && $this->mdl_tasks->form_value('task_status') == 4) : ?>
            <div class="alert alert-warning small"><?php echo trans('info_task_readonly') ?></div>
        <?php endif ?>

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php if ($this->mdl_tasks->form_value('task_id')) : ?>
                            #<?php echo $this->mdl_tasks->form_value('task_id'); ?>&nbsp;
                            <?php echo $this->mdl_tasks->form_value('task_name', true); ?>
                        <?php else : ?>
                            <?php _trans('new_task'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="task_name"><?php _trans('task_name'); ?></label>
                            <input type="text" name="task_name" id="task_name" class="form-control"
                                   value="<?php echo $this->mdl_tasks->form_value('task_name', true); ?>">
                        </div>

                        <div class="form-group">
                            <label for="task_description"><?php _trans('task_description'); ?></label>
                            <textarea name="task_description" id="task_description" class="form-control" rows="3"
                            ><?php echo $this->mdl_tasks->form_value('task_description', true); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="task_price"><?php _trans('task_price'); ?></label>
                            <div class="input-group">
                                <input type="text" name="task_price" id="task_price" class="amount form-control"
                                       value="<?php echo format_amount($this->mdl_tasks->form_value('task_price')); ?>">
                                <div class="input-group-addon">
                                    <?php echo get_setting('currency_symbol') ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tax_rate_id"><?php _trans('tax_rate'); ?></label>
                            <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
                                <option value="0"><?php _trans('none'); ?></option>
                                <?php foreach ($tax_rates as $tax_rate) { ?>
                                    <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                        <?php check_select($this->mdl_tasks->form_value('tax_rate_id'), $tax_rate->tax_rate_id); ?>>
                                        <?php echo $tax_rate->tax_rate_name . ' (' . format_amount($tax_rate->tax_rate_percent) . '%)'; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="task_finish_date"><?php _trans('task_finish_date'); ?></label>
                            <div class="input-group">
                                <input name="task_finish_date" id="task_finish_date" class="form-control datepicker"
                                       value="<?php echo date_from_mysql($this->mdl_tasks->form_value('task_finish_date')); ?>">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="task_status"><?php _trans('status'); ?></label>
                            <select name="task_status" id="task_status" class="form-control simple-select">
                                <?php foreach ($task_statuses as $key => $status) {
                                    if ($this->mdl_tasks->form_value('task_status') != 4 && $key == 4) continue; ?>
                                    <option value="<?php echo $key; ?>" <?php check_select($key, $this->mdl_tasks->form_value('task_status')); ?>>
                                        <?php echo $status['label']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('extra_information'); ?>
                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="project_id"><?php _trans('project'); ?>: </label>
                            <select name="project_id" id="project_id" class="form-control simple-select">
                                <option value=""><?php _trans('select_project'); ?></option>
                                <?php foreach ($projects as $project) { ?>
                                    <option value="<?php echo $project->project_id; ?>"
                                        <?php check_select($this->mdl_tasks->form_value('project_id'), $project->project_id); ?>>
                                        <?php echo htmlspecialchars($project->project_name); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

</form>
