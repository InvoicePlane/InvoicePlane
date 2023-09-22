<!DOCTYPE html> 
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>
            <?php echo get_setting('custom_title', 'InvoicePlane', true); ?> - <?php echo trans('quote'); ?> <?php echo $quote->quote_number; ?>
        </title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/style.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom.css">
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
                        <h4><?php _htmlsc($quote->user_name); ?></h4>
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
                                <?php echo htmlsc($quote->user_phone); ?><br>
                            <?php } ?>
                            <?php if ($quote->user_mobile) { ?>
                                <?php echo htmlsc($quote->user_mobile); ?>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
                <!-- Ends -->

                <!-- Invoice Details -->
                <table class="w-10 mt-5">
                    <tr>
                        <!-- Empty Space -->
                        <td class="w-5"></td>
                        <!-- Empty Space -->
                        <td class="w-1"></td>
                        <!-- Invoice Table -->
                        <td class="w-4 pt-3">
                            <table class="table table-condensed">
                            <tbody>
                                    <tr>
                                        <td class="text-bold px-1 py-2 bt">
                                            <?php echo trans('Quote #'); ?>
                                        </td>
                                        <td class="text-right px-1 py-2 bt">
                                            <?php echo $quote->quote_number; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold px-1 py-2 bt"><?php echo trans('quote_date') . ':'; ?></td>
                                        <td class="text-right px-1 py-2 bt"><?php echo date_from_mysql($quote->quote_date_created, true); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold px-1 py-2 bt"><?php echo trans('expires') . ': '; ?></td>
                                        <td class="text-right px-1 py-2 bt"><?php echo date_from_mysql($quote->quote_date_expires, true); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold px-1 py-2 bt"><?php echo trans('total') . ': '; ?></td>
                                        <td class="text-right px-1 py-2 bt"><?php echo format_currency($quote->quote_total); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- Ends -->
                
                <!-- Quoted To Section -->
                <table class="w-10 mt-4">
                     <tr>
                        <td class="w-4 text-bold"><h3><?php echo ('Quoted To'); ?></h3></td>
                        <td class="w-2"></td>
                        <td class="w-4 text-right text-bold">
                            <h3><?php _htmlsc($quote->client_name); ?></h3>
                        </td>
                    </tr>
                </table>
                <!-- Ends -->

                <hr/>
                <!-- Client Section -->
                <table class="w-10 mt-2 mb-4">
                    <tr>
                        <td class="w-6">
                            <br>
                            <!-- Client Details -->
                            <?php if ($quote->client_vat_id) {
                                echo lang('vat_id_short') . ': ' . $quote->client_vat_id . '<br>';
                            } ?>
                            <?php if ($quote->client_tax_code) {
                                echo lang('tax_code_short') . ': ' . $quote->client_tax_code . '<br>';
                            } ?>
                            <?php if ($quote->client_address_1) {
                                echo htmlsc($quote->client_address_1) . '<br>';
                            } ?>
                            <?php if ($quote->client_address_2) {
                                echo htmlsc($quote->client_address_2) . '<br>';
                            } ?>
                            <?php if ($quote->client_city) {
                                echo htmlsc($quote->client_city) . ' ';
                            } ?>
                            <?php if ($quote->client_state) {
                                echo htmlsc($quote->client_state) . ' ';
                            } ?>
                            <?php if ($quote->client_zip) {
                                echo htmlsc($quote->client_zip) . '<br><br>';
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

                <!-- Quote Table Section -->
                <table class="w-10 table item-table bt">
                <thead>
                        <tr>
                            <th class="py-2 px-2"><?php echo trans('item'); ?></th>
                            <th class="py-2 px-2"><?php echo trans('description'); ?></th>
                            <th class="text-right py-2 px-2"><?php echo trans('qty'); ?></th>
                            <th class="text-right py-2 px-2"><?php echo trans('price'); ?></th>
                            <th class="text-right py-2 px-2"><?php echo trans('discount'); ?></th>
                            <th class="text-right py-2 px-2"><?php echo trans('total'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="py-2 px-2 bt"><?php _htmlsc($item->item_name); ?></td>
                                <td class="py-2 px-2 bt"><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                                <td class="amount py-2 px-2 bt">
                                    <?php echo format_amount($item->item_quantity); ?>
                                    <?php if ($item->item_product_unit): ?>
                                        <br>
                                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="amount py-2 px-2 bt"><?php echo format_currency($item->item_price); ?></td>
                                <td class="amount py-2 px-2 bt"><?php echo format_currency($item->item_discount); ?></td>
                                <td class="amount py-2 px-2 bt"><?php echo format_currency($item->item_total); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="py-2 px-2 bt" colspan="4"></td>
                            <td class="text-right py-2 px-2 bt"><?php echo trans('subtotal'); ?>:</td>
                            <td class="amount py-2 px-2 bt"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
                        </tr>
                        <?php if ($quote->quote_item_tax_total > 0): ?>
                            <tr>
                                <td class="no-bottom-border py-2 px-2 bt" colspan="4"></td>
                                <td class="text-right py-2 px-2 bt"><?php echo trans('item_tax'); ?></td>
                                <td class="amount py-2 px-2 bt"><?php echo format_currency($quote->quote_item_tax_total); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($quote_tax_rates as $quote_tax_rate): ?>
                            <tr>
                                <td class="no-bottom-border py-2 px-2 bt" colspan="4"></td>
                                <td class="text-right py-2 px-2 bt">
                                    <?php echo $quote_tax_rate->quote_tax_rate_name . ' ' . format_amount($quote_tax_rate->quote_tax_rate_percent); ?> %
                                </td>
                                <td class="amount py-2 px-2 bt"><?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="no-bottom-border py-2 px-2 bt" colspan="4"></td>
                            <td class="text-right py-2 px-2 bt"><?php echo trans('discount'); ?>:</td>
                            <td class="amount py-2 px-2 bt">
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
                            <td class="no-bottom-border py-2 px-2 bt" colspan="4"></td>
                            <td class="text-right py-2 px-2 bt"><?php echo trans('total'); ?></td>
                            <td class="amount py-2 px-2 bt"><?php echo format_currency($quote->quote_total) ?></td>
                        </tr>
                    </tbody>
                </table>
                <!-- End Invoice Table Section -->
            </div>
        </div>
    </body>
</html>