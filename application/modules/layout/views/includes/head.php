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
<meta name="_csrf" content="<?= $this->security->get_csrf_hash() ?>">

<link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/default/img/favicon.png">

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/style.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom.css">

<?php if ($this->mdl_settings->setting('monospace_amounts') == 1) { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/monospace.css">
<?php } ?>

<!--[if lt IE 9]>
<script src="<?php echo base_url(); ?>assets/default/js/legacy.min.js"></script>
<![endif]-->

<script src="<?php echo base_url(); ?>assets/default/js/dependencies.min.js"></script>

<script>
    Dropzone.autoDiscover = false;

    function csrf() {
        return $('meta[name=_csrf]').attr('content');
    }

    $(function () {
        $('.nav-tabs').tab();
        $('.tip').tooltip();

        $('body').on('focus', ".datepicker", function () {
            $(this).datepicker({
                autoclose: true,
                format: '<?php echo date_format_datepicker(); ?>',
                language: '<?php echo trans('cldr'); ?>',
                weekStart: '<?php echo $this->mdl_settings->setting('first_day_of_week'); ?>',
                todayBtn: "linked"
            });
        });

        $(document).on('click', '.create-invoice', function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_invoice'); ?>");
        });

        $(document).on('click', '.create-quote', function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>");
        });

        $(document).on('click', '#btn_quote_to_invoice', function () {
            quote_id = $(this).data('quote-id');
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_quote_to_invoice'); ?>/" + quote_id);
        });

        $(document).on('click', '#btn_copy_invoice', function () {
            invoice_id = $(this).data('invoice-id');
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_copy_invoice'); ?>", {invoice_id: invoice_id});
        });

        $(document).on('click', '#btn_create_credit', function () {
            invoice_id = $(this).data('invoice-id');
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_credit'); ?>", {invoice_id: invoice_id});
        });

        $(document).on('click', '#btn_copy_quote', function () {
            quote_id = $(this).data('quote-id');
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_copy_quote'); ?>", {quote_id: quote_id});
        });

        $(document).on('click', '.client-create-invoice', function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_invoice'); ?>", {
                client_name: $(this).data('client-name')
            });
        });

        $(document).on('click', '.client-create-quote', function () {
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
