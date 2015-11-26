<html lang="<?php echo lang('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo lang('quote'); ?></title>
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
            <b><?php echo $quote->client_name; ?></b>
        </div>
        <?php if ($quote->client_vat_id) {
            echo '<div>' . lang('vat_id_short') . ': ' . $quote->client_vat_id . '</div>';
        }
        if ($quote->client_tax_code) {
            echo '<div>' . lang('tax_code_short') . ': ' . $quote->client_tax_code . '</div>';
        }
        if ($quote->client_address_1) {
            echo '<div>' . $quote->client_address_1 . '</div>';
        }
        if ($quote->client_address_2) {
            echo '<div>' . $quote->client_address_2 . '</div>';
        }
        if ($quote->client_city && $quote->client_zip) {
            echo '<div>' . $quote->client_city . ' ' . $quote->client_zip . '</div>';
        } else {
            if ($quote->client_zip) {
                echo '<div>' . $quote->client_zip . '</div>';
            }
            if ($quote->client_zip) {
                echo '<div>' . $quote->client_zip . '</div>';
            }
        }
        if ($quote->client_state) {
            echo '<div>' . $quote->client_state . '</div>';
        }
        if ($quote->client_country) {
            echo '<div>' . get_country_name(lang('cldr'), $quote->client_country) . '</div>';
        }

        echo '<br/>';

        if ($quote->client_phone) {
            echo '<div>' . lang('phone_abbr') . ': ' . $quote->client_phone . '</div>';
        } ?>

    </div>
    <div id="company">
        <div><b><?php echo $quote->user_name; ?></b></div>
        <?php if ($quote->user_vat_id) {
            echo '<div>' . lang('vat_id_short') . ': ' . $quote->user_vat_id . '</div>';
        }
        if ($quote->user_tax_code) {
            echo '<div>' . lang('tax_code_short') . ': ' . $quote->user_tax_code . '</div>';
        }
        if ($quote->user_address_1) {
            echo '<div>' . $quote->user_address_1 . '</div>';
        }
        if ($quote->user_address_2) {
            echo '<div>' . $quote->user_address_2 . '</div>';
        }
        if ($quote->user_city && $quote->user_zip) {
            echo '<div>' . $quote->user_city . ' ' . $quote->user_zip . '</div>';
        } else {
            if ($quote->user_zip) {
                echo '<div>' . $quote->user_zip . '</div>';
            }
            if ($quote->user_zip) {
                echo '<div>' . $quote->user_zip . '</div>';
            }
        }
        if ($quote->user_state) {
            echo '<div>' . $quote->user_state . '</div>';
        }
        if ($quote->user_country) {
            echo '<div>' . get_country_name(lang('cldr'), $quote->user_country) . '</div>';
        }

        echo '<br/>';

        if ($quote->user_phone) {
            echo '<div>' . lang('phone_abbr') . ': ' . $quote->user_phone . '</div>';
        }
        if ($quote->user_fax) {
            echo '<div>' . lang('fax_abbr') . ': ' . $quote->user_fax . '</div>';
        }
        ?>
    </div>

</header>

<main>

    <div class="invoice-details clearfix">
        <table>
            <tr>
                <td><?php echo lang('quote_date') . ':'; ?></td>
                <td><?php echo date_from_mysql($quote->quote_date_created, true); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('expires') . ': '; ?></td>
                <td><?php echo date_from_mysql($quote->quote_date_expires, true); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('total') . ': '; ?></td>
                <td><?php echo format_currency($quote->quote_total); ?></td>
            </tr>
        </table>
    </div>

    <h1 class="invoice-title"><?php echo lang('quote') . ' ' . $quote->quote_number; ?></h1>

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
            <td class="text-right"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
        </tr>

        <?php if ($quote->quote_item_tax_total > 0) { ?>
            <tr>
                <td colspan="5" class="text-right">
                    <?php echo lang('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($quote_tax_rates as $quote_tax_rate) : ?>
            <tr>
                <td colspan="5" class="text-right">
                    <?php echo $quote_tax_rate->quote_tax_rate_name . ' (' . $quote_tax_rate->quote_tax_rate_percent . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>

        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo lang('total'); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($quote->quote_total); ?></b>
            </td>
        </tr>
        </tbody>
    </table>

</main>

<footer>
    <?php if ($quote->notes) : ?>
        <div class="notes">
            <b><?php echo lang('notes'); ?></b><br/>
            <?php echo nl2br($quote->notes); ?>
        </div>
    <?php endif; ?>
</footer>

</body>
</html>