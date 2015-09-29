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
        if ($this->mdl_settings->setting('custom_title') != '') {
            echo $this->mdl_settings->setting('custom_title');
        } else {
            echo 'InvoicePlane';
        } ?>
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/default/img/favicon.png">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom.css">

    <?php if ($this->mdl_settings->setting('monospace_amounts') == 1) { ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/monospace.css">
    <?php } ?>

    <script src="<?php echo base_url(); ?>assets/default/js/libs/modernizr-2.8.3.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.11.2.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-3.3.2.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-ui-1.11.2.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-typeahead.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/select2.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/default/js/libs/dropzone.js"></script>

    <script type="text/javascript">
        Dropzone.autoDiscover = false;

        $(function () {
            $('.nav-tabs').tab();
            $('.tip').tooltip();

            $('body').on('focus', ".datepicker", function () {
                $(this).datepicker({
                    autoclose: true,
                    format: '<?php echo date_format_datepicker(); ?>',
                    language: '<?php echo lang('cldr'); ?>',
                    weekStart: '<?php echo $this->mdl_settings->setting('first_day_of_week'); ?>'
                });
            });

            $('.create-invoice').click(function () {
                $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_invoice'); ?>");
            });

            $('.create-quote').click(function () {
                $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>");
            });

            $('#btn_quote_to_invoice').click(function () {
                quote_id = $(this).data('quote-id');
                $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_quote_to_invoice'); ?>/" + quote_id);
            });

            $('#btn_copy_invoice').click(function () {
                invoice_id = $(this).data('invoice-id');
                $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_copy_invoice'); ?>", {invoice_id: invoice_id});
            });

            $('#btn_create_credit').click(function () {
                invoice_id = $(this).data('invoice-id');
                $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_credit'); ?>", {invoice_id: invoice_id});
            });

            $('#btn_copy_quote').click(function () {
                quote_id = $(this).data('quote-id');
                $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_copy_quote'); ?>", {quote_id: quote_id});
            });

            $('.client-create-invoice').click(function () {
                $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_invoice'); ?>", {
                    client_name: $(this).data('client-name')
                });
            });
            $('.client-create-quote').click(function () {
                $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>", {
                    client_name: $(this).data('client-name')
                });
            });
            $(document).on('click', '.invoice-add-payment', function () {
                invoice_id = $(this).data('invoice-id');
                invoice_balance = $(this).data('invoice-balance');
                invoice_payment_method = $(this).data('invoice-payment-method');
                $('#modal-placeholder').load("<?php echo site_url('payments/ajax/modal_add_payment'); ?>", {
                    invoice_id: invoice_id,
                    invoice_balance: invoice_balance,
                    invoice_payment_method: invoice_payment_method
                });
            });

        });

    </script>

</head>

<body class="<?php if ($this->mdl_settings->setting('disable_sidebar') == 1) {
    echo 'hidden-sidebar';
} ?>">

<noscript>
    <div class="alert alert-danger no-margin"><?php echo lang('please_enable_js'); ?></div>
</noscript>

