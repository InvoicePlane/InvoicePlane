<!doctype html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>

	<meta charset="utf-8">

	<!-- Use the .htaccess and remove these lines to avoid edge case issues -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>InvoicePlane</title>

	<!-- Mobile viewport optimized: j.mp/bplateviewport -->
	<meta name="viewport" content="width=device-width">

	<!-- CSS: implied media=all -->
    <link rel="stylesheet" href="<?php echo base_url() . 'assets/default/css/style.css'; ?>">
    <link rel="stylesheet" href="<?php echo base_url() . 'assets/default/css/custom.css'; ?>">
	<!-- end CSS-->

	<script src="<?php echo base_url() . 'assets/default/js/libs/modernizr-2.8.2.js'; ?>"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.11.1.min.js"></script>
    <script type="text/javascript">
        (function ($) {
            $(document);
        }(jQuery));
    </script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-3.2.0.min.js"></script>

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