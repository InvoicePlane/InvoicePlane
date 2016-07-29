<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo trans('tasks_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="row">
            <div class="col-xs-12 col-sm-7">
                <fieldset>
                    <legend>
                        <?php if ($this->mdl_tasks->form_value('task_id')) : ?>
                            #<?php echo $this->mdl_tasks->form_value('task_id'); ?>&nbsp;
                            <?php echo $this->mdl_tasks->form_value('task_name'); ?>
                        <?php else : ?>
                            <?php echo trans('new_task'); ?>
                        <?php endif; ?>
                    </legend>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label"><?php echo trans('task_name'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="task_name" id="task_name" class="form-control"
                                   value="<?php echo $this->mdl_tasks->form_value('task_name'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label"><?php echo trans('task_description'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="task_description" id="task_description" class="form-control"
                                   value="<?php echo $this->mdl_tasks->form_value('task_description'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label"><?php echo trans('task_price'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="task_price" id="task_price" class="form-control"
                                   value="<?php echo $this->mdl_tasks->form_value('task_price'); ?>">
                        </div>
                    </div>

                    <div class="form-group has-feedback">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label for="task_finish_date"><?php echo trans('task_finish_date'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <div class="input-group">
                                <input name="task_finish_date" id="task_finish_date"
                                       class="form-control datepicker"
                                       value="<?php echo date_from_mysql($this->mdl_tasks->form_value('task_finish_date')); ?>">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label"><?php echo trans('status'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <select name="task_status" id="task_status" class="form-control">
                                <?php foreach ($task_statuses as $key => $status) { ?>
                                    <option value="<?php echo $key; ?>"
                                            <?php if ($key == $this->mdl_tasks->form_value('task_status')) { ?>selected="selected"<?php } ?>><?php echo $status['label']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-5">
                <fieldset>
                    <legend><?php echo trans('extra_information'); ?></legend>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo trans('project'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <select name="project_id" id="project_id" class="form-control">
                                <option value=""><?php echo trans('select_project'); ?></option>
                                <?php foreach ($projects as $project) { ?>
                                    <option value="<?php echo $project->project_id; ?>"
                                            <?php if ($this->mdl_tasks->form_value('project_id') == $project->project_id) { ?>selected="selected"<?php } ?>><?php echo $project->project_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

    </div>

</form>