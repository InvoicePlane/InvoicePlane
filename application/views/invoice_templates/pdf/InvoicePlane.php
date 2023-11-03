<!DOCTYPE html> 
    <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <title>
                <?php echo get_setting('custom_title', 'InvoicePlane', true); ?> - <?php _trans('invoice'); ?> <?php echo $invoice->invoice_number; ?>
            </title>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
    </head>
    <body>
        <!-- Main Body -->
        <div class="container">
            <div id="content">
                <!-- Company Section -->
                <table class="w-10">
                    <!-- Company Details -->
                    <tr>
                        <!-- Logo -->
                        <td class="w-4">
                            <?php if ($logo = invoice_logo()) { echo $logo . '<br><br>'; } ?>
                        </td>
                        <!-- Empty Space -->
                        <td class="w-2"></td>
                        <!-- Company Address -->
                        <td class="w-5 text-right">
                            <h4><?php _htmlsc($invoice->user_name); ?></h4>
                            <?php if ($invoice->user_vat_id) {
                                echo lang('vat_id_short') . ': ' . $invoice->user_vat_id . '<br>';
                            } ?>
                            <?php if ($invoice->user_tax_code) {
                                echo lang('tax_code_short') . ': ' . $invoice->user_tax_code . '<br>';
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
                            <?php if ($invoice->user_phone) { ?>
                                <?php echo htmlsc($invoice->user_phone); ?><br>
                            <?php } ?>
                            <?php if ($invoice->user_mobile) { ?>
                                <?php echo htmlsc($invoice->user_mobile); ?>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
                <!-- Ends -->

                <!-- Invoice Details -->
                <table class="w-10 mt-5">
                    <tr>
                        <td class="w-5">
                        <!-- Empty Space -->
                        </td>
                        <!-- Empty Space -->
                        <td class="w-1"></td>
                        <!-- Invoice Table -->
                        <td class="w-4 pt-3">
                            <table class="table table-condensed">
                                <tr>
                                    <td class="text-bold bt py-1 px-1"><?php echo trans('invoice_date') . ':'; ?></td>
                                    <td class="text-right bt py-1 px-1"><?php echo date_from_mysql($invoice->invoice_date_created, true); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold bt py-1 px-1"><?php echo trans('due_date') . ': '; ?></td>
                                    <td class="text-right bt py-1 px-1"><?php echo date_from_mysql($invoice->invoice_date_due, true); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-bold bt py-1 px-1"><?php echo trans('amount_due') . ': '; ?></td>
                                    <td class="text-right bt py-1 px-1"><?php echo format_currency($invoice->invoice_balance); ?></td>
                                </tr>
                                <?php if ($payment_method): ?>
                                <tr>
                                    <td class="text-bold bt py-1 px-1"><?php echo trans('payment_method') . ': '; ?></td>
                                    <td class="text-right bt py-1 px-1"><?php _htmlsc($payment_method->payment_method_name); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- Ends -->
                
                <!-- Invoiced To Section -->
                <table class="w-10 mt-4">
                    <tr>
                        <td class="w-4 text-bold"><h3><?php echo ('Invoiced To'); ?></h3></td>
                        <td class="w-2"></td>
                        <td class="w-4 text-right text-bold mt-3">
                            <h3><?php _htmlsc($invoice->client_name); ?></h3>
                        </td>
                    </tr>
                </table>
                <!-- Ends -->

                <hr/>
                <!-- Client Section -->
                <table class="w-10 mt-3">
                    <tr>
                        <td class="w-6">
                            <br>
                            <!-- Client Details -->
                            <?php if ($invoice->client_vat_id) {
                                echo lang('vat_id_short') . ': ' . $invoice->client_vat_id . '<br>';
                            } ?>
                            <?php if ($invoice->client_tax_code) {
                                echo lang('tax_code_short') . ': ' . $invoice->client_tax_code . '<br>';
                            } ?>
                            <?php if ($invoice->client_address_1) {
                                echo htmlsc($invoice->client_address_1) . '<br>';
                            } ?>
                            <?php if ($invoice->client_address_2) {
                                echo htmlsc($invoice->client_address_2) . '<br>';
                            } ?>
                            <?php if ($invoice->client_city) {
                                echo htmlsc($invoice->client_city) . ' ';
                            } ?>
                            <?php if ($invoice->client_state) {
                                echo htmlsc($invoice->client_state) . ' ';
                            } ?>
                            <?php if ($invoice->client_zip) {
                                echo htmlsc($invoice->client_zip) . '<br><br>';
                            } ?>
                            <!-- Client Details Ends -->
                        </td>
                            <!-- This is empty space -->                        
                        <td class="w-2"></td>
                            <!-- Custom Section -->
                        <td class="w-4">
                            <!--- Empty Space --->
                        </td>
                    </tr>
                </table>
                <!-- Ends -->

                <!-- Invoice Table Section -->
                <table class="w-10 table item-table mt-5 bt">
                    <thead>
                        <tr>
                            <th class="item-name"><?php _trans('item'); ?></th>
                            <th class="item-desc"><?php _trans('description'); ?></th>
                            <th class="item-amount text-right py-2 px-1"><?php _trans('qty'); ?></th>
                            <th class="item-price text-right py-2 px-1"><?php _trans('price'); ?></th>
                            <?php if ($show_item_discounts) : ?>
                                <th class="item-discount text-right py-2 px-1"><?php _trans('discount'); ?></th>
                            <?php endif; ?>
                            <th class="item-total text-right py-2 px-1"><?php _trans('total'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($items as $item) { ?>
                            <tr>
                                <td class="py-2 px-1 bt"><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                                <td class="py-2 px-1 bt"><?php _htmlsc($item->item_name); ?></td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_amount($item->item_quantity); ?>
                                    <?php if ($item->item_product_unit) : ?>
                                        <br>
                                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_currency($item->item_price); ?>
                                </td>
                                <?php if ($show_item_discounts) : ?>
                                    <td class="text-right py-2 px-1 bt">
                                        <?php echo format_currency($item->item_discount); ?>
                                    </td>
                                <?php endif; ?>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_currency($item->item_total); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tbody class="invoice-sums">
                        <tr>
                            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right px-1 py-2 bt">
                                <?php _trans('subtotal'); ?>
                            </td>
                            <td class="text-right py-2 px-1 bt"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
                        </tr>

                        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
                            <tr>
                                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right py-2 px-1 bt">
                                    <?php _trans('item_tax'); ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
                            <tr>
                                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right py-2 px-1 bt">
                                    <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' (' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%)'; ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        <?php if ($invoice->invoice_discount_percent != '0.00') : ?>
                            <tr>
                                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right py-2 px-1 bt">
                                    <?php _trans('discount'); ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_amount($invoice->invoice_discount_percent); ?>%
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($invoice->invoice_discount_amount != '0.00') : ?>
                            <tr>
                                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right py-2 px-1 bt">
                                    <?php _trans('discount'); ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_currency($invoice->invoice_discount_amount); ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right py-2 px-1 bt">
                                <b><?php _trans('total'); ?></b>
                            </td>
                            <td class="text-right py-2 px-1 bt">
                                <b><?php echo format_currency($invoice->invoice_total); ?></b>
                            </td>
                        </tr>
                        <tr>
                            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right py-2 px-1 bt">
                                <?php _trans('paid'); ?>
                            </td>
                            <td class="text-right py-2 px-1 bt">
                                <?php echo format_currency($invoice->invoice_paid); ?>
                            </td>
                        </tr>
                        <tr>
                            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right py-2 px-1 bt">
                                <b><?php _trans('balance'); ?></b>
                            </td>
                            <td class="text-right py-2 px-1 bt">
                                <b><?php echo format_currency($invoice->invoice_balance); ?></b>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- End Invoice Table Section -->
            </div>
        </div>
        <footer>
            <?php if ($invoice->invoice_terms) : ?>
                <div class="notes">
                    <b><?php _trans('terms'); ?></b><br/>
                    <?php echo nl2br(htmlsc($invoice->invoice_terms)); ?>
                </div>
            <?php endif; ?>
        </footer>
    </body>
</html>