<!doctype html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
	<title>InvoicePlane</title>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<meta name="robots" content="NOINDEX,NOFOLLOW">

	<link rel="icon" type="image/png" href="/assets/default/img/favicon.png">

    <link rel="stylesheet" href="<?php echo base_url() . 'assets/default/css/style.css'; ?>">
    <link rel="stylesheet" href="<?php echo base_url() . 'assets/default/css/custom.css'; ?>">

	<script src="<?php echo base_url() . 'assets/default/js/libs/modernizr-2.8.3.min.js'; ?>"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.11.2.min.js"></script>
    <script type="text/javascript">
        (function ($) {
            $(document);
        }(jQuery));
    </script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-3.3.1.min.js"></script>

	<style type="text/css">
		html { overflow-y: visible; }
	</style>

</head>

<body style="overflow-y: visible;height:auto;">

    <noscript>
        <div class="alert alert-danger no-margin"><?php echo lang('please_enable_js'); ?></div>
    </noscript>

	<?php echo $content; ?>

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/default/js/plugins.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/default/js/scripts.min.js"></script>

	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
	     chromium.org/developers/how-tos/chrome-frame-getting-started -->
	<!--[if lt IE 7 ]>
	  <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	  <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	<![endif]-->

</body>
</html>