<nav class="navbar navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#ip-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <?php echo lang('menu') ?> &nbsp; <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="ip-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><?php echo anchor('dashboard', lang('dashboard'), 'class="hidden-sm"') ?>
                    <?php echo anchor('dashboard', '<i class="fa fa-dashboard"></i>',
                        'class="visible-sm-inline-block"') ?>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;<span
                            class="hidden-sm"><?php echo lang('clients'); ?></span><i
                            class="visible-sm-inline fa fa-users"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('clients/form', lang('add_client')); ?></li>
                        <li><?php echo anchor('clients/index', lang('view_clients')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;<span
                            class="hidden-sm"><?php echo lang('quotes'); ?></span><i
                            class="visible-sm-inline fa fa-file"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="create-quote"><?php echo lang('create_quote'); ?></a></li>
                        <li><?php echo anchor('quotes/index', lang('view_quotes')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;<span
                            class="hidden-sm"><?php echo lang('invoices'); ?></span><i
                            class="visible-sm-inline fa fa-file-text"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="create-invoice"><?php echo lang('create_invoice'); ?></a></li>
                        <li><?php echo anchor('invoices/index', lang('view_invoices')); ?></li>
                        <li><?php echo anchor('invoices/recurring/index', lang('view_recurring_invoices')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;<span
                            class="hidden-sm"><?php echo lang('products'); ?></span><i
                            class="visible-sm-inline fa fa-database"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('products/form', lang('create_product')); ?></li>
                        <li><?php echo anchor('products/index', lang('view_products')); ?></li>
                        <li><?php echo anchor('families/index', lang('product_families')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;<span
                            class="hidden-sm"><?php echo lang('payments'); ?></span><i
                            class="visible-sm-inline fa fa-credit-card"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('payments/form', lang('enter_payment')); ?></li>
                        <li><?php echo anchor('payments/index', lang('view_payments')); ?></li>
                    </ul>
                </li>

                <li class="dropdown <?php if (!$this->mdl_settings->setting('default_hourly_rate')) {
                    echo 'hidden';
                } ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;<span
                            class="hidden-sm"><?php echo lang('tasks'); ?></span><i
                            class="visible-sm-inline fa fa-check-square-o"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('tasks/form', lang('create_task')); ?></li>
                        <li><?php echo anchor('tasks/index', lang('show_tasks')); ?></li>
                        <li><?php echo anchor('projects/index', lang('projects')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;<span
                            class="hidden-sm"><?php echo lang('reports'); ?></span><i
                            class="visible-sm-inline fa fa-bar-chart"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('reports/invoice_aging', lang('invoice_aging')); ?></li>
                        <li><?php echo anchor('reports/payment_history', lang('payment_history')); ?></li>
                        <li><?php echo anchor('reports/sales_by_client', lang('sales_by_client')); ?></li>
                        <li><?php echo anchor('reports/sales_by_year', lang('sales_by_date')); ?></li>
                    </ul>
                </li>

            </ul>

            <?php if (isset($filter_display) and $filter_display == true) { ?>
                <?php $this->layout->load_view('filter/jquery_filter'); ?>
                <form class="navbar-form navbar-left" role="search" onsubmit="return false;">
                    <div class="form-group">
                        <input id="filter" type="text" class="search-query form-control input-sm"
                               placeholder="<?php echo $filter_placeholder; ?>">
                    </div>
                </form>
            <?php } ?>

            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="http://docs.invoiceplane.com/" target="_blank"
                       class="tip icon" data-original-title="<?php echo lang('documentation'); ?>"
                       data-placement="bottom">
                        <i class="fa fa-question-circle"></i>
                        <span class="visible-xs">&nbsp;<?php echo lang('documentation'); ?></span>
                    </a>
                </li>

                <li class="dropdown">
                    <a href="#" class="tip icon dropdown-toggle" data-toggle="dropdown"
                       data-original-title="<?php echo lang('settings'); ?>"
                       data-placement="bottom">
                        <i class="fa fa-cogs"></i>
                        <span class="visible-xs">&nbsp;<?php echo lang('settings'); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('custom_fields/index', lang('custom_fields')); ?></li>
                        <li><?php echo anchor('email_templates/index', lang('email_templates')); ?></li>
                        <li><?php echo anchor('invoice_groups/index', lang('invoice_groups')); ?></li>
                        <li><?php echo anchor('invoices/archive', lang('invoice_archive')); ?></li>
                        <!-- // temporarily disabled
                        <li><?php echo anchor('item_lookups/index', lang('item_lookups')); ?></li>
                        -->
                        <li><?php echo anchor('payment_methods/index', lang('payment_methods')); ?></li>
                        <li><?php echo anchor('tax_rates/index', lang('tax_rates')); ?></li>
                        <li><?php echo anchor('users/index', lang('user_accounts')); ?></li>
                        <li class="divider hidden-xs hidden-sm"></li>
                        <li><?php echo anchor('settings', lang('system_settings')); ?></li>
                        <li><?php echo anchor('import', lang('import_data')); ?></li>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo site_url('users/form/' .
                        $this->session->userdata('user_id')); ?>">
                        <?php
                        print($this->session->userdata('user_name'));
                        if ($this->session->userdata('user_company')) {
                            print(" (" . $this->session->userdata('user_company') . ")");
                        }
                        ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('sessions/logout'); ?>"
                       class="tip icon logout" data-placement="bottom"
                       data-original-title="<?php echo lang('logout'); ?>">
                        <i class="fa fa-power-off"></i>
                        <span class="visible-xs">&nbsp;<?php echo lang('logout'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="sidebar hidden-xs <?php if ($this->mdl_settings->setting('disable_sidebar') == 1) {
    echo 'hidden';
} ?>">
    <ul>
        <li>
            <a href="<?php echo site_url('dashboard'); ?>" title="<?php echo lang('dashboard'); ?>" class="tip"
               data-placement="right">
                <i class="fa fa-dashboard"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('clients/index'); ?>" title="<?php echo lang('clients'); ?>" class="tip"
               data-placement="right">
                <i class="fa fa-users"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('quotes/index'); ?>" title="<?php echo lang('quotes'); ?>" class="tip"
               data-placement="right">
                <i class="fa fa-file"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('invoices/index'); ?>" title="<?php echo lang('invoices'); ?>" class="tip"
               data-placement="right">
                <i class="fa fa-file-text"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('payments/index'); ?>" title="<?php echo lang('payments'); ?>" class="tip"
               data-placement="right">
                <i class="fa fa-money"></i>
            </a>
        </li>
    </ul>
</div>

<div id="main-area">

    <div id="modal-placeholder"></div>

    <?php echo $content; ?>

</div>

<div id="fullpage-loader" style="display: none">
    <div class="loader-content">
        <i class="fa fa-cog fa-spin"></i>

        <div id="loader-error" style="display: none">
            <?php echo lang('loading_error'); ?><br/>
            <a href="https://wiki.invoiceplane.com/<?php echo lang('cldr'); ?>/1.0/general/faq"
               class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-support"></i> <?php echo lang('loading_error_help'); ?>
            </a>
        </div>
    </div>
</div>

<script defer src="<?php echo base_url(); ?>assets/default/js/plugins.js"></script>
<script defer src="<?php echo base_url(); ?>assets/default/js/scripts.min.js"></script>
<script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-datepicker.js"></script>
<?php if (lang('cldr') != 'en') { ?>
    <script
        src="<?php echo base_url(); ?>assets/default/js/locales/bootstrap-datepicker.<?php echo lang('cldr'); ?>.js"></script>
<?php } ?>

<!--[if lt IE 7 ]>
<script src="<?php echo base_url(); ?>assets/default/js/dd_belatedpng.js"></script>
<script
    type="text/javascript"> DD_belatedPNG.fix('img, .png_bg'); //fix any <img> or .png_bg background-images </script>
<![endif]-->

<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
     chromium.org/developers/how-tos/chrome-frame-getting-started -->
<!--[if lt IE 7 ]>
<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
<script type="text/javascript">window.attachEvent('onload', function () {
    CFInstall.check({mode: 'overlay'})
})</script>
<![endif]-->

</body>
</html>
