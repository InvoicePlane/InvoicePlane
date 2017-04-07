<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>
        <form method="post" class="form-horizontal" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

            <legend><?php _trans('setup_prerequisites'); ?></legend>

            <p><?php _trans('setup_prerequisites_message'); ?></p>

            <?php foreach ($basics as $basic) {
                if (isset($basic['warning'])) { ?>
                    <p><i class="fa fa-exclamation text-warning fa-margin"></i> <?php echo $basic['message']; ?></p>
                <?php } elseif ($basic['success'] == 1) { ?>
                    <p><i class="fa fa-check text-success fa-margin"></i> <?php echo $basic['message']; ?></p>
                <?php } else {
                    $errors = true;
                    ?>
                    <p><i class="fa fa-close text-danger fa-margin"></i> <?php echo $basic['message']; ?></p>
                <?php }
            } ?>

            <br>

            <?php foreach ($writables as $writable) {
                if ($writable['success'] === 1) { ?>
                    <p><i class="fa fa-check text-success fa-margin"></i> <?php echo $writable['message']; ?></p>
                <?php } else { ?>
                    <p><i class="fa fa-close text-danger fa-margin"></i> <?php echo $writable['message']; ?></p>
                <?php }
            } ?>

            <?php if ($errors) { ?>
                <a href="javascript:history.go(0)" class="btn btn-danger">
                    <?php _trans('try_again'); ?>
                </a>
            <?php } else { ?>
                <input class="btn btn-success" type="submit" name="btn_continue"
                       value="<?php _trans('continue'); ?>">
            <?php } ?>

        </form>

    </div>
</div>
