<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" class="form-horizontal"
              action="<?php echo site_url($this->uri->uri_string()); ?>">

            <legend><?php echo lang('setup_prerequisites'); ?></legend>

            <p><?php echo lang('setup_prerequisites_message'); ?></p>

            <?php foreach ($basics as $basic) {
                if ($basic['success']) { ?>
                    <p><i class="fa fa-check text-success fa-margin"></i> <?php echo $basic['message']; ?></p>
                <?php } elseif ($basic['warning']) { ?>
                    <p><i class="fa fa-exclamation text-warning fa-margin"></i> <?php echo $basic['message']; ?></p>
                <?php } else { ?>
                    <p><i class="fa fa-close text-danger fa-margin"></i> <?php echo $basic['message']; ?></p>
                <?php }
            } ?>

            <?php foreach ($writables as $writable) {
                if ($writable['success']) { ?>
                    <p><i class="fa fa-check text-success fa-margin"></i> <?php echo $writable['message']; ?></p>
                <?php } else { ?>
                    <p><i class="fa fa-close text-danger fa-margin"></i> <?php echo $writable['message']; ?></p>
                <?php }
            } ?>

            <?php if ($errors) { ?>
                <a href="javascript:history.go(0)" class="btn btn-danger">
                    <?php echo lang('try_again'); ?>
                </a>
            <?php } else { ?>
                <input class="btn btn-success" type="submit" name="btn_continue"
                       value="<?php echo lang('continue'); ?>">
            <?php } ?>

        </form>

    </div>
</div>