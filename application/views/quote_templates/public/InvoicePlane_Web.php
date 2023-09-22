<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title> <?php echo get_setting('custom_title', 'InvoicePlane', true); ?> - <?php echo trans('quote'); ?> <?php echo $quote->quote_number; ?> </title>
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
                                <?php echo trans('quote'); ?>
                            </h2>
                            <div class="btn-group">
                                <?php if (in_array($quote->quote_status_id, array(2, 3))): ?>
                                <a href="<?php echo site_url('guest/view/approve_quote/' . $quote_url_key); ?>" class="btn btn-success">
                                    <i class="fa fa-check"></i>
                                    <?php echo trans('approve_this_quote'); ?>
                                </a>
                                <a href="<?php echo site_url('guest/view/reject_quote/' . $quote_url_key); ?>" class="btn btn-danger">
                                    <i class="fa fa-times-circle"></i>
                                    <?php echo trans('reject_this_quote'); ?>
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo site_url('guest/view/generate_quote_pdf/' . $quote_url_key); ?>" class="btn btn-primary">
                                    <i class="fa fa-print"></i>
                                    <?php echo trans('download_pdf'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-1"><!--- This is empty space---></div>
                    <div class="col-5"><!--- This is empty space---></div>
                </div>
                <!--- Ends --->
                <hr/>
                <!--- No Idea what this does --->
                <div class="row">
                    <?php if ($flash_message) { ?>
                        <div class="alert alert-info">
                          <?php echo $flash_message; ?>
                        </div>
                    <?php } else {
                        echo '<br>';
                    } ?>
                </div>
                <!--- Ends --->
                <!--- Company Section --->
                <div class="row mb-3">
                    <!--- Logo --->
                    <div class="col-sm-12 col-md-6 col-lg-5 text-bold">
                        <?php if ($logo = invoice_logo()) { echo $logo . '<br><br>'; } ?>
                    </div>
                    <!--- Logo Ends --->
                    <div class="col-lg-2">
                        <!--- This is empty space--->
                    </div>
                    <!--- Company Address --->
                    <div class="col-sm-12 col-md-6 col-lg-5 text-right">
                        <h4> <?php _htmlsc($quote->user_name); ?> </h4>
                        <?php if ($quote->user_vat_id) {
                            echo lang('vat_id_short') . ': ' . $quote->user_vat_id . '<br>';
                        } ?>
                        <?php if ($quote->user_tax_code) {
                            echo lang('tax_code_short') . ': ' . $quote->user_tax_code . '<br>';
                        } ?>
                        <?php if ($quote->user_address_1) {
                            echo htmlsc($quote->user_address_1) . '<br>';
                        } ?>
                        <?php if ($quote->user_address_2) {
                            echo htmlsc($quote->user_address_2) . '<br>';
                        } ?>
                        <?php if ($quote->user_city) {
                            echo htmlsc($quote->user_city) . ' ';
                        } ?>
                        <?php if ($quote->user_state) {
                            echo htmlsc($quote->user_state) . ' ';
                        } ?>
                        <?php if ($quote->user_zip) {
                            echo htmlsc($quote->user_zip) . '<br>';
                        } ?>
                        <?php if ($quote->user_phone) { ?>
                            <?php echo htmlsc($quote->user_phone); ?>
                            <br>
                            <?php } ?>
                            <?php if ($quote->user_mobile) { ?>
                                <?php echo htmlsc($quote->user_mobile); ?>
                            <?php } ?>
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
                                    <td class="text-bold">
                                        <?php echo trans('Quote #'); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo $quote->quote_number; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-bold">
                                        <?php echo trans('quote_date'); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo date_from_mysql($quote->quote_date_created); ?>
                                    </td>
                                </tr>
                                <tr class="<?php echo ($is_expired ? 'overdue' : '') ?>">
                                    <td class="text-bold">
                                        <?php echo trans('expires'); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo date_from_mysql($quote->quote_date_expires); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-bold">
                                        <?php echo trans('total'); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo format_currency($quote->quote_total); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--- Ends --->
                <!--- Quoted To Section --->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-5">
                        <h2 class="text-bold"> <?php echo ('Quoted To'); ?> </h2>
                    </div>
                    <div class="col-lg-2"><!-- This is empty space --></div>
                    <div class="col-sm-12 col-md-6 col-lg-5 text-right">
                        <h2 class="text-bold"> <?php _htmlsc($quote->client_name); ?> </h2>
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
                        <h5 class="text-bold"><?php echo htmlspecialchars($custom_fields['client']['Company Name']); ?><br><?php if ($quote->client_phone) { echo htmlsc($quote->client_phone); ?> <?php } ?> </h5>
                        <?php endif; ?>
                        <!--- Custom Field Ends --->
                        <br>
                        <!--- Client Details --->
                        <?php if ($quote->client_vat_id) { echo lang('vat_id_short') . ': ' . $quote->client_vat_id . '<br>'; } ?>
                        <?php if ($quote->client_tax_code) { echo lang('tax_code_short') . ': ' . $quote->client_tax_code . '<br>'; } ?>
                        <?php if ($quote->client_address_1) { echo htmlsc($quote->client_address_1) . '<br>'; } ?>
                        <?php if ($quote->client_address_2) { echo htmlsc($quote->client_address_2) . '<br>'; } ?>
                        <?php if ($quote->client_city) { echo htmlsc($quote->client_city) . ' '; } ?>
                        <?php if ($quote->client_state) { echo htmlsc($quote->client_state) . ' '; } ?>
                        <?php if ($quote->client_zip) { echo htmlsc($quote->client_zip) . '<br><br>'; ?>
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
                    <table class="table table-striped table-bordered">
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
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php _htmlsc($item->item_name); ?></td>
                                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                                <td class="amount">
                                    <?php echo format_amount($item->item_quantity); ?>
                                    <?php if ($item->item_product_unit): ?>
                                    <br>
                                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="amount"><?php echo format_currency($item->item_price); ?></td>
                                    <td class="amount"><?php echo format_currency($item->item_discount); ?></td>
                                    <td class="amount"><?php echo format_currency($item->item_total); ?></td>
                                </tr>
                                <?php endforeach ?>
                                <tr>
                                    <td colspan="4"></td>
                                    <td class="text-right"><?php echo trans('subtotal'); ?>:</td>
                                    <td class="amount"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
                                </tr>

                                <?php if ($quote->quote_item_tax_total > 0) { ?>
                                    <tr>
                                        <td class="no-bottom-border" colspan="4"></td>
                                        <td class="text-right"><?php echo trans('item_tax'); ?></td>
                                        <td class="amount"><?php echo format_currency($quote->quote_item_tax_total); ?></td>
                                    </tr>
                                <?php } ?>
                                <?php foreach ($quote_tax_rates as $quote_tax_rate): ?>
                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="text-right">
                                        <?php echo $quote_tax_rate->quote_tax_rate_name . ' ' . format_amount($quote_tax_rate->quote_tax_rate_percent); ?> %
                                    </td>
                                    <td class="amount"><?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?></td>
                                </tr>
                                <?php endforeach ?>
                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="text-right"><?php echo trans('discount'); ?>:</td>
                                    <td class="amount">
                                <?php
                                    if ($quote->quote_discount_percent > 0) {
                                        echo format_amount($quote->quote_discount_percent) . ' %';
                                    } else {
                                        echo format_amount($quote->quote_discount_amount);
                                    }
                                ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="text-right"><?php echo trans('total'); ?></td>
                                    <td class="amount"><?php echo format_currency($quote->quote_total) ?></td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>
            <!--- No Idea what this does either --->
            <div class="row">
                <?php if ($quote->notes) { ?>
                    <div class="col-xs-12 col-md-6">
                        <h4><?php echo trans('notes'); ?></h4>
                        <p><?php echo nl2br(htmlsc($quote->notes)); ?></p>
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
