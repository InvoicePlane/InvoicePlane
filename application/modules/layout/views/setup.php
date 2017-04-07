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
    <title>InvoicePlane Setup</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/core/img/favicon.png">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/invoiceplane/css/welcome.css">

    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>assets/core/js/legacy.min.js"></script>
    <![endif]-->

    <script src="<?php echo base_url(); ?>assets/core/js/dependencies.min.js"></script>

</head>
<body>

<noscript>
    <div class="alert alert-danger no-margin"><?php _trans('please_enable_js'); ?></div>
</noscript>

<?php echo $content; ?>

</body>
</html>
