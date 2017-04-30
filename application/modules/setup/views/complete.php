<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <h2><?php _trans('setup_complete'); ?></h2>

        <p>
            <?php _trans('setup_complete_message'); ?>
        </p>

        <p class="alert alert-info">
            <?php _trans('setup_complete_support_note'); ?>
        </p>

        <p class="alert alert-warning">
            <?php _trans('setup_complete_secure_setup'); ?>
        </p>

        <?php if ($this->session->userdata('setup_notice')) {
            $setup_notice = $this->session->userdata('setup_notice');
            ?>
            <div class="alert <?php echo $setup_notice['type']; ?>">
                <?php echo $setup_notice['content']; ?>
            </div>
        <?php } ?>

        <a href="<?php echo site_url('sessions/login'); ?>" class="btn btn-success">
            <i class="fa fa-check fa-margin"></i> <?php _trans('login'); ?>
        </a>

    </div>
</div>
