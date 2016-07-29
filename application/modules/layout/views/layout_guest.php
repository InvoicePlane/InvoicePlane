<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<head>
    <title>
        <?php
        if ($this->mdl_settings->setting('custom_title') != '') {
            echo $this->mdl_settings->setting('custom_title');
        } else {
            echo 'InvoicePlane';
        } ?>
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/default/img/favicon.png">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom.css">

    <?php if ($this->mdl_settings->setting('monospace_amounts') == 1) { ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/monospace.css">
    <?php } ?>

    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/html5shiv-3.7.2.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/respond-1.4.2.min.js"></script>
    <![endif]-->

    <script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.12.3.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-3.3.6.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-ui-1.11.4.min.js"></script>

</head>

<body class="<?php if ($this->mdl_settings->setting('disable_sidebar') == 1) {
    echo 'hidden-sidebar';
} ?>">

<nav class="navbar navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle"
                    data-toggle="collapse" data-target="#ip-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <?php echo trans('menu') ?> &nbsp; <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="ip-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><?php echo anchor('guest', trans('dashboard')); ?></li>
                <li><?php echo anchor('guest/quotes/index', trans('quotes')); ?></li>
                <li><?php echo anchor('guest/invoices/index', trans('invoices')); ?></li>
                <li><?php echo anchor('guest/payments/index', trans('payments')); ?></li>
            </ul>

            <ul class="nav navbar-nav navbar-right settings">
                <li>
                    <a href="<?php echo site_url('sessions/logout'); ?>"
                       class="tip icon logout" data-placement="bottom"
                       data-original-title="<?php echo trans('logout'); ?>">
                        <span class="visible-xs">&nbsp;<?php echo trans('logout'); ?></span>
                        <i class="fa fa-power-off"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="sidebar hidden-xs <?php if ($this->mdl_settings->setting('disable_sidebar') == 1) {
    echo 'hidden';
} ?>">
    <ul>
        <li>
            <a href="<?php echo site_url('guest'); ?>" title="<?php echo trans('dashboard'); ?>" class="tip"
               data-placement="right">
                <i class="fa fa-dashboard"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('guest/quotes/index'); ?>" title="<?php echo trans('quotes'); ?>" class="tip"
               data-placement="right">
                <i class="fa fa-file"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('guest/invoices/index'); ?>" title="<?php echo trans('invoices'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-file-text"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('guest/payments/index'); ?>" title="<?php echo trans('payments'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-money"></i>
            </a>
        </li>
    </ul>
</div>

<div id="main-area">

    <div id="modal-placeholder"></div>

    <?php echo $content; ?>

</div>

<script defer src="<?php echo base_url(); ?>assets/default/js/plugins.js"></script>
<script defer src="<?php echo base_url(); ?>assets/default/js/scripts.min.js"></script>
<script defer src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-datepicker.min.js"></script>
<?php if (trans('cldr') != 'en') { ?>
    <script
        src="<?php echo base_url(); ?>assets/default/js/locales/bootstrap-datepicker.<?php echo trans('cldr'); ?>.js"></script>
<?php } ?>

</body>
</html>
