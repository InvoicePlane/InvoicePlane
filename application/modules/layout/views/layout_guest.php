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

		<meta name="viewport" content="width=device-width,initial-scale=1">

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/style.css">

		<script src="<?php echo base_url(); ?>assets/default/js/libs/modernizr-2.0.6.js"></script>
		<script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.7.1.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap.min.js"></script>

	</head>

	<body>

		<nav class="navbar navbar-inverse">

			<div class="navbar-inner">

				<div class="container-fluid">

					<ul class="nav">
						<li><?php echo anchor('guest', lang('dashboard')); ?></li>
                        <li><?php echo anchor('guest/quotes/index', lang('quotes')); ?></li>
                        <li><?php echo anchor('guest/invoices/index', lang('invoices')); ?></li>
                        <li><?php echo anchor('guest/payments/index', lang('payments')); ?></li>
					</ul>

					<ul class="nav pull-right settings">
                        <li><a href="#"><?php echo lang('welcome') . ' ' . $this->session->userdata('user_name'); ?></a></li>
						<li><a href="<?php echo site_url('sessions/logout'); ?>" class="tip icon logout" data-original-title="<?php echo lang('logout'); ?>" data-placement="bottom"><i class="icon-off"></i></a></li>
					</ul>

				</div>

			</div>

		</nav>

		<div class="sidebar">

			<ul>
				<li><a href="<?php echo site_url('guest'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/dashboard24x24.png" title="<?php echo lang('dashboard'); ?>" /></a></li>
				<li><a href="<?php echo site_url('guest/quotes/index'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/quotes24x24.png" title="<?php echo lang('quotes'); ?>" /></a></li>
				<li><a href="<?php echo site_url('guest/invoices/index'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/invoices24x24.png" title="<?php echo lang('invoices'); ?>" /></a></li>
				<li><a href="<?php echo site_url('guest/payments/index'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/payments24x24.png" title="<?php echo lang('payments'); ?>" /></a></li>
			</ul>

		</div>

		<div class="main-area">

			<div id="modal-placeholder"></div>
			
			<?php echo $content; ?>

		</div><!--end.content-->

		<script defer src="<?php echo base_url(); ?>assets/default/js/plugins.js"></script>
		<script defer src="<?php echo base_url(); ?>assets/default/js/script.js"></script>
		<script src="<?php echo base_url(); ?>assets/default/js/bootstrap-datepicker.js"></script>

		<!--[if lt IE 7 ]>
			<script src="<?php echo base_url(); ?>assets/default/js/dd_belatedpng.js"></script>
			<script type="text/javascript"> DD_belatedPNG.fix('img, .png_bg'); //fix any <img> or .png_bg background-images </script>
		<![endif]-->

		<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
			 chromium.org/developers/how-tos/chrome-frame-getting-started -->
		<!--[if lt IE 7 ]>
		  <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
		  <script type="text/javascript">window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->

	</body>
</html>