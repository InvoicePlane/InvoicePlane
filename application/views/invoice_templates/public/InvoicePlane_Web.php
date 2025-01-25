<?php
$taxes_after_discounts = config_item('taxes_after_discounts');
// $show_taxes = $invoice->invoice_tax_total + $invoice->invoice_item_tax_total != 0; // #idea
?><!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>
        <?php echo get_setting('custom_title', 'InvoicePlane', true); ?>
        - <?php _trans('invoice'); ?> <?php echo $invoice->invoice_number; ?>
    </title>

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom.css">
</head>
<body>

<div class="container">
    <div id="content">

        <div class="webpreview-header">

            <h2><?php _trans('invoice'); ?>&nbsp;<?php echo $invoice->invoice_number; ?></h2>

            <div class="btn-group">
                <?php if ($invoice->sumex_id == NULL) : ?>
                <a href="<?php echo site_url('guest/view/generate_invoice_pdf/' . $invoice_url_key); ?>"
                   class="btn btn-primary">
                    <?php else : ?>
                    <a href="<?php echo site_url('guest/view/generate_sumex_pdf/' . $invoice_url_key); ?>"
                       class="btn btn-primary">
                        <?php endif; ?>
                        <i class="fa fa-print"></i> <?php _trans('download_pdf'); ?>
                    </a>
                    <?php if (get_setting('enable_online_payments') == 1 && $invoice->invoice_balance > 0) { ?>
                        <a href="<?php echo site_url('guest/payment_information/form/' . $invoice_url_key); ?>"
                           class="btn btn-success">
                            <i class="fa fa-credit-card"></i> <?php _trans('pay_now'); ?>
                        </a>
                    <?php } ?>
            </div>

        </div>

        <hr>

        <?php echo $this->layout->load_view('layout/alerts'); ?>

        <div class="invoice">

            <?php
            $logo = invoice_logo();
            if ($logo) {
                echo $logo . '<br><br>';
            }
            ?>

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-5">

                    <h4><?php _htmlsc($invoice->user_name); ?></h4>
                    <p><?php
                        if ($invoice->user_vat_id) {
                            echo trans('vat_id_short') . ': ' . $invoice->user_vat_id . '<br>';
                        }
                        if ($invoice->user_tax_code) {
                            echo trans('tax_code_short') . ': ' . $invoice->user_tax_code . '<br>';
                        }
                        if ($invoice->user_address_1) {
                            echo htmlsc($invoice->user_address_1) . '<br>';
                        }
                        if ($invoice->user_address_2) {
                            echo htmlsc($invoice->user_address_2) . '<br>';
                        }
                        if ($invoice->user_city) {
                            echo htmlsc($invoice->user_city) . ' ';
                        }
                        if ($invoice->user_state) {
                            echo htmlsc($invoice->user_state) . ' ';
                        }
                        if ($invoice->user_zip) {
                            echo htmlsc($invoice->user_zip) . '<br>';
                        }
                        if ($invoice->user_phone) {
                            _trans('phone_abbr');
                            echo ': ' . htmlsc($invoice->user_phone) . '<br>';
                        }
                        if ($invoice->user_fax) {
                            _trans('fax_abbr');
                            echo ': ' . htmlsc($invoice->user_fax);
                        }
                    ?></p>

                </div>
                <div class="col-lg-2"></div>
                <div class="col-xs-12 col-md-6 col-lg-5 text-right">

                    <h4><?php _htmlsc(format_client($invoice)); ?></h4>
                    <p><?php
                        if ($invoice->client_vat_id) {
                            _trans('vat_id_short');
                            echo ': ' . $invoice->client_vat_id . '<br>';
                        }
                        if ($invoice->client_tax_code) {
                            _trans('tax_code_short');
                            echo ': ' . $invoice->client_tax_code . '<br>';
                        }
                        if ($invoice->client_address_1) {
                            echo htmlsc($invoice->client_address_1) . '<br>';
                        }
                        if ($invoice->client_address_2) {
                            echo htmlsc($invoice->client_address_2) . '<br>';
                        }
                        if ($invoice->client_city) {
                            echo htmlsc($invoice->client_city) . ' ';
                        }
                        if ($invoice->client_state) {
                            echo htmlsc($invoice->client_state) . ' ';
                        }
                        if ($invoice->client_zip) {
                            echo htmlsc($invoice->client_zip) . '<br>';
                        }
                        if ($invoice->client_phone) {
                            echo trans('phone_abbr') . ': ' . htmlsc($invoice->client_phone) . '<br>';
                        }
                    ?></p>

                    <br>

                    <table class="table table-condensed">
                        <tbody>
                        <tr>
                            <td><?php _trans('invoice_date'); ?></td>
                            <td style="text-align:right;"><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                        </tr>
                        <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                            <td><?php _trans('due_date'); ?></td>
                            <td class="amount">
                                <?php echo date_from_mysql($invoice->invoice_date_due); ?>
                            </td>
                        </tr>
                        <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                            <td><?php _trans('amount_due'); ?></td>
                            <td style="text-align:right;"><?php echo format_currency($invoice->invoice_balance); ?></td>
                        </tr>
                        <?php if ($payment_method): ?>
                            <tr>
                                <td><?php _trans('payment_method') . ': '; ?></td>
                                <td><?php _htmlsc($payment_method->payment_method_name); ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>

            <br>

            <div class="invoice-items">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th><?php _trans('item'); ?></th>
                            <th><?php _trans('description'); ?></th>
                            <th class="amount"><?php _trans('qty'); ?></th>
                            <th class="amount"><?php _trans('price'); ?></th>
                            <th class="amount"><?php _trans('discount'); ?></th>
                            <th class="amount"><?php _trans('total'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?php _htmlsc($item->item_name); ?></td>
                                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                                <td class="amount">
                                    <?php echo format_quantity($item->item_quantity); ?>
                                    <?php if ($item->item_product_unit) : ?>
                                        <br>
                                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="amount"><?php echo format_currency($item->item_price); ?></td>
                                <td class="amount"><?php echo format_currency($item->item_discount); ?></td>
                                <td class="amount"><?php echo format_currency($item->item_subtotal); ?></td>
                            </tr>
                        <?php endforeach ?>

                        <tr>
                            <td colspan="4"></td>
                            <td class="amount"><?php _trans('subtotal'); ?>:</td>
                            <td class="amount"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
                        </tr>

                        <?php if ($taxes_after_discounts) : ?>
                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount"><?php _trans('discount'); ?></td>
                            <td class="amount">
                                <?php
                                    if ($invoice->invoice_discount_percent > 0) {
                                        echo format_amount($invoice->invoice_discount_percent) . '&nbsp;%';
                                    } else {
                                        echo format_currency($invoice->invoice_discount_amount);
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php endif ?>

                        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="amount"><?php _trans('item_tax'); ?></td>
                                <td class="amount"><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
                            </tr>
                        <?php } ?>

                        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="amount">
                                    <?php
                                        _htmlsc($invoice_tax_rate->invoice_tax_rate_name);
                                        echo ' ' . format_amount($invoice_tax_rate->invoice_tax_rate_percent)
                                    ?>&nbsp;%
                                </td>
                                <td class="amount"><?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?></td>
                            </tr>
                        <?php endforeach ?>

                        <?php if ($taxes_after_discounts) : ?>
                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount"><?php _trans('discount'); ?></td>
                            <td class="amount">
                                <?php
                                    if ($invoice->invoice_discount_percent > 0) {
                                        echo format_amount($invoice->invoice_discount_percent) . '&nbsp;%';
                                    } else {
                                        echo format_currency($invoice->invoice_discount_amount);
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php endif ?>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount"><?php _trans('total'); ?>:</td>
                            <td class="amount"><?php echo format_currency($invoice->invoice_total); ?></td>
                        </tr>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount"><?php _trans('paid'); ?></td>
                            <td class="amount"><?php echo format_currency($invoice->invoice_paid) ?></td>
                        </tr>
                        <tr class="<?php echo ($invoice->invoice_balance > 0) ? 'overdue' : 'text-success'; ?>">
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount"><?php _trans('balance'); ?></td>
                            <td class="amount">
                                <b><?php echo format_currency($invoice->invoice_balance) ?></b>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <?php if ($invoice->invoice_balance == 0) {
                    echo '<img src="' . base_url('assets/core/img/paid.png') . '" class="paid-stamp">';
                } ?>
                <?php if ($is_overdue) {
                    echo '<img src="' . base_url('assets/core/img/overdue.png') . '" class="overdue-stamp">';
                } ?>

            </div><!-- .invoice-items -->

            <hr>

            <?php if (get_setting('qr_code') && $invoice->invoice_balance > 0) : ?>
                <table class="invoice-qr-code-table">
                    <tr>
                        <td>
                            <div>
                                <strong><?php _trans('qr_code_settings_recipient'); ?>:</strong>
                                <?php echo get_setting('qr_code_recipient'); ?>
                            </div>
                            <div>
                                <strong><?php _trans('qr_code_settings_iban'); ?>:</strong>
                                <?php echo get_setting('qr_code_iban'); ?>
                            </div>
                            <div>
                                <strong><?php _trans('qr_code_settings_bic'); ?>:</strong>
                                <?php echo get_setting('qr_code_bic'); ?>
                            </div>
                            <div>
                                <strong><?php _trans('qr_code_settings_remittance_text'); ?>:</strong>
                                <?php echo parse_template($invoice, get_setting('qr_code_remittance_text')); ?>
                            </div>
                        </td>
                        <td class="amount">
                            <?php echo invoice_qrcode($invoice->invoice_id); ?>
                        </td>
                    </tr>
                </table>

                <hr>
            <?php endif; ?>

            <div class="row">

                <?php if ($invoice->invoice_terms) { ?>
                    <div class="col-xs-12 col-md-6">
                        <h4><?php _trans('terms'); ?></h4>
                        <p><?php echo nl2br(htmlsc($invoice->invoice_terms)); ?></p>
                    </div>
                <?php } ?>

                <?php
                if (count($attachments) > 0) { ?>
                    <div class="col-xs-12 col-md-6">
                        <h4><?php _trans('attachments'); ?></h4>
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <?php foreach ($attachments as $attachment) { ?>
                                    <tr class="attachments">
                                        <td><?php echo $attachment['name']; ?></td>
                                        <td>
                                            <a href="<?php echo site_url('guest/get/attachment/' . $attachment['fullname']); ?>"
                                               class="btn btn-primary btn-sm">
                                                <i class="fa fa-download"></i> <?php _trans('download') ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>

            </div>

        </div><!-- .invoice-items -->
    </div><!-- #content -->
</div>

</body>
</html>
