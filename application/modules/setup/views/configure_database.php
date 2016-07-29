<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post"
              action="<?php echo site_url($this->uri->uri_string()); ?>">

            <legend><?php echo trans('setup_database_details'); ?></legend>

            <?php if (!$database['success']) { ?>

                <?php if ($database['message'] and $_POST) { ?>
                    <p><span class="label label-danger"><?php echo trans('failure'); ?></span>
                        <?php echo $database['message']; ?>
                    </p>
                <?php } ?>

                <p><?php echo trans('setup_database_message'); ?></p>

                <div class="form-group">
                    <label for="db_hostname">
                        <?php echo trans('hostname'); ?>
                    </label>
                    <input type="text" name="db_hostname" id="db_hostname" class="form-control"
                           value="<?php echo $this->input->post('db_hostname'); ?>">
                    <span class="help-block"><?php echo trans('setup_db_hostname_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo trans('username'); ?>
                    </label>
                    <input type="text" name="db_username" id="db_username" class="form-control"
                           value="<?php echo $this->input->post('db_username'); ?>">
                    <span class="help-block"><?php echo trans('setup_db_username_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo trans('password'); ?>
                    </label>
                    <input type="password" name="db_password" id="db_password" class="form-control"
                           value="<?php echo $this->input->post('db_password'); ?>">
                    <span class="help-block"><?php echo trans('setup_db_password_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo trans('database'); ?>
                    </label>
                    <input type="text" name="db_database" id="db_database" class="form-control"
                           value="<?php echo $this->input->post('db_database'); ?>">
                    <span class="help-block"><?php echo trans('setup_db_database_info'); ?></span>
                </div>
            <?php } ?>

            <?php if ($errors) { ?>
                <input type="submit" class="btn btn-primary" name="btn_try_again"
                       value="<?php echo trans('try_again'); ?>">
            <?php } else { ?>
                <p><i class="fa fa-check text-success fa-margin"></i>
                    <?php echo trans('setup_database_configured_message'); ?>
                </p>
                <input type="submit" class="btn btn-success" name="btn_continue"
                       value="<?php echo trans('continue'); ?>">
            <?php } ?>

        </form>

    </div>
</div>