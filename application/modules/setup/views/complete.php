<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <h2><?php echo lang('setup_complete'); ?></h2>

        <p>
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

        <div><?php $this->layout->load_view('layout/alerts'); ?></div>

        <a href="<?php echo site_url('sessions/login'); ?>" class="btn btn-success" >
            <i class="fa fa-check fa-margin"></i> <?php echo lang('login'); ?>
        </a>

    </div>
</div>