<div class="container">

    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="install-panel">

            <h1><span>InvoicePlane</span></h1>

            <h2><?php echo lang('setup_complete'); ?></h2>

            <p class="alert alert-success">
                <?php echo lang('setup_complete_message'); ?>
            </p>

            <p class="alert alert-info">
                <?php echo lang('setup_complete_support_note'); ?>
            </p>

            <p class="alert alert-warning">
                <?php echo lang('setup_complete_secure_setup'); ?>
                <br/>
                <code>Redirect /setup http://yourdomain.com/</code>
            </p>

            <a href="<?php echo site_url('sessions/login'); ?>" class="btn btn-success" >
                <i class="fa fa-check fa-margin"></i> <?php echo lang('login'); ?>
            </a>
        </div>
    </div>
</div>