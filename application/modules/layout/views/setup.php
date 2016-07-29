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
    <title>InvoicePlane Setup</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/default/img/favicon.png">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/welcome.css">

    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/html5shiv-3.7.2.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/respond-1.4.2.min.js"></script>
    <![endif]-->

    <script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.12.3.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-3.3.6.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/select2-4.0.2.full.min.js"></script>

</head>

<body>

<noscript>
    <div class="alert alert-danger no-margin"><?php echo trans('please_enable_js'); ?></div>
</noscript>

<?php echo $content; ?>

</body>
</html>