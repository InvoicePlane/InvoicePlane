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
        if (get_setting('custom_title') != '') {
            echo get_setting('custom_title');
        } else {
            echo 'InvoicePlane';
        } ?>
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/core/img/favicon.png">

    <link href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/style.css"
          rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/core/css/custom.css" rel="stylesheet">

</head>

<body>

<noscript>
    <div class="alert alert-danger no-margin"><?php _trans('please_enable_js'); ?></div>
</noscript>

<br>

<div class="container">

    <div id="password_reset"
         class="panel panel-default panel-body col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">

        <div class="row"><?php $this->layout->load_view('layout/alerts'); ?></div>

        <h3><?php _trans('password_reset'); ?></h3>

        <br/>

        <p><?php _trans('password_reset_info'); ?></p>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

            <div class="form-group">
                <label for="email" class="hidden"><?php _trans('email'); ?></label>
                <input type="text" name="email" id="email" class="form-control"
                       placeholder="<?php _trans('email'); ?>" required autofocus>
            </div>

            <input type="hidden" name="btn_reset" value="true">

            <button type="submit" class="btn btn-danger">
                <i class="fa fa-key fa-margin"></i> <?php _trans('reset_password'); ?>
            </button>

        </form>

    </div>

</div>

</body>
</html>
