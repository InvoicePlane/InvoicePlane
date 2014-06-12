<div class="container">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="install-panel">

            <h1><span>InvoicePlane</span></h1>

            <form method="post" class="form-horizontal"
                  action="<?php echo site_url($this->uri->uri_string()); ?>">

                <legend><?php echo lang('setup_upgrade_tables'); ?></legend>

                <?php if ($errors) { ?>
                    <p>
                        <?php echo lang('setup_upgrade_message'); ?>
                    </p>

                    <?php foreach ($errors as $error) { ?>
                        <p>
                            <span class="label label-danger"><?php echo lang('failure'); ?></span>
                            <?php echo $error; ?>
                        </p>
                    <?php } ?>

                <?php } else { ?>
                    <p>
                        <span class="label label-success"><?php echo lang('success'); ?></span>
                        <?php echo lang('setup_upgrade_success'); ?>
                    </p>
                <?php } ?>

                <?php if ($errors) { ?>
                    <input type="submit" class="btn btn-danger" name="btn_try_again" value="<?php echo lang('try_again'); ?>">
                <?php } else { ?>
                    <input type="submit" class="btn btn-success" name="btn_continue" value="<?php echo lang('continue'); ?>">
                <?php } ?>

            </form>

        </div>
    </div>
</div>