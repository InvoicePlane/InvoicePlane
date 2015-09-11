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

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php
        if ($this->mdl_settings->setting('custom_title') != '') {
            echo $this->mdl_settings->setting('custom_title');
        } else {
            echo 'InvoicePlane';
        } ?> - <?php echo lang('invoice'); ?> <?php echo $invoice->invoice_number; ?></title>

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom.css">

    <style>
        body {
            color: #333 !important;
            padding: 0 0 25px;
            height: auto;
        }

        table {
            width: 100%;
        }

        #header table {
            width: 100%;
            padding: 0px;
            margin-bottom: 15px;
        }

        #header table td {
            vertical-align: text-top;
        }

        #invoice-to {
            margin-bottom: 15px;
        }

        #invoice-to td {
            text-align: left
        }

        #invoice-to h3 {
            margin-bottom: 10px;
        }

        .seperator {
            height: 25px
        }

        .no-bottom-border {
            border: none !important;
            background-color: white !important;
        }

        .alignr {
            text-align: right;
        }

        #invoice-container {
            margin: 25px auto;
            width: 900px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.25);
        }

        #menu-container {
            margin: 25px auto;
            width: 900px;
        }

        .flash-message {
            font-size: 120%;
            font-weight: bold;
        }
    </style>

</head>

<body>

<div id="menu-container">

    <a href="<?php echo site_url('guest/view/generate_invoice_pdf/' . $invoice_url_key); ?>" class="btn btn-primary"><i
            class="fa fa-print"></i> <?php echo lang('download_pdf'); ?></a>
    <?php if ($this->mdl_settings->setting('merchant_enabled') == 1 and $invoice->invoice_balance > 0) { ?><a
        href="<?php echo site_url('guest/payment_handler/make_payment/' . $invoice_url_key); ?>"
        class="btn btn-success"><i class="fa fa-credit-card"></i> <?php echo lang('pay_now'); ?></a><?php } ?>

    <?php if ($flash_message) { ?>
        <div class="alert flash-message">
            <?php echo $flash_message; ?>
        </div>
    <?php } ?>
</div>

<div id="invoice-container">

    <div id="header">
        <table>
            <tr>
                <td id="company-name">
                    <?php echo invoice_logo(); ?>
                    <h2><?php echo $invoice->user_name; ?></h2>

                    <p><?php if ($invoice->user_vat_id) {
                            echo lang("vat_id_short") . ": " . $invoice->user_vat_id . '<br>';
                        } ?>
                        <?php if ($invoice->user_tax_code) {
                            echo lang("tax_code_short") . ": " . $invoice->user_tax_code . '<br>';
                        } ?>
                        <?php if ($invoice->user_address_1) {
                            echo $invoice->user_address_1 . '<br>';
                        } ?>
                        <?php if ($invoice->user_address_2) {
                            echo $invoice->user_address_2 . '<br>';
                        } ?>
                        <?php if ($invoice->user_city) {
                            echo $invoice->user_city . ' ';
                        } ?>
                        <?php if ($invoice->user_state) {
                            echo $invoice->user_state . ' ';
                        } ?>
                        <?php if ($invoice->user_zip) {
                            echo $invoice->user_zip . '<br>';
                        } ?>
                        <?php if ($invoice->user_phone) { ?><?php echo lang('phone_abbr'); ?>: <?php echo $invoice->user_phone; ?>
                            <br><?php } ?>
                        <?php if ($invoice->user_fax) { ?><?php echo lang('fax_abbr'); ?>: <?php echo $invoice->user_fax; ?><?php } ?>
                    </p>
                </td>
                <td class="alignr"><h2><?php echo lang('invoice'); ?><?php echo $invoice->invoice_number; ?></h2></td>
            </tr>
        </table>
    </div>
    <div id="invoice-to">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h3><?php echo $invoice->client_name; ?></h3>

                    <p><?php if ($invoice->client_vat_id) {
                            echo lang("vat_id_short") . ": " . $invoice->client_vat_id . '<br>';
                        } ?>
                        <?php if ($invoice->client_tax_code) {
                            echo lang("tax_code_short") . ": " . $invoice->client_tax_code . '<br>';
                        } ?>
                        <?php if ($invoice->client_address_1) {
                            echo $invoice->client_address_1 . '<br>';
                        } ?>
                        <?php if ($invoice->client_address_2) {
                            echo $invoice->client_address_2 . '<br>';
                        } ?>
                        <?php if ($invoice->client_city) {
                            echo $invoice->client_city . ' ';
                        } ?>
                        <?php if ($invoice->client_state) {
                            echo $invoice->client_state . ' ';
                        } ?>
                        <?php if ($invoice->client_zip) {
                            echo $invoice->client_zip . '<br>';
                        } ?>
                        <?php if ($invoice->client_phone) { ?><?php echo lang('phone_abbr'); ?>: <?php echo $invoice->client_phone; ?>
                            <br><?php } ?>
                    </p>
                </td>
                <td style="width:30%;"></td>
                <td style="width:25%;">
                    <table>
                        <tbody>
                        <tr>
                            <td><?php echo lang('invoice_date'); ?></td>
                            <td style="text-align:right;"><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('due_date'); ?></td>
                            <td style="text-align:right;"><?php echo date_from_mysql($invoice->invoice_date_due); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('payment_method'); ?></td>
                            <td style="text-align:right;"><?php if ($payment_method != null) {
                                    echo $payment_method->payment_method_name;
                                } ?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('amount_due'); ?></td>
                            <td style="text-align:right;"><?php echo format_currency($invoice->invoice_balance); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div id="invoice-items">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?php echo lang('item'); ?></th>
                <th><?php echo lang('description'); ?></th>
                <th><?php echo lang('qty'); ?></th>
                <th><?php echo lang('price'); ?></th>
                <th><?php echo lang('total'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item) : ?>
                <tr>
                    <td><?php echo $item->item_name; ?></td>
                    <td><?php echo nl2br($item->item_description); ?></td>
                    <td><?php echo format_amount($item->item_quantity, $this->mdl_settings->setting('item_amount_decimal_places')); ?></td>
                    <td><?php echo format_currency($item->item_price, $this->mdl_settings->setting('item_price_decimal_places')); ?></td>
                    <td><?php echo format_currency($item->item_subtotal); ?></td>
                </tr>
            <?php endforeach ?>
            <tr>
                <td colspan="3"></td>
                <td><?php echo lang('subtotal'); ?>:</td>
                <td><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
            </tr>
            <?php if ($invoice->invoice_item_tax_total > 0) { ?>
                <tr>
                    <td class="no-bottom-border" colspan="3"></td>
                    <td><?php echo lang('item_tax'); ?></td>
                    <td><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
                </tr>
            <?php } ?>
            <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
                <tr>
                    <td class="no-bottom-border" colspan="3"></td>
                    <td><?php echo $invoice_tax_rate->invoice_tax_rate_name . ' ' . $invoice_tax_rate->invoice_tax_rate_percent; ?>
                        %
                    </td>
                    <td><?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?></td>
                </tr>
            <?php endforeach ?>
            <tr>
                <td class="no-bottom-border" colspan="3"></td>
                <td><?php echo lang('total'); ?>:</td>
                <td><?php echo format_currency($invoice->invoice_total); ?></td>
            </tr>
            <tr>
                <td class="no-bottom-border" colspan="3"></td>
                <td><?php echo lang('paid'); ?></td>
                <td><?php echo format_currency($invoice->invoice_paid) ?></td>
            </tr>
            <tr>
                <td class="no-bottom-border" colspan="3"></td>
                <td><?php echo lang('balance'); ?></td>
                <td><strong><?php echo format_currency($invoice->invoice_balance) ?></strong></td>
            </tr>
            </tbody>
        </table>
        <div class="seperator"></div>

        <?php if ($invoice->invoice_terms) { ?>
            <h4><?php echo lang('terms'); ?></h4>
            <p><?php echo nl2br($invoice->invoice_terms); ?></p>
        <?php } ?>
    </div>

</div>

</body>
</html>