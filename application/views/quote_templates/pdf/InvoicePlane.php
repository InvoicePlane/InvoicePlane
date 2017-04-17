<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('quote'); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
</head>
<body>
<header class="clearfix">

    <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>

    <div id="client">
        <div>
            <b><?php _htmlsc($quote->client_name); ?></b>
        </div>
        <?php if ($quote->client_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . $quote->client_vat_id . '</div>';
        }
        if ($quote->client_tax_code) {
            echo '<div>' . trans('tax_code_short') . ': ' . $quote->client_tax_code . '</div>';
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
        if ($quote->client_state) {
            echo '<div>' . htmlsc($quote->client_state) . '</div>';
        }
        if ($quote->client_country) {
            echo '<div>' . get_country_name(trans('cldr'), $quote->client_country) . '</div>';
        }

        echo '<br/>';

        if ($quote->client_phone) {
            echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($quote->client_phone) . '</div>';
        } ?>

    </div>
    <div id="company">
        <div><b><?php _htmlsc($quote->user_name); ?></b></div>
        <?php if ($quote->user_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . $quote->user_vat_id . '</div>';
        }
        if ($quote->user_tax_code) {
            echo '<div>' . trans('tax_code_short') . ': ' . $quote->user_tax_code . '</div>';
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
            echo '<div>' . get_country_name(trans('cldr'), $quote->user_country) . '</div>';
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
                <td><?php echo trans('quote_date') . ':'; ?></td>
                <td><?php echo date_from_mysql($quote->quote_date_created, true); ?></td>
            </tr>
            <tr>
                <td><?php echo trans('expires') . ': '; ?></td>
                <td><?php echo date_from_mysql($quote->quote_date_expires, true); ?></td>
            </tr>
            <tr>
                <td><?php echo trans('total') . ': '; ?></td>
                <td><?php echo format_currency($quote->quote_total); ?></td>
            </tr>
        </table>
    </div>

    <h1 class="invoice-title"><?php echo trans('quote') . ' ' . $quote->quote_number; ?></h1>

    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name"><?php _trans('item'); ?></th>
            <th class="item-desc"><?php _trans('description'); ?></th>
            <th class="item-amount text-right"><?php _trans('qty'); ?></th>
            <th class="item-price text-right"><?php _trans('price'); ?></th>
            <?php if ($show_item_discounts) : ?>
                <th class="item-discount text-right"><?php _trans('discount'); ?></th>
            <?php endif; ?>
            <th class="item-total text-right"><?php _trans('total'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) { ?>
            <tr>
                <td><?php _htmlsc($item->item_name); ?></td>
                <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                <td class="text-right">
                    <?php echo format_amount($item->item_quantity); ?>
                    <?php if ($item->item_product_unit) : ?>
                        <br>
                        <small><?php _htmlsc($item->item_product_unit); ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($item->item_price); ?>
                </td>
                <?php if ($show_item_discounts) : ?>
                    <td class="text-right">
                        <?php echo format_currency($item->item_discount); ?>
                    </td>
                <?php endif; ?>
                <td class="text-right">
                    <?php echo format_currency($item->item_total); ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tbody class="invoice-sums">

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?>
                    class="text-right"><?php _trans('subtotal'); ?></td>
            <td class="text-right"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
        </tr>

        <?php if ($quote->quote_item_tax_total > 0) { ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('item_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>

        <?php foreach ($quote_tax_rates as $quote_tax_rate) : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php echo $quote_tax_rate->quote_tax_rate_name . ' (' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                </td>
            </tr>
        <?php endforeach ?>

        <?php if ($quote->quote_discount_percent != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_amount($quote->quote_discount_percent); ?>%
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($quote->quote_discount_amount != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                    <?php _trans('discount'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($quote->quote_discount_amount); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="5"' : 'colspan="4"'); ?> class="text-right">
                <b><?php _trans('total'); ?></b>
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
            <b><?php _trans('notes'); ?></b><br/>
            <?php echo nl2br(htmlsc($quote->notes)); ?>
        </div>
    <?php endif; ?>
</footer>

</body>
</html>
