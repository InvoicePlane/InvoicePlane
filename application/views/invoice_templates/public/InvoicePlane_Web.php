<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title> <?php echo get_setting('custom_title', 'InvoicePlane', true); ?> - <?php echo trans('invoice'); ?> <?php echo $invoice->invoice_number; ?> </title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom.css">
</head>
    <body>
        <!--- Main Body --->
        <div class="container">
            <div id="content">
                <!--- Header Buttons --->
                <div class="row">
                    <div class="col-6">
                    <div class="webpreview-header">
                    <h2>
                        <?php echo trans('invoice'); ?>
                    </h2>
                        <div class="btn-group">
                            <?php if ($invoice->sumex_id == NULL) : ?>
                                <a href="<?php echo site_url('guest/view/generate_invoice_pdf/' . $invoice_url_key); ?>" class="btn btn-primary">
                                <?php else : ?> <a href="<?php echo site_url('guest/view/generate_sumex_pdf/' . $invoice_url_key); ?>" class="btn btn-primary"> <?php endif; ?>
                                    <i class="fa fa-print"></i> <?php echo trans('download_pdf'); ?>
                                </a>
                                <?php if (get_setting('enable_online_payments') == 1 && $invoice->invoice_balance > 0) { ?>
                                    <a href="<?php echo site_url('guest/payment_information/form/' . $invoice_url_key); ?>" class="btn btn-success">
                                        <i class="fa fa-credit-card"></i> <?php echo trans('pay_now'); ?>
                                    </a>
                                <?php } ?>
                        </div>
                </div>
                    <div class="col-1"><!--- This is empty space---></div>
                    <div class="col-5"><!--- This is empty space---></div>
                </div>
                <!--- Ends --->
                <!--- No Idea what this does --->
                <div class="row">
                <?php echo $this->layout->load_view('layout/alerts'); ?>
                </div>
                <!--- Ends --->
                <hr/>
                <!--- Company Section --->
                <div class="row mb-3">
                    <!--- Logo --->
                    <div class="col-sm-12 col-md-6 col-lg-5 text-bold">
                    <?php
                        $logo = invoice_logo();
                        if ($logo) {
                            echo $logo . '<br><br>';
                        }
                    ?>
                    </div>
                    <!--- Logo Ends --->
                    <div class="col-lg-2">
                        <!--- This is empty space--->
                    </div>
                    <!--- Company Address --->
                    <div class="col-sm-12 col-md-6 col-lg-5 text-right">
                    <h4><?php _htmlsc($invoice->user_name); ?></h4>
                    <p><?php if ($invoice->user_vat_id) {
                            echo lang("vat_id_short") . ": " . $invoice->user_vat_id . '<br>';
                        } ?>
                        <?php if ($invoice->user_tax_code) {
                            echo lang("tax_code_short") . ": " . $invoice->user_tax_code . '<br>';
                        } ?>
                        <?php if ($invoice->user_address_1) {
                            echo htmlsc($invoice->user_address_1) . '<br>';
                        } ?>
                        <?php if ($invoice->user_address_2) {
                            echo htmlsc($invoice->user_address_2) . '<br>';
                        } ?>
                        <?php if ($invoice->user_city) {
                            echo htmlsc($invoice->user_city) . ' ';
                        } ?>
                        <?php if ($invoice->user_state) {
                            echo htmlsc($invoice->user_state) . ' ';
                        } ?>
                        <?php if ($invoice->user_zip) {
                            echo htmlsc($invoice->user_zip) . '<br>';
                        } ?>
                        <?php if ($invoice->user_phone) { ?><?php echo trans('phone_abbr'); ?>: <?php echo htmlsc($invoice->user_phone); ?>
                            <br><?php } ?>
                        <?php if ($invoice->user_fax) { ?><?php echo trans('fax_abbr'); ?>: <?php echo htmlsc($invoice->user_fax); ?><?php } ?>
                    </p>
                    </div>
                </div>
                <!--- Ends --->
                <!--- Quote Section --->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-5"><!--- This is empty space --></div>
                    <div class="col-lg-2"><!-- This is empty space --></div>
                    <div class="col-sm-12 col-md-6 col-lg-5">
                        <!--- Quote Table -->
                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td><?php echo trans('invoice_date'); ?></td>
                                    <td class="text-right"><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                                </tr>
                                <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                                    <td><?php echo trans('due_date'); ?></td>
                                    <td class="text-right">
                                        <?php echo date_from_mysql($invoice->invoice_date_due); ?>
                                    </td>
                                </tr>
                                <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                                    <td><?php echo trans('amount_due'); ?></td>
                                    <td class="text-right"><?php echo format_currency($invoice->invoice_balance); ?></td>
                                </tr>
                                <?php if ($payment_method): ?>
                                <tr>
                                    <td><?php echo trans('payment_method') . ': '; ?></td>
                                    <td class="text-right"><?php _htmlsc($payment_method->payment_method_name); ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                    </table>
                    </div>
                </div>
                <!--- Ends --->
                <!--- Quoted To Section --->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-5">
                        <h2 class="text-bold"> <?php echo ('Inovoiced To'); ?> </h2>
                    </div>
                    <div class="col-lg-2"><!-- This is empty space --></div>
                    <div class="col-sm-12 col-md-6 col-lg-5 text-right">
                        <h2 class="text-bold"> <?php _htmlsc($invoice->client_name); ?> </h2>
                        <h5 class="text-bold"> <?php if (isset($custom_fields['client']['Client Code'])): ?> <?php echo trans('Client Code:'); ?> <?php echo htmlspecialchars($custom_fields['client']['Client Code']); ?> <?php endif; ?> </h5>
                    </div>
                </div>
                <!--- Ends --->
                <hr/>
                <!--- Client Section --->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-5">
                        <!--- Custom Field Comany Name & Client's Phone --->
                        <?php if (isset($custom_fields['client']['Company Name'])): ?>
                        <h5 class="text-bold"><?php echo htmlspecialchars($custom_fields['client']['Company Name']); ?><br><?php if ($invoice->client_phone) { echo htmlsc($invoice->client_phone); ?> <?php } ?> </h5>
                        <?php endif; ?>
                        <!--- Custom Field Ends --->
                        <br>
                        <!--- Client Details --->
                        <?php if ($invoice->client_vat_id) { echo lang('vat_id_short') . ': ' . $invoice->client_vat_id . '<br>'; } ?>
                        <?php if ($invoice->client_tax_code) { echo lang('tax_code_short') . ': ' . $invoice->client_tax_code . '<br>'; } ?>
                        <?php if ($invoice->client_address_1) { echo htmlsc($invoice->client_address_1) . '<br>'; } ?>
                        <?php if ($invoice->client_address_2) { echo htmlsc($invoice->client_address_2) . '<br>'; } ?>
                        <?php if ($invoice->client_city) { echo htmlsc($invoice->client_city) . ' '; } ?>
                        <?php if ($invoice->client_state) { echo htmlsc($invoice->client_state) . ' '; } ?>
                        <?php if ($invoice->client_zip) { echo htmlsc($invoice->client_zip) . '<br><br>'; ?>
                        <?php } ?>
                        <!--- Client Details Ends --->
                    </div>
                </div>
                <!--- Ends --->
            </div>
        </div>
        <!--- Ends --->
        <!--- Quote Table Section --->
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                <table class="table table-striped table-bordered w-10">
                        <thead>
                        <tr>
                            <th><?php echo trans('item'); ?></th>
                            <th><?php echo trans('description'); ?></th>
                            <th class="text-right"><?php echo trans('qty'); ?></th>
                            <th class="text-right"><?php echo trans('price'); ?></th>
                            <th class="text-right"><?php echo trans('discount'); ?></th>
                            <th class="text-right"><?php echo trans('total'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?php _htmlsc($item->item_name); ?></td>
                                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                                <td class="amount">
                                    <?php echo format_amount($item->item_quantity); ?>
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
                            <td class="text-right"><?php echo trans('subtotal'); ?>:</td>
                            <td class="amount"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
                        </tr>

                        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="text-right"><?php echo trans('item_tax'); ?></td>
                                <td class="amount"><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
                            </tr>
                        <?php } ?>

                        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="text-right">
                                    <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' ' . format_amount($invoice_tax_rate->invoice_tax_rate_percent); ?>
                                    %
                                </td>
                                <td class="amount"><?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?></td>
                            </tr>
                        <?php endforeach ?>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="text-right"><?php echo trans('discount'); ?>:</td>
                            <td class="amount">
                                <?php
                                if ($invoice->invoice_discount_percent > 0) {
                                    echo format_amount($invoice->invoice_discount_percent) . ' %';
                                } else {
                                    echo format_amount($invoice->invoice_discount_amount);
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="text-right"><?php echo trans('total'); ?>:</td>
                            <td class="amount"><?php echo format_currency($invoice->invoice_total); ?></td>
                        </tr>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="text-right"><?php echo trans('paid'); ?></td>
                            <td class="amount"><?php echo format_currency($invoice->invoice_paid) ?></td>
                        </tr>
                        <tr class="<?php echo ($invoice->invoice_balance > 0); ?>">
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="text-right"><?php echo trans('balance'); ?></td>
                            <td class="amount">
                                <b><?php echo format_currency($invoice->invoice_balance) ?></b>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--- No Idea what this does either --->
            <div class="row">
                <?php if ($invoice->notes) { ?>
                    <div class="col-xs-12 col-md-6">
                        <h4><?php echo trans('notes'); ?></h4>
                        <p><?php echo nl2br(htmlsc($invoice->notes)); ?></p>
                    </div>
                <?php } ?>
                <?php if (count($attachments) > 0) { ?>
                    <div class="col-xs-12 col-md-6">
                        <h4><?php echo trans('attachments'); ?></h4>
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <?php foreach ($attachments as $attachment) { ?>
                                    <tr class="attachments">
                                        <td><?php echo $attachment['name']; ?></td>
                                        <td>
                                            <a href="<?php echo site_url('guest/get/attachment/' . $attachment['fullname']); ?>" class="btn btn-primary btn-sm">
                                                <i class="fa fa-download"></i>
                                                <?php _trans('download') ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!--- Ends --->
        </div>
        <!--- Ends --->
    </body>
</html>
