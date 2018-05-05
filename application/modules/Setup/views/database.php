<!-- @TODO Move texts into lang file [Kovah 2018-05-05] -->
<h2>Database Setup</h2>

<form action="/setup/database/" method="post">

    <?php _csrf_field(); ?>

    <?php if (!$database['success']) { ?>

        <p class="mb-5"><?php _trans('setup_database_message'); ?></p>

        <?php if ($database['message'] && $_POST) { ?>
            <div class="alert alert-danger">
                <b><?php _trans('failure'); ?></b>
                <br>
                <?php echo $database['message']; ?>
            </div>
        <?php } ?>

        <div class="form-group">
            <label for="db_hostname"><?php _trans('setup_db_hostname'); ?></label>
            <input type="text" name="db_hostname" id="db_hostname" class="form-control" required
                   value="<?php echo($this->input->post('db_hostname') ? $this->input->post('db_hostname') : 'localhost'); ?>">
            <span class="form-help"><?php _trans('setup_db_hostname_info'); ?></span>
        </div>

        <div class="form-group">
            <label for="db_port">
                <?php _trans('setup_db_port'); ?>
            </label>
            <input type="text" name="db_port" id="db_port" class="form-control" required
                   value="<?php echo($this->input->post('db_port') ? $this->input->post('db_port') : 3306); ?>">
            <span class="form-help"><?php _trans('setup_db_port_info'); ?></span>
        </div>

        <div class="form-group">
            <label for="db_username">
                <?php _trans('username'); ?>
            </label>
            <input type="text" name="db_username" id="db_username" class="form-control" required
                   value="<?php echo $this->input->post('db_username'); ?>">
            <span class="form-help"><?php _trans('setup_db_username_info'); ?></span>
        </div>

        <div class="form-group">
            <label for="db_password">
                <?php _trans('password'); ?>
            </label>
            <input type="password" name="db_password" id="db_password" class="form-control"
                   value="<?php echo $this->input->post('db_password'); ?>">
            <span class="form-help"><?php _trans('setup_db_password_info'); ?></span>
        </div>

        <div class="form-group">
            <label for="db_database">
                <?php _trans('setup_db_database'); ?>
            </label>
            <input type="text" name="db_database" id="db_database" class="form-control" required
                   value="<?php echo $this->input->post('db_database'); ?>">
            <span class="form-help"><?php _trans('setup_db_database_info'); ?></span>
        </div>

        <div class="form-group mb-5">
            <label for="db_prefix">
                <?php _trans('setup_db_prefix'); ?>
            </label>
            <input type="text" name="db_prefix" id="db_prefix" class="form-control"
                   value="<?php echo $this->input->post('db_prefix'); ?>">
            <span class="form-help"><?php _trans('setup_db_prefix_info'); ?></span>
        </div>

    <?php } ?>

    <?php if ($errors) { ?>
        <button class="btn btn-warning" type="submit" name="btn_save" value="1">
            <?php _trans('setup_db_save_settings'); ?>
        </button>
    <?php } else { ?>
        <div class="alert alert-success mt-5 mb-5">
            <i class="fa fa-check text-success fa-mr"></i>
            <?php _trans('setup_database_configured_message'); ?>
        </div>
        <button class="btn btn-primary" type="submit" name="btn_continue" value="1">
            <?php _trans('continue'); ?>
        </button>
    <?php } ?>

</form>
