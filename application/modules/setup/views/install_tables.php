<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" class="form-horizontal" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

            <legend><?php _trans('setup_install_tables'); ?></legend>

            <?php if ($errors) { ?>
                <p><?php _trans('setup_tables_errors'); ?></p>

                <?php foreach ($errors as $error) { ?>
                    <p>
                        <span class="label label-important">
                            <?php _trans('failure'); ?>
                        </span>
                        <?php echo $error; ?>
                    </p>
                <?php } ?>

            <?php } else { ?>
                <p>
                    <i class="fa fa-check text-success fa-margin"></i>
                    <?php _trans('setup_tables_success'); ?>
                </p>
            <?php } ?>

            <?php if ($errors) { ?>
                <input type="submit" class="btn btn-primary" name="btn_try_again"
                       value="<?php _trans('try_again'); ?>">
            <?php } else { ?>
                <input type="submit" class="btn btn-success" name="btn_continue"
                       value="<?php _trans('continue'); ?>">
            <?php } ?>

        </form>

    </div>
</div>
