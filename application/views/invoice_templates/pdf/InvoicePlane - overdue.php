<html lang="<?php echo lang('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo lang('invoice'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom-pdf.css">
</head>
<body>
<header class="clearfix">

    <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>

    <div id="client">
        <div>
            <b><?php echo $invoice->client_name; ?></b>
        </div>
        <?php if ($invoice->client_vat_id) {
            echo '<div>' . lang('vat_id_short') . ': ' . $invoice->client_vat_id . '</div>';
        }
        if ($invoice->client_tax_code) {
            echo '<div>' . lang('tax_code_short') . ': ' . $invoice->client_tax_code . '</div>';
        }
        if ($invoice->client_address_1) {
            echo '<div>' . $invoice->client_address_1 . '</div>';
        }
        if ($invoice->client_address_2) {
            echo '<div>' . $invoice->client_address_2 . '</div>';
        }
        if ($invoice->client_city && $invoice->client_zip) {
            echo '<div>' . $invoice->client_city . ' ' . $invoice->client_zip . '</div>';
        } else {
            if ($invoice->client_zip) {
                echo '<div>' . $invoice->client_zip . '</div>';
            }
            if ($invoice->client_zip) {
                echo '<div>' . $invoice->client_zip . '</div>';
            }
        }
        if ($invoice->client_state) {
            echo '<div>' . $invoice->client_state . '</div>';
        }
        if ($invoice->client_country) {
            echo '<div>' . get_country_name(lang('cldr'), $invoice->client_country) . '</div>';
        }

        echo '<br/>';

        if ($invoice->client_phone) {
            echo '<div>' . lang('phone_abbr') . ': ' . $invoice->client_phone . '</div>';
        } ?>

    </div>
    <div id="company">
        <div><b><?php echo $invoice->user_name; ?></b></div>
        <?php if ($invoice->user_vat_id) {
            echo '<div>' . lang('vat_id_short') . ': ' . $invoice->user_vat_id . '</div>';
        }
        if ($invoice->user_tax_code) {
            echo '<div>' . lang('tax_code_short') . ': ' . $invoice->user_tax_code . '</div>';
        }
        if ($invoice->user_address_1) {
            echo '<div>' . $invoice->user_address_1 . '</div>';
        }
        if ($invoice->user_address_2) {
            echo '<div>' . $invoice->user_address_2 . '</div>';
        }
        if ($invoice->user_city && $invoice->user_zip) {
            echo '<div>' . $invoice->user_city . ' ' . $invoice->user_zip . '</div>';
        } else {
            if ($invoice->user_zip) {
                echo '<div>' . $invoice->user_zip . '</div>';
            }
            if ($invoice->user_zip) {
                echo '<div>' . $invoice->user_zip . '</div>';
            }
        }
        if ($invoice->user_state) {
            echo '<div>' . $invoice->user_state . '</div>';
        }
        if ($invoice->user_country) {
            echo '<div>' . get_country_name(lang('cldr'), $invoice->user_country) . '</div>';
        }

        echo '<br/>';

        if ($invoice->user_phone) {
            echo '<div>' . lang('phone_abbr') . ': ' . $invoice->user_phone . '</div>';
        }
        if ($invoice->user_fax) {
            echo '<div>' . lang('fax_abbr') . ': ' . $invoice->user_fax . '</div>';
        }
        ?>
    </div>

</header>

<main>

    <div class="invoice-details clearfix">
        <table>
            <tr>
                <td><?php echo lang('invoice_date') . ':'; ?></td>
                <td><?php echo date_from_mysql($invoice->invoice_date_created, true); ?></td>
            </tr>
            <tr>
                <td class="text-red"><?php echo lang('due_date') . ': '; ?></td>
                <td class="text-red"><?php echo date_from_mysql($invoice->invoice_date_due, true); ?></td>
            </tr>
            <tr>
                <td class="text-red"><?php echo lang('amount_due') . ': '; ?></td>
                <td class="text-red"><?php echo format_currency($invoice->invoice_balance); ?></td>
            </tr>
            <?php if ($payment_method): ?>
                <tr>
                    <td><?php echo lang('payment_method') . ': '; ?></td>
                    <td><?php echo $payment_method->payment_method_name; ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <h1 class="invoice-title text-red"><?php echo lang('invoice') . ' ' . $invoice->invoice_number; ?></h1>

    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name"><?php echo lang('item'); ?></th>
            <th class="item-desc"><?php echo lang('description'); ?></th>
            <th class="item-amount"><?php echo lang('qty'); ?></th>
            <th class="item-price"><?php echo lang('price'); ?></th>
            <th class="item-discount"><?php echo lang('discount'); ?></th>
            <th class="item-total"><?php echo lang('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) { ?>
            <tr>
                <td><?php echo $item->item_name; ?></td>
                <td><?php echo nl2br($item->item_description); ?></td>
                <td class="text-right">
                    <?php echo format_amount($item->item_quantity); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_price); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_discount); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_subtotal); ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tbody class="invoice-sums">

        <tr>
            <td colspan="5" class="text-right"><?php echo lang('subtotal'); ?></td>
            <td class="text-right"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
        </tr>

        <?php if ($invoice->invoice_item_tax_total > 0) { ?>
            <tr>
                <td colspan="5" class="text-right">
                    <?php echo lang('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
            <tr>
                <td colspan="5" class="text-right">
                    <?php echo $invoice_tax_rate->invoice_tax_rate_name . ' (' . $invoice_tax_rate->invoice_tax_rate_percent . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>

        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo lang('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_total); ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="text-right">
                <?php echo lang('paid'); ?>
            </td>
            <td class="text-right">
                <?php echo format_currency($invoice->invoice_paid); ?>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo lang('balance'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_balance); ?></b>
            </td>
        </tr>
        </tbody>
    </table>

</main>

<footer>
    <?php if ($invoice->invoice_terms) : ?>
        <div class="notes">
            <b><?php echo lang('terms'); ?></b><br/>
            <?php echo nl2br($invoice->invoice_terms); ?>
        </div>
    <?php endif; ?>
</footer>

</body>
</html>