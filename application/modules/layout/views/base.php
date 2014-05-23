<!doctype html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>

	<meta charset="utf-8">

	<!-- Use the .htaccess and remove these lines to avoid edge case issues -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>FusionInvoice</title>
	<meta name="description" content="">
	<meta name="author" content="William G. Rivera">

	<!-- Mobile viewport optimized: j.mp/bplateviewport -->
	<meta name="viewport" content="width=device-width">

	<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

	<!-- CSS: implied media=all -->
	<link rel="stylesheet" href="<?php echo base_url() . 'assets/default/css/style.css'; ?>">
	<!-- end CSS-->

	<!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

	<!-- All JavaScript at the bottom, except for Modernizr / Respond.
	     Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
	     For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
	<script src="<?php echo base_url() . 'assets/default/js/libs/modernizr-2.0.6.js'; ?>"></script>
	
	<style type="text/css">
		html { overflow-y: visible; }
	</style>

</head>

<body style="overflow-y: visible;height:auto;">

	<?php echo $content; ?>

	<script type="text/javascript" src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap.min.js"></script>
	
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/default/js/plugins.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/default/js/script.js"></script>

	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
	     chromium.org/developers/how-tos/chrome-frame-getting-started -->
	<!--[if lt IE 7 ]>
	  <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	  <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	<![endif]-->

</body>
</html>