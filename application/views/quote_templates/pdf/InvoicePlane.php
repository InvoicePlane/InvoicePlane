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
                        <?php if ($quote->quote_discount_percent > 0 || $quote->quote_discount_amount > 0) : ?>
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
                        <?php endif; ?>
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
        <?php if ($quote->notes) { ?>
            <table class="w-10 table item-table bt">
                <thead>
                    <tr>
                        <th class="py-2 px-2"><?php echo trans('notes'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2 px-2 bt">
                            <?php echo nl2br(strip_tags($quote->notes, '<p><br><a><strong><em><ul><li><ol><h1><h2><h3><h4><h5><h6>')); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>
    </body>
<?php
// Fix item table head when numerous (>= 12) items (overflowing in 2nd page)
$add_table_and_head_for_sums = 1; // Set to 0/false/null/'', return to original IP
// edit if you know what you're doing
$colspan = $show_item_discounts ? 5 : 4;
?><!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo get_setting('custom_title', 'InvoicePlane', true); ?> - <?php _trans('quote'); ?></title>
    <link rel="stylesheet" href="<?php _theme_asset('css/templates.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php _core_asset('css/custom-pdf.css'); ?>" type="text/css">
</head>
<body>
<header class="clearfix">

    <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>

    <div id="client">
        <div>
            <b><?php _htmlsc(format_client($quote)); ?></b>
        </div>
<?php
if ($quote->client_vat_id) {
    echo '<div>' . trans('vat_id_short') . ': ' . htmlsc($quote->client_vat_id) . '</div>';
}
if ($quote->client_tax_code) {
    echo '<div>' . trans('tax_code_short') . ': ' . htmlsc($quote->client_tax_code) . '</div>';
}
if ($quote->client_address_1) {
    echo '<div>' . htmlsc($quote->client_address_1) . '</div>';
}
if ($quote->client_address_2) {
    echo '<div>' . htmlsc($quote->client_address_2) . '</div>';
}
if ($quote->client_city || $quote->client_state || $quote->client_zip) {
    echo '<div>';
    if ($quote->client_city) {
        echo htmlsc($quote->client_city) . ' ';
    }
    if ($quote->client_state) {
        echo htmlsc($quote->client_state) . ' ';
    }
    if ($quote->client_zip) {
        echo htmlsc($quote->client_zip);
    }
    echo '</div>';
}
if ($quote->client_country) {
    echo '<div>' . get_country_name(trans('cldr'), htmlsc($quote->client_country)) . '</div>';
}

echo '<br>';

if ($quote->client_phone) {
    echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($quote->client_phone) . '</div>';
}
?>

    </div>
    <div id="company">
        <div><b><?php _htmlsc($quote->user_name); ?></b></div>
<?php
if ($quote->user_vat_id) {
    echo '<div>' . trans('vat_id_short') . ': ' . htmlsc($quote->user_vat_id) . '</div>';
}
if ($quote->user_tax_code) {
    echo '<div>' . trans('tax_code_short') . ': ' . htmlsc($quote->user_tax_code) . '</div>';
}
if ($quote->user_address_1) {
    echo '<div>' . htmlsc($quote->user_address_1) . '</div>';
}
if ($quote->user_address_2) {
    echo '<div>' . htmlsc($quote->user_address_2) . '</div>';
}
if ($quote->user_city || $quote->user_state || $quote->user_zip) {
    echo '<div>';
    if ($quote->user_city) {
        echo htmlsc($quote->user_city) . ' ';
    }
    if ($quote->user_state) {
        echo htmlsc($quote->user_state) . ' ';
    }
    if ($quote->user_zip) {
        echo htmlsc($quote->user_zip);
    }
    echo '</div>';
}
if ($quote->user_country) {
    echo '<div>' . get_country_name(trans('cldr'), htmlsc($quote->user_country)) . '</div>';
}

echo '<br/>';

if ($quote->user_phone) {
    echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($quote->user_phone) . '</div>';
}
if ($quote->user_fax) {
    echo '<div>' . trans('fax_abbr') . ': ' . htmlsc($quote->user_fax) . '</div>';
}
?>
    </div>

</header>

<main>

    <div class="invoice-details clearfix">
        <table>
            <tr>
                <td><?php _trans('quote_date'); ?>:</td>
                <td><?php echo date_from_mysql($quote->quote_date_created, true); ?></td>
            </tr>
            <tr>
                <td><?php _trans('expires'); ?>:</td>
                <td><?php echo date_from_mysql($quote->quote_date_expires, true); ?></td>
            </tr>
            <tr>
                <td><?php _trans('total'); ?>:</td>
                <td><?php echo format_currency($quote->quote_total); ?></td>
            </tr>
        </table>
    </div>

    <h1 class="invoice-title"><?php _trans('quote'); ?> <?php _htmlsc($quote->quote_number); ?></h1>

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
<?php
    if ($item->item_product_unit) {
?>
                    <br>
                    <small><?php _htmlsc($item->item_product_unit); ?></small>
<?php
    }
?>
                </td>
                <td class="text-right">
                    <?php echo format_currency(htmlsc($item->item_price)); ?>
                </td>
<?php
    if ($show_item_discounts) {
?>
                <td class="text-right">
                    <?php echo format_currency($item->item_discount); ?>
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
    discount_global_print_in_pdf($quote, $show_item_discounts, 'quote'); // in helpers/pdf_helper
}
?>

        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <?php _trans('subtotal'); ?>
            </td>
            <td class="text-right"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
        </tr>

<?php
if ($quote->quote_item_tax_total > 0) {
?>
        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <?php _trans('item_tax'); ?>
            </td>
            <td class="text-right">
                <?php echo format_currency($quote->quote_item_tax_total); ?>
            </td>
        </tr>
<?php
}
?>

<?php
foreach ($quote_tax_rates as $quote_tax_rate) {
?>
        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <?php echo $quote_tax_rate->quote_tax_rate_name . ' (' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '%)'; ?>
            </td>
            <td class="text-right">
                <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
            </td>
        </tr>
<?php
}
?>

<?php
if ($legacy_calculation) {
    discount_global_print_in_pdf($quote, $show_item_discounts, 'quote'); // in helpers/pdf_helper
}
?>

        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <b><?php _trans('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency(htmlsc($quote->quote_total)); ?></b>
            </td>
        </tr>
        </tbody>
    </table>
</main>

<div class="invoice-terms">
<?php
if ($quote->notes) {
?>
    <div class="notes">
        <b><?php _trans('notes'); ?></b><br/>
        <?php echo nl2br(htmlsc($quote->notes)); ?>
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
