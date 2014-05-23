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
        <script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-ui-1.10.3.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap.min.js"></script>

        <script type="text/javascript">

            $(function()
            {
                $('.nav-tabs').tab();
                $('.tip').tooltip();
				
                $('.datepicker').datepicker({ format: '<?php echo date_format_datepicker(); ?>'});
		
                $('.create-invoice').click(function() {
                    $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_invoice'); ?>");
                });
				
                $('.create-quote').click(function() {
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>");
                });
				
                $('#btn_quote_to_invoice').click(function() {
                    quote_id = $(this).data('quote-id');
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_quote_to_invoice'); ?>/" + quote_id);
                });
				
                $('#btn_copy_invoice').click(function() {
                    invoice_id = $(this).data('invoice-id');
                    $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_copy_invoice'); ?>", {invoice_id: invoice_id});
                });
                
                $('#btn_copy_quote').click(function() {
                    quote_id = $(this).data('quote-id');
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_copy_quote'); ?>", {quote_id: quote_id});
                });
                
                $('.client-create-invoice').click(function() {
                    $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_invoice'); ?>", {
                        client_name: $(this).data('client-name')
                    });
                });
                $('.client-create-quote').click(function() {
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>", {
                        client_name: $(this).data('client-name')
                    });
                });
				$(document).on('click', '.invoice-add-payment', function() {
                    invoice_id = $(this).data('invoice-id');
                    invoice_balance = $(this).data('invoice-balance');
                    $('#modal-placeholder').load("<?php echo site_url('payments/ajax/modal_add_payment'); ?>", {invoice_id: invoice_id, invoice_balance: invoice_balance });
                });

            });

        </script>

	</head>

	<body>

		<nav class="navbar navbar-inverse">

			<div class="navbar-inner">

				<div class="container">

					<ul class="nav">

						<li><?php echo anchor('dashboard', lang('dashboard')); ?></li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('clients'); ?><b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><?php echo anchor('clients/form', lang('add_client')); ?></li>
								<li><?php echo anchor('clients/index', lang('view_clients')); ?></li>
							</ul>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('quotes'); ?><b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#" class="create-quote"><?php echo lang('create_quote'); ?></a></li>
								<li><?php echo anchor('quotes/index', lang('view_quotes')); ?></li>
							</ul>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('invoices'); ?><b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#" class="create-invoice"><?php echo lang('create_invoice'); ?></a></li>
								<li><?php echo anchor('invoices/index', lang('view_invoices')); ?></li>
                                <li><?php echo anchor('invoices/recurring/index', lang('view_recurring_invoices')); ?></li>
							</ul>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('payments'); ?><b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><?php echo anchor('payments/form', lang('enter_payment')); ?></li>
								<li><?php echo anchor('payments/index', lang('view_payments')); ?></li>
							</ul>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('reports'); ?><b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><?php echo anchor('reports/invoice_aging', lang('invoice_aging')); ?></li>
								<li><?php echo anchor('reports/payment_history', lang('payment_history')); ?></li>
								<li><?php echo anchor('reports/sales_by_client', lang('sales_by_client')); ?></li>
							</ul>
						</li>

					</ul>

					<?php if (isset($filter_display) and $filter_display == TRUE) { ?>
					<?php $this->layout->load_view('filter/jquery_filter'); ?>
					<form class="navbar-search pull-left">
						<input type="text" class="search-query" id="filter" placeholder="<?php echo $filter_placeholder; ?>">
					</form>
					<?php } ?>

					<ul class="nav pull-right settings">
                        <li><a href="#"><?php echo lang('welcome') . ' ' . $this->session->userdata('user_name'); ?></a></li>
                        <li class="divider-vertical"></li>
                        <li><a href="http://docs.fusioninvoice.com/1.3/" target="_blank" class="tip icon" data-original-title="Documentation" data-placement="bottom"><i class="icon-question-sign"></i></a></li>
						<li class="divider-vertical"></li>
						<li class="dropdown">
							<a href="#" class="tip icon dropdown-toggle" data-toggle="dropdown" data-original-title="<?php echo lang('settings'); ?>" data-placement="bottom"><i class="icon-cog"></i></a>
							<ul class="dropdown-menu">
                                <li><?php echo anchor('custom_fields/index', lang('custom_fields')); ?></li>
								<li><?php echo anchor('email_templates/index', lang('email_templates')); ?></li>
                                <li><?php echo anchor('import', lang('import_data')); ?></li>
								<li><?php echo anchor('invoice_groups/index', lang('invoice_groups')); ?></li>
                                <li><?php echo anchor('item_lookups/index', lang('item_lookups')); ?></li>
								<li><?php echo anchor('payment_methods/index', lang('payment_methods')); ?></li>
								<li><?php echo anchor('tax_rates/index', lang('tax_rates')); ?></li>
								<li><?php echo anchor('users/index', lang('user_accounts')); ?></li>
                                <li class="divider"></li>
                                <li><?php echo anchor('settings', lang('system_settings')); ?></li>
							</ul>
						</li>
						<li class="divider-vertical"></li>
						<li><a href="<?php echo site_url('sessions/logout'); ?>" class="tip icon logout" data-original-title="<?php echo lang('logout'); ?>" data-placement="bottom"><i class="icon-off"></i></a></li>
					</ul>

				</div>

			</div>

		</nav>

		<div class="sidebar">

			<ul>
				<li><a href="<?php echo site_url('dashboard'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/dashboard24x24.png" title="<?php echo lang('dashboard'); ?>" /></a></li>
				<li><a href="<?php echo site_url('clients/index'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/clients24x24.png" title="<?php echo lang('clients'); ?>" /></a></li>
				<li><a href="<?php echo site_url('quotes/index'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/quotes24x24.png" title="<?php echo lang('quotes'); ?>" /></a></li>
				<li><a href="<?php echo site_url('invoices/index'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/invoices24x24.png" title="<?php echo lang('invoices'); ?>" /></a></li>
				<li><a href="<?php echo site_url('payments/index'); ?>"><img alt="" src="<?php echo base_url(); ?>assets/default/img/icons/payments24x24.png" title="<?php echo lang('payments'); ?>" /></a></li>
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