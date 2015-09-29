<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom.css">

    <style>
        .color-l {
            color: #aaa;
        }

        .color-n {
            color: #888;
        }

        .color-d {
            color: #555;
        }

        .border-bottom-l {
            border-color: #aaa;
        }

        .border-bottom-n {
            border-color: #888;
        }

        .border-bottom-d {
            border-color: #555;
        }

        .border-top-l {
            border-color: #aaa;
        }

        .border-top-n {
            border-color: #888;
        }

        .border-top-d {
            border-color: #555;
        }

        .background-l {
            background-color: #eee;
        }

        .company-name,
        .invoice-id {
            color: #333 !important;
        }
    </style>

</head>
<body>
<div id="header">
    <table>
        <tr>
            <td style="width:60%">
                <div style="display: block; height: 2cm"></div>

                <div class="invoice-to">
                    <p><?php echo lang('bill_to'); ?>:</p>

                    <p><b><?php echo $invoice->client_name; ?></b><br/>
                        <?php if ($invoice->client_vat_id) {
                            echo lang('vat_id_short') . ': ' . $invoice->client_vat_id . '<br/>';
                        } ?>
                        <?php if ($invoice->client_tax_code) {
                            echo lang('tax_code_short') . ': ' . $invoice->client_tax_code . '<br/>';
                        } ?>
                        <?php if ($invoice->client_address_1) {
                            echo $invoice->client_address_1 . '<br/>';
                        } ?>
                        <?php if ($invoice->client_address_2) {
                            echo $invoice->client_address_2 . '<br/>';
                        } ?>
                        <?php if ($invoice->client_city) {
                            echo $invoice->client_city . ' ';
                        } ?>
                        <?php if ($invoice->client_zip) {
                            echo $invoice->client_zip . '<br/>';
                        } ?>
                        <?php if ($invoice->client_state) {
                            echo $invoice->client_state . '<br/>';
                        } ?>

                        <?php if ($invoice->client_phone) { ?>
                            <?php echo lang('phone_abbr'); ?>: <?php echo $invoice->client_phone; ?><br/>
                        <?php } ?>
                    </p>
                </div>

            </td>

            <td class="text-right" style="width:40%;">
                <div class="company-details">
                    <?php echo invoice_logo_pdf(); ?>
                    <h3 class="company-name text-right">
                        <?php echo $invoice->user_name; ?>
                    </h3>

                    <p class="text-right">
                        <?php if ($invoice->user_vat_id) {
                            echo lang('vat_id_short') . ': ' . $invoice->user_vat_id . '<br/>';
                        } ?>
                        <?php if ($invoice->user_tax_code) {
                            echo lang('tax_code_short') . ': ' . $invoice->user_tax_code . '<br/>';
                        } ?>
                        <?php if ($invoice->user_address_1) {
                            echo $invoice->user_address_1 . '<br/>';
                        } ?>
                        <?php if ($invoice->user_address_2) {
                            echo $invoice->user_address_2 . '<br/>';
                        } ?>
                        <?php if ($invoice->user_city) {
                            echo $invoice->user_city . ' ';
                        } ?>

                        <?php if ($invoice->user_zip) {
                            echo $invoice->user_zip . '<br/>';
                        } ?>
                        <?php if ($invoice->user_state) {
                            echo $invoice->user_state . '<br/>';
                        } ?>
                        <?php if ($invoice->user_phone) {
                            ?><?php echo lang('phone_abbr'); ?>: <?php echo $invoice->user_phone; ?><br><?php
                        } ?>
                        <?php if ($invoice->user_fax) {
                            ?><?php echo lang('fax_abbr'); ?>: <?php echo $invoice->user_fax; ?><?php
                        } ?>
                    </p>
                </div>
                <br/>

                <div class="invoice-details">
                    <table>
                        <tbody>
                        <tr>
                            <td class="text-right color-n">
                                <?php echo lang('invoice_date'); ?>: &nbsp;
                            </td>
                            <td class="text-right color-n">
                                <?php echo date_from_mysql($invoice->invoice_date_created, true); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right color-n">
                                <?php echo lang('due_date'); ?>: &nbsp;
                            </td>
                            <td class="text-right color-n">
                                <?php echo date_from_mysql($invoice->invoice_date_due, true); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right color-n">
                                <?php echo lang('payment_method'); ?>: &nbsp;
                            </td>
                            <td class="text-right color-n">
                                <?php if ($payment_method != null) {
                                    echo $payment_method->payment_method_name;
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right color-n">
                                <?php echo lang('amount_due'); ?>: &nbsp;
                            </td>
                            <td class="text-right color-n">
                                <?php echo format_currency($invoice->invoice_balance); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </td>
        </tr>
    </table>
</div>

<br/>

<h2 class="invoice-id"><?php echo lang('invoice'); ?><?php echo $invoice->invoice_number; ?></h2>
<br/>

<div class="invoice-items">
    <table class="table table-striped" style="width: 100%;">
        <thead>
        <tr class="border-bottom-d">
            <th class="color-d"><?php echo lang('item'); ?></th>
            <th class="color-d"><?php echo lang('description'); ?></th>
            <th class="text-right color-d"><?php echo lang('qty'); ?></th>
            <th class="text-right color-d"><?php echo lang('price'); ?></th>
            <th class="text-right color-d"><?php echo lang('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        $linecounter = 0;
        foreach ($items as $item) { ?>
            <tr class="border-bottom-n <?php echo($linecounter % 2 ? 'background-l' : '') ?>">
                <td><?php echo $item->item_name; ?></td>
                <td><?php echo nl2br($item->item_description); ?></td>
                <td class="text-right">
                    <?php echo format_amount($item->item_quantity, $this->mdl_settings->setting('item_amount_decimal_places')); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_price, $this->mdl_settings->setting('item_price_decimal_places')); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_subtotal); ?>
                </td>
            </tr>
            <?php $linecounter++; ?>
        <?php } ?>

        </tbody>
    </table>
    <table>
        <tr>
            <td class="text-right">
                <table class="amount-summary">
                    <tr>
                        <td class="text-right color-n">
                            <?php echo lang('subtotal'); ?>:
                        </td>
                        <td class="text-right color-n">
                            <?php echo format_currency($invoice->invoice_item_subtotal); ?>
                        </td>
                    </tr>
                    <?php if ($invoice->invoice_item_tax_total > 0) { ?>
                        <tr>
                            <td class="text-right color-n">
                                <?php echo lang('item_tax'); ?>
                            </td>
                            <td class="text-right color-n">
                                <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
                        <tr>
                            <td class="text-right color-n">
                                <?php echo $invoice_tax_rate->invoice_tax_rate_name . ' ' . $invoice_tax_rate->invoice_tax_rate_percent; ?>
                                %
                            </td>
                            <td class="text-right color-n">
                                <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                            </td>
                        </tr>
                    <?php endforeach ?>

                    <tr class="border-top-l amount-total">
                        <td class="text-right color-d">
                            <?php echo lang('total'); ?>:
                        </td>
                        <td class="text-right color-d">
                            <?php echo format_currency($invoice->invoice_total); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right color-d">
                            <?php echo lang('paid'); ?>:
                        </td>
                        <td class="text-right color-d">
                            <?php echo format_currency($invoice->invoice_paid) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right color-d">
                            <b><?php echo lang('balance'); ?>:</b>
                        </td>
                        <td class="text-right color-d">
                            <b><?php echo format_currency($invoice->invoice_balance) ?></b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="seperator border-bottom-l"></div>
    <?php if ($invoice->invoice_terms) { ?>
        <h4><?php echo lang('terms'); ?></h4>
        <p><?php echo nl2br($invoice->invoice_terms); ?></p>
    <?php } ?>

</div>
</body>
</html>
