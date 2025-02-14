<!DOCTYPE html>

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

    <link rel="icon" href="<?php _core_asset('img/favicon.png'); ?>" type="image/png">

    <link rel="stylesheet" href="<?php _theme_asset('css/welcome.css'); ?>" type="text/css">
    <!--[if lt IE 9]>
    <script src="<?php _core_asset('js/legacy.min.js'); ?>"></script>
    <![endif]-->

    <script src="<?php _core_asset('js/dependencies.min.js'); ?>"></script>

</head>
<body>

<noscript>
    <div class="alert alert-danger no-margin"><?php _trans('please_enable_js'); ?></div>
</noscript>

<?php echo $content; ?>

</body>
</html>
