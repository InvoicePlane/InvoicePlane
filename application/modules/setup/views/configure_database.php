<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

            <legend><?php _trans('setup_database_details'); ?></legend>

            <?php if (!$database['success']) { ?>

                <?php if ($database['message'] and $_POST) { ?>
                    <div class="alert alert-danger">
                        <b><?php _trans('failure'); ?></b><br>
                        <?php echo $database['message']; ?>
                    </div>
                <?php } ?>

                <p><?php _trans('setup_database_message'); ?></p>

                <div class="form-group">
                    <label for="db_hostname">
                        <?php _trans('hostname'); ?>
                    </label>
                    <input type="text" name="db_hostname" id="db_hostname" class="form-control"
                           value="<?php echo($this->input->post('db_hostname') ? $this->input->post('db_hostname') : 'localhost'); ?>">
                    <span class="help-block"><?php _trans('setup_db_hostname_info'); ?></span>
                </div>

                <div class="form-group">
                    <label for="db_port">
                        <?php _trans('port'); ?>
                    </label>
                    <input type="text" name="db_port" id="db_port" class="form-control"
                           value="<?php echo($this->input->post('db_port') ? $this->input->post('db_port') : 3306); ?>">
                    <span class="help-block"><?php _trans('setup_db_port_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php _trans('username'); ?>
                    </label>
                    <input type="text" name="db_username" id="db_username" class="form-control"
                           value="<?php echo $this->input->post('db_username'); ?>">
                    <span class="help-block"><?php _trans('setup_db_username_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php _trans('password'); ?>
                    </label>
                    <input type="password" name="db_password" id="db_password" class="form-control"
                           value="<?php echo $this->input->post('db_password'); ?>">
                    <span class="help-block"><?php _trans('setup_db_password_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php _trans('database'); ?>
                    </label>
                    <input type="text" name="db_database" id="db_database" class="form-control"
                           value="<?php echo $this->input->post('db_database'); ?>">
                    <span class="help-block"><?php _trans('setup_db_database_info'); ?></span>
                </div>
            <?php } ?>

            <?php if ($errors) { ?>
                <input type="submit" class="btn btn-primary" name="btn_try_again"
                       value="<?php _trans('try_again'); ?>">
            <?php } else { ?>
                <p><i class="fa fa-check text-success fa-margin"></i>
                    <?php _trans('setup_database_configured_message'); ?>
                </p>
                <input type="submit" class="btn btn-success" name="btn_continue"
                       value="<?php _trans('continue'); ?>">
            <?php } ?>

        </form>

    </div>
</div>
