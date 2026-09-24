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
                            <?php if ($logo = invoice_logo()) {
                            echo $logo . '<br><br>';
                            } ?>
                        </td>
                        <!-- Empty Space -->
                        <td class="w-2"></td>
                        <!-- Company Address -->
                        <td class="w-5 text-right">
                            <h4><?php _htmlsc($invoice->user_name); ?></h4>
                            <?php if ($invoice->user_vat_id) {
                                echo trans('vat_id_short') . ': ' . $invoice->user_vat_id . '<br>';
                            } ?>
                            <?php if ($invoice->user_tax_code) {
                                echo trans('tax_code_short') . ': ' . $invoice->user_tax_code . '<br>';
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
                        <td class="w-4 text-bold"><h3><?php _trans('bill_to'); ?></h3></td>
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
                                echo trans('vat_id_short') . ': ' . $invoice->client_vat_id . '<br>';
                            } ?>
                            <?php if ($invoice->client_tax_code) {
                                echo trans('tax_code_short') . ': ' . $invoice->client_tax_code . '<br>';
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
                                <td class="py-2 px-1 bt"><?php _htmlsc($item->item_name); ?></td>
                                <td class="py-2 px-1 bt"><?php echo nl2br(htmlsc($item->item_description)); ?></td>
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
                                    <?php echo format_currency($item->item_subtotal); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tbody class="invoice-sums">
                        <tr>
                            <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right px-1 py-2 bt">
                                <?php _trans('subtotal'); ?>
                            </td>
                            <td class="text-right py-2 px-1 bt"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
                        </tr>

                        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
                            <tr>
                                <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right py-2 px-1 bt">
                                    <?php _trans('item_tax'); ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
                            <tr>
                                <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right py-2 px-1 bt">
                                    <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' (' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%)'; ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        <?php if ($invoice->invoice_discount_percent != '0.00') : ?>
                            <tr>
                                <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right py-2 px-1 bt">
                                    <?php _trans('discount'); ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_amount($invoice->invoice_discount_percent); ?>%
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($invoice->invoice_discount_amount != '0.00') : ?>
                            <tr>
                                <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right py-2 px-1 bt">
                                    <?php _trans('discount'); ?>
                                </td>
                                <td class="text-right py-2 px-1 bt">
                                    <?php echo format_currency($invoice->invoice_discount_amount); ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right py-2 px-1 bt">
                                <b><?php _trans('total'); ?></b>
                            </td>
                            <td class="text-right py-2 px-1 bt">
                                <b><?php echo format_currency($invoice->invoice_total); ?></b>
                            </td>
                        </tr>
                        <tr>
                            <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right py-2 px-1 bt">
                                <?php _trans('paid'); ?>
                            </td>
                            <td class="text-right py-2 px-1 bt">
                                <?php echo format_currency($invoice->invoice_paid); ?>
                            </td>
                        </tr>
                        <tr>
                            <td <?php echo $show_item_discounts ? 'colspan="5"' : 'colspan="4"'; ?> class="text-right py-2 px-1 bt">
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
        <?php
        if ($invoice->client_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . htmlsc($invoice->client_vat_id) . '</div>';
        }
if ($invoice->client_tax_code) {
    echo '<div>' . trans('tax_code_short') . ': ' . htmlsc($invoice->client_tax_code) . '</div>';
}
if ($invoice->client_address_1) {
    echo '<div>' . htmlsc($invoice->client_address_1) . '</div>';
}
if ($invoice->client_address_2) {
    echo '<div>' . htmlsc($invoice->client_address_2) . '</div>';
}
if ($invoice->client_city || $invoice->client_state || $invoice->client_zip) {
    echo '<div>';
    if ($invoice->client_city) {
        echo htmlsc($invoice->client_city) . ' ';
    }
    if ($invoice->client_state) {
        echo htmlsc($invoice->client_state) . ' ';
    }
    if ($invoice->client_zip) {
        echo htmlsc($invoice->client_zip);
    }
    echo '</div>';
}
if ($invoice->client_country) {
    echo '<div>' . get_country_name(trans('cldr'), htmlsc($invoice->client_country)) . '</div>';
}

echo '<br>';

if ($invoice->client_phone) {
    echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($invoice->client_phone) . '</div>';
}
?>
    </div>
    <div id="company">
        <div><b><?php _htmlsc($invoice->user_name); ?></b></div>
        <?php
if ($invoice->user_vat_id) {
    echo '<div>' . trans('vat_id_short') . ': ' . htmlsc($invoice->user_vat_id) . '</div>';
}
if ($invoice->user_tax_code) {
    echo '<div>' . trans('tax_code_short') . ': ' . htmlsc($invoice->user_tax_code) . '</div>';
}
if ($invoice->user_address_1) {
    echo '<div>' . htmlsc($invoice->user_address_1) . '</div>';
}
if ($invoice->user_address_2) {
    echo '<div>' . htmlsc($invoice->user_address_2) . '</div>';
}
if ($invoice->user_city || $invoice->user_state || $invoice->user_zip) {
    echo '<div>';
    if ($invoice->user_city) {
        echo htmlsc($invoice->user_city) . ' ';
    }
    if ($invoice->user_state) {
        echo htmlsc($invoice->user_state) . ' ';
    }
    if ($invoice->user_zip) {
        echo htmlsc($invoice->user_zip);
    }
    echo '</div>';
}
if ($invoice->user_country) {
    echo '<div>' . get_country_name(trans('cldr'), htmlsc($invoice->user_country)) . '</div>';
}

echo '<br>';

if ($invoice->user_phone) {
    echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($invoice->user_phone) . '</div>';
}
if ($invoice->user_fax) {
    echo '<div>' . trans('fax_abbr') . ': ' . htmlsc($invoice->user_fax) . '</div>';
}
?>
    </div>

</header>

<?php echo $watermark ?>

<main>

    <div class="invoice-details clearfix">
        <table class="large">
            <tr>
                <td rowspan="<?php echo $payment_method ? 5 : 4 ?>" style="width:40%;text-align:left;"><?php echo $stamp ?></td>
            </tr>
            <tr>
                <td><?php _trans('invoice_date'); ?>:</td>
                <td><?php echo date_from_mysql($invoice->invoice_date_created, true); ?></td>
            </tr>
            <tr>
                <td<?php echo $text_class_date ?>><?php _trans('due_date'); ?>:</td>
                <td<?php echo $text_class_date ?>><?php echo date_from_mysql($invoice->invoice_date_due, true); ?></td>
            </tr>
            <tr>
                <td<?php echo $text_class_balance ?>><?php _trans('amount_due'); ?>:</td>
                <td<?php echo $text_class_balance ?>><?php echo format_currency($invoice->invoice_balance); ?></td>
            </tr>
            <?php
    if ($payment_method) {
        ?>
                <tr>
                    <td><?php _trans('payment_method'); ?>:</td>
                    <td><?php _htmlsc($payment_method->payment_method_name); ?></td>
                </tr>
                <?php
    }
?>
        </table>
    </div>

    <h1 class="invoice-title <?php echo $text_class ?>"><?php _trans('invoice') ?> <?php _htmlsc($invoice->invoice_number) ?></h1>

    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name"><?php _trans('item'); ?></th>
            <th class="item-desc"><?php _trans('description'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-price text-right"><?php _trans('price'); ?></th>
            <?php
if ($show_item_discounts) {
    ?>
                <th class="item-discount text-right"><?php _trans('discount'); ?></th>
                <?php
}
?>
            <th class="item-total text-right"><?php _trans('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) {
            ?>
            <tr>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                <td class="text-right">
                    <?php echo format_quantity($item->item_quantity); ?>
                    <?php if ($item->item_product_unit) { ?>
                        <br>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php } ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency(htmlsc($item->item_price)); ?>
                </td>
                <?php
                if ($show_item_discounts) {
                    ?>
                    <td class="text-right">
                        <?php echo format_currency(htmlsc($item->item_discount)); ?>
                    </td>
                    <?php
                }
            ?>
                <td class="text-right">
                    <?php echo format_currency(htmlsc($item->item_total)); ?>
                </td>
            </tr>
            <?php
        }
?>

        </tbody>

        <?php
// Fix for mpdf: table head of items printed on 2nd page
if ($add_table_and_head_for_sums) {
    $colspan .= '" style="width:543px'; // little hackish
    ?>
    </table>

    <table class="item-table">
        <thead>
        <tr>
            <th colspan="<?php echo $colspan ?>">&nbsp;</th>
            <th class="text-right">
                <?php _trans('total'); ?>
            </th>
        </tr>
        </thead>
        <?php
} // fi add_table_head_for_totals
?>

        <tbody class="invoice-sums">

        <?php
if ( ! $legacy_calculation) {
    discount_global_print_in_pdf($invoice, $show_item_discounts); // in helpers/pdf_helper
}
?>

        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <?php _trans('subtotal'); ?>
            </td>
            <td class="text-right"><?php echo format_currency(htmlsc($invoice->invoice_item_subtotal)); ?></td>
        </tr>

        <?php
if ($invoice->invoice_item_tax_total > 0) {
    ?>
            <tr>
                <td class="text-right" colspan="<?php echo $colspan ?>">
                    <?php _trans('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                </td>
            </tr>
            <?php
}
?>

        <?php
foreach ($invoice_tax_rates as $invoice_tax_rate) {
    ?>
            <tr>
                <td class="text-right" colspan="<?php echo $colspan ?>">
                    <?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' (' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                </td>
            </tr>
            <?php
}
?>

        <?php
if ($legacy_calculation) {
    discount_global_print_in_pdf($invoice, $show_item_discounts); // in helpers/pdf_helper
}
?>

        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency(htmlsc($invoice->invoice_total)); ?></b>
            </td>
        </tr>
        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <?php _trans('paid'); ?>
            </td>
            <td class="text-right">
                <?php echo format_currency(htmlsc($invoice->invoice_paid)); ?>
            </td>
        </tr>
        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <b><?php _trans('balance'); ?></b>
            </td>
            <td class="text-right <?php echo $text_class ?>">
                <b><?php echo format_currency($invoice->invoice_balance); ?></b>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    if ($show_qrcode) {
        ?>
        <table class="invoice-qr-code-table">
            <tr>
                <td>
                    <div>
                        <strong><?php _trans('qr_code_settings_recipient'); ?>:</strong>
                        <?php _htmlsc($invoice->user_company ?: get_setting('qr_code_recipient')); ?>
                    </div>
                    <div>
                        <strong><?php _trans('qr_code_settings_iban'); ?>:</strong>
                        <?php _htmlsc($invoice->user_iban ?: get_setting('qr_code_iban')); ?>
                    </div>
                    <div>
                        <strong><?php _trans('qr_code_settings_bic'); ?>:</strong>
                        <?php _htmlsc($invoice->user_bic ?: get_setting('qr_code_bic')); ?>
                    </div>
                    <div>
                        <strong><?php _trans('qr_code_settings_remittance_text'); ?>:</strong>
                        <?php _htmlsc(parse_template($invoice, $invoice->user_remittance_text ?: get_setting('qr_code_remittance_text'))); ?>
                    </div>
                </td>
                <td class="text-right">
                    <?php echo invoice_qrcode(htmlsc($invoice->invoice_id)); ?>
                </td>
            </tr>
        </table>
        <?php
    }
?>

</main>

<div class="invoice-terms">
    <?php
if ($invoice->invoice_terms) {
    ?>
        <div class="notes">
            <b><?php _trans('terms'); ?></b><br/>
            <?php echo nl2br(htmlsc($invoice->invoice_terms)); ?>
        </div>
        <?php
}
?>
</div>
<sethtmlpagefooter name="defaultFooter" value="on" />
<!-- To use the template with page numbering, uncomment the following line -->
<!-- <sethtmlpagefooter name="footerWithPageNumbers" value="on" /> -->
</body>
</html>
