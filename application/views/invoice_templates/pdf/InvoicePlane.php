<?php
// Fix item table head when numerous (>= 12) items (overflowing in 2nd page)
$add_table_and_head_for_sums = 1; // Set to 0/false/null/'', return to original IP

// Idea*: if einvoice == no and show pdf_watermark == yes: do not show stamp!
// Like $only_one = ! $invoice->client_einvoicing_active** && get_setting('pdf_watermark');
// ** And/Or with $invoice->client_einvoicing_version (ubl not embed xml in pdf)
// Note: watermarktext not applied if embed xml (Zugferd/Factur-x) pdf A (see mpdf_helper)

// Init some vars (edit if you know what you're doing)
$colspan            = $show_item_discounts ? 5 : 4;
$text_class         = '';
$text_class_date    = '';
$text_class_balance = '';
$watermark          = '';
$stamp              = '';
$show_qrcode        = $invoice->invoice_balance > 0 && $invoice->invoice_balance < 10e9 && get_setting('qr_code');
$invoice_mode ??= 'default'; // from template - overdue / paid.php

switch ($invoice_mode) {
    case 'overdue':
        $text_class         = 'text-red';
        $text_class_date    = ' class="' . $text_class . '"';
        $text_class_balance = ' class="' . $text_class . '"';
        $watermark          = '<watermarktext content="' . trans('overdue') . '" alpha="0.2" />';
        $stamp              = '<span class="stamp overdue">' . trans('overdue') . '</span>'; // * if watermark ok == no stamp (todo?)
        break;
    case 'paid':
        $show_qrcode = false;
        $text_class  = 'text-green';
        // $text_class_date    = ' class="' . $text_class . '"';
        $text_class_balance = ' class="' . $text_class . '"';
        $watermark          = '<watermarktext content="' . trans('paid') . '" alpha="0.2" />';
        $stamp              = '<span class="stamp paid">' . trans('paid') . '</span>'; // * if watermark ok == no stamp (todo?)
        break;
    default:
}

?><!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo get_setting('custom_title', 'InvoicePlane', true); ?> - <?php _trans('invoice'); ?></title>
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
            <b><?php _htmlsc(format_client($invoice)); ?></b>
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
                    <?php echo format_currency($item->item_price); ?>
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
                    <?php echo format_currency($item->item_total); ?>
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
            <td class="text-right"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
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
                <b><?php echo format_currency($invoice->invoice_total); ?></b>
            </td>
        </tr>
        <tr>
            <td class="text-right" colspan="<?php echo $colspan ?>">
                <?php _trans('paid'); ?>
            </td>
            <td class="text-right">
                <?php echo format_currency($invoice->invoice_paid); ?>
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
                    <?php echo $invoice->user_company ?: get_setting('qr_code_recipient'); ?>
                </div>
                <div>
                    <strong><?php _trans('qr_code_settings_iban'); ?>:</strong>
                    <?php echo $invoice->user_iban ?: get_setting('qr_code_iban'); ?>
                </div>
                <div>
                    <strong><?php _trans('qr_code_settings_bic'); ?>:</strong>
                    <?php echo $invoice->user_bic ?: get_setting('qr_code_bic'); ?>
                </div>
                <div>
                    <strong><?php _trans('qr_code_settings_remittance_text'); ?>:</strong>
                    <?php echo parse_template($invoice, $invoice->user_remittance_text ?: get_setting('qr_code_remittance_text')); ?>
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

<htmlpagefooter name="footer">
    <footer>
        <?php _trans('invoice'); ?> <?php echo $invoice->invoice_number; ?> - <?php _trans('page'); ?> {PAGENO} / {nbpg}
    </footer>
</htmlpagefooter>

</body>
</html>
