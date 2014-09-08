<div class="container">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="install-panel">

            <h1><span>InvoicePlane</span></h1>

            <form method="post" class="form-horizontal"
                  action="<?php echo site_url($this->uri->uri_string()); ?>">

                <legend><?php echo lang('setup_prerequisites'); ?></legend>

                <p><?php echo lang('setup_prerequisites_message'); ?></p>

                <?php foreach ($basics as $basic) { ?>
                    <?php if ($basic['success']) { ?>
                    <p><span class="label label-success"><?php echo lang('success'); ?></span> <?php echo $basic['message']; ?></p>
                    <?php } else { ?>
                    <p><span class="label label-danger"><?php echo lang('failure'); ?></span> <?php echo $basic['message']; ?></p>
                    <?php } ?>
                <?php } ?>

                <?php foreach ($writables as $writable) { ?>
                    <?php if ($writable['success']) { ?>
                    <p><span class="label label-success"><?php echo lang('success'); ?></span> <?php echo $writable['message']; ?></p>
                    <?php } else { ?>
                    <p><span class="label label-danger"><?php echo lang('failure'); ?></span> <?php echo $writable['message']; ?></p>
                    <?php } ?>
                <?php } ?>

                <?php if ($errors) { ?>
                    <a href="javascript:history.go(0)" class="btn btn-danger">
                        <?php echo lang('try_again'); ?>
                    </a>
                <?php } else { ?>
                <input class="btn btn-success" type="submit" name="btn_continue"
                       value="<?php echo lang('continue'); ?>">
                <?php } ?>

            </div>
        </div>

    </form>
</div>