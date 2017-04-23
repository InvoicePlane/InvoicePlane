<!doctype html lang="<?php _trans('cldr'); ?>">

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="<?php _trans('cldr'); ?>"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="<?php _trans('cldr'); ?>"> <!--<![endif]-->

<head>
    <title>
        <?php
        if (get_setting('custom_title') != '') {
            echo get_setting('custom_title', '', true);
        } else {
            echo 'InvoicePlane';
        } ?>
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <meta name="_csrf" content="<?php echo $this->security->get_csrf_hash() ?>">

    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/core/img/favicon.png">

    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom.css">

    <?php if (get_setting('monospace_amounts') == 1) { ?>
        <link rel="stylesheet"
              href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/monospace.css">
    <?php } ?>

    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>assets/core/js/legacy.min.js"></script>
    <![endif]-->

    <script src="<?php echo base_url(); ?>assets/core/js/dependencies.min.js"></script>

</head>
<body class="<?php echo get_setting('disable_sidebar') ? 'hidden-sidebar' : ''; ?>">

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
                       title="<?php _trans('logout'); ?>">
                        <span class="visible-xs">&nbsp;<?php _trans('logout'); ?></span>
                        <i class="fa fa-power-off"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="main-area">

    <div class="sidebar hidden-xs <?php if (get_setting('disable_sidebar') == 1) {
        echo 'hidden';
    } ?>">
        <ul>
            <li>
                <a href="<?php echo site_url('guest'); ?>" title="<?php _trans('dashboard'); ?>" class="tip"
                   data-placement="right">
                    <i class="fa fa-dashboard"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('guest/quotes/index'); ?>" title="<?php _trans('quotes'); ?>"
                   class="tip"
                   data-placement="right">
                    <i class="fa fa-file"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('guest/invoices/index'); ?>" title="<?php _trans('invoices'); ?>"
                   class="tip" data-placement="right">
                    <i class="fa fa-file-text"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('guest/payments/index'); ?>" title="<?php _trans('payments'); ?>"
                   class="tip" data-placement="right">
                    <i class="fa fa-money"></i>
                </a>
            </li>
        </ul>
    </div>

    <div id="main-content">
        <?php echo $content; ?>
    </div>

</div>

<div id="modal-placeholder"></div>

<?php echo $this->layout->load_view('layout/includes/fullpage-loader'); ?>

<script defer src="<?php echo base_url(); ?>assets/core/js/scripts.min.js"></script>
<?php if (trans('cldr') != 'en') { ?>
    <script src="<?php echo base_url(); ?>assets/core/js/locales/bootstrap-datepicker.<?php _trans('cldr'); ?>.js"></script>
<?php } ?>

</body>
</html>
