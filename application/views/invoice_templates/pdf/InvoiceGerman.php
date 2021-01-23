<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('invoice'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/custom-pdf.css">
</head>
<body>
<header class="clearfix">
<?php
	if ($show_item_discounts) {
		$colspan=6;
	} else {
		$colspan=5;
	}
?>

    <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>

    <div id="client">
        <div>
            <b><?php _htmlsc(format_client($invoice)); ?></b>
        </div>
        <?php
			if ($invoice->client_address_1) {
				echo '<div>' . htmlsc($invoice->client_address_1) . '</div>';
			}
			if ($invoice->client_address_2) {
				echo '<div>' . htmlsc($invoice->client_address_2) . '</div>';
			}
			if ($invoice->client_city || $invoice->client_state || $invoice->client_zip) {
				echo '<div>';
				if ($invoice->client_zip) {
					echo htmlsc($invoice->client_zip) . ' ';
				}
				if ($invoice->client_city) {
					echo htmlsc($invoice->client_city) . ' ';
				}
				echo '</div>';
			}
			if ($invoice->client_country) {
				echo '<div>' . get_country_name(trans('cldr'), $invoice->client_country) . '</div>';
			}

			echo '<br/>';
		?>

    </div>
    <div id="company">
        <div>
			<b>
			<?php
				if ($custom_fields['client']['Unternehmenszuordnung'] == 'IT Systemhaus Poper') {
					_htmlsc($invoice->user_company);
				}
				if ($custom_fields['client']['Unternehmenszuordnung'] == 'Ingenieurbüro Poper') {
					_htmlsc($custom_fields['user']['2. Firma Name']);
				}
			?>
			</b>
		</div>
        <div>
			<?php _htmlsc($invoice->user_name); ?>
		</div>
        <?php if ($invoice->user_address_1) {
            echo '<div>' . htmlsc($invoice->user_address_1) . '</div>';
        }
        if ($invoice->user_address_2) {
            echo '<div>' . htmlsc($invoice->user_address_2) . '</div>';
        }
        if ($invoice->user_city || $invoice->user_state || $invoice->user_zip) {
            echo '<div>';
            if ($invoice->user_zip) {
                echo htmlsc($invoice->user_zip) . ' ';
            }
            if ($invoice->user_city) {
                echo htmlsc($invoice->user_city);
            }
            echo '</div>';
        }
        if ($invoice->user_country) {
            echo '<div>' . get_country_name(trans('cldr'), $invoice->user_country) . '</div>';
        }
        if ($invoice->user_phone) {
            echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($invoice->user_phone) . '</div>';
        }
        if ($invoice->user_fax) {
            echo '<div>' . trans('fax_abbr') . ': ' . htmlsc($invoice->user_fax) . '</div>';
        }
        if ($invoice->user_mobile) {
            echo '<div>' . trans('mobile') . ': ' . htmlsc($invoice->user_mobile) . '</div>';
        }
        ?>
    </div>
</header>

<main>

    <div class="invoice-details clearfix">
        <table>
          <?php if ($invoice->user_vat_id): ?>
            <tr>
              <td><?php echo trans('vat_id_user') . ':'; ?></td>
              <td><?php _htmlsc($invoice->user_vat_id); ?></td>
            </tr>
          <?php endif; ?>
          <?php if ($invoice->client_vat_id): ?>
            <tr>
              <td><?php echo trans('vat_id_client') . ':'; ?></td>
              <td><?php _htmlsc($invoice->client_vat_id); ?></td>
            </tr>
          <?php endif; ?>
          <tr>
            <td><?php echo trans('invoice_date') . ':'; ?></td>
            <td><?php echo date_from_mysql($invoice->invoice_date_created, true); ?></td>
          </tr>
          <tr>
            <td><?php echo trans('due_date') . ': '; ?></td>
            <td><?php echo date_from_mysql($invoice->invoice_date_due, true); ?></td>
          </tr>
          <?php if ($payment_method): ?>
              <tr>
                <td><?php echo trans('payment_method') . ': '; ?></td>
                <td><?php _htmlsc($payment_method->payment_method_name); ?></td>
              </tr>
          <?php endif; ?>
          <?php if ($custom_fields['client']['Kundennummer']): ?>
            <tr>
              <td><?php echo trans('cus_id') . ': '; ?></td>
              <td><?php echo $custom_fields['client']['Kundennummer']; ?></td>
            </tr>
          <?php endif; ?>
          <?php if ($custom_fields['client']['BAN']): ?>
            <tr>
              <td><?php echo trans('ban') . ': '; ?></td>
              <td><?php echo $custom_fields['client']['BAN']; ?></td>
            </tr>
          <?php endif; ?>
        </table>
    </div>

    <h1 class="invoice-title"><?php echo trans('invoice') . ' ' . $invoice->invoice_number; ?></h1>

    <table class="item-table">
        <thead>
			<tr>
				<th class="item-name"><?php _trans('item'); ?></th>
				<th class="item-desc"><?php _trans('description'); ?></th>
				<th class="item-amount"><?php _trans('qty'); ?></th>
				<th class="item-price"><?php _trans('price'); ?></th>
				<?php if ($show_item_discounts) : ?>
					<th class="item-discount"><?php _trans('discount'); ?></th>
				<?php endif; ?>
				<th class="item-tax_rate"><?php _trans('item_tax_rate'); ?></th>
				<th class="item-total"><?php _trans('item_subtotal'); ?></th>
			</tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) { ?>
            <tr>
                <td class="item-name"><?php _htmlsc($item->item_name); ?></td>
                <td class="item-desc"><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                <td class="item-amount">
                    <?php echo format_amount($item->item_quantity); ?>
                    <?php if (isset($item->item_product_unit)) : ?>
                        <br>
                        <?php _htmlsc($item->item_product_unit); ?>
                    <?php endif; ?>
                </td>
                <td class="item-price"><?php echo format_currency($item->item_price); ?></td>
                <?php if ($show_item_discounts) : ?>
                    <td class="item-discount">
                        <?php echo format_currency($item->item_discount); ?>
                    </td>
                <?php endif; ?>
                <td class="item-tax_rate"><?php echo format_amount($item->item_tax_rate_percent); ?>%</td>
                <td class="item-total"><?php echo format_currency($item->item_subtotal); ?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tbody class="invoice-sums">
        <tr>
            <td class="text-right" colspan=" <?php echo $colspan; ?> ">
                <?php _trans('subtotal'); ?>
            </td>
            <td class="text-right"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
        </tr>
        <?php if (isset($invoice_tax_rates)) { ?>
			<?php foreach ($invoice_tax_rates as $invoice_tax_rate) : ?>
				<tr>
					<td class="text-right" colspan=" <?php echo $colspan; ?> ">
						<?php echo 'a:'. htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' (' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%)'; ?>
					</td>
					<td class="text-right">
						<?php echo 't:'.format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php } ?>
        <?php if (isset($invoice->invoice_item_tax_total)) { ?>
            <tr>
                <td class="text-right" colspan=" <?php echo $colspan; ?> ">
                    <?php _trans('total_tax'); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                </td>
            </tr>
        <?php } ?>
        <?php if (isset($invoice->invoice_discount_percent)) : ?>
			<?php if ($invoice->invoice_discount_percent > 0) : ?>
				<tr>
					<td class="text-right" colspan=" <?php echo $colspan; ?> ">
						<?php _trans('discount'); ?>
					</td>
					<td class="text-right">
						<?php echo format_amount($invoice->invoice_discount_percent); ?>%
					</td>
				</tr>
			<?php endif; ?>
        <?php endif; ?>
        <?php if (isset($invoice->invoice_discount_amount)) : ?>
			<?php if ($invoice->invoice_discount_amount > 0) : ?>
				<tr>
					<td class="text-right" colspan=" <?php echo $colspan; ?> ">
						<?php _trans('discount'); ?>
					</td>
					<td class="text-right">
						<?php echo format_currency($invoice->invoice_discount_amount); ?>
					</td>
				</tr>
			<?php endif; ?>
        <?php endif; ?>

        <tr>
			<?php if (!isset($invoice->invoice_paid) or ($invoice->invoice_paid == 0)) { ?>
				<td class="invoice-sum-bold" colspan=" <?php echo $colspan; ?> ">
					<?php _trans('total'); ?>
				</td>
				<td class="invoice-sum-bold">
					<?php echo format_currency($invoice->invoice_total); ?>
				</td>
			<?php } else { ?>
				<td class="invoice-sum-nobold" colspan=" <?php echo $colspan; ?> ">
					<?php _trans('total'); ?>
				</td>
				<td class="invoice-sum-nobold">
					<?php echo format_currency($invoice->invoice_total); ?>
				</td>
			<?php } ?>
        </tr>
		<?php if (isset($invoice->invoice_paid)) : ?>
			<?php if ($invoice->invoice_paid > 0) : ?>
				<tr>
					<td class="invoice-sum-nobold" colspan=" <?php echo $colspan; ?> ">
						<?php _trans('paid'); ?>
					</td>
					<td class="invoice-sum-nobold">
						<?php echo format_currency($invoice->invoice_paid); ?>
					</td>
				</tr>
				<tr>
					<td class="invoice-sum-bold" colspan=" <?php echo $colspan; ?> ">
						<?php _trans('balance'); ?>
					</td>
					<td class="invoice-sum-bold">
						<?php echo format_currency($invoice->invoice_balance); ?>
					</td>
				</tr>
			<?php endif; ?>
        <?php endif; ?>
        </tbody>
    </table>

</main>

<footer>
	<?php
		if ($invoice->invoice_terms): ?>
			<div class="invoice-terms">
				<b>
				<?php _trans('terms'); ?>
				</b><br/>
				<?php echo nl2br(htmlsc($invoice->invoice_terms)); ?>
			</div>
		<?php endif; ?>
	<?php
		if ($custom_fields['client']['Rechnungsbedingungen']): ?>
			<div class="invoice-terms">
				<b>
				<?php _trans('terms_client'); ?>
				</b><br/>
				<?php echo nl2br(htmlsc($custom_fields['client']['Rechnungsbedingungen'])); ?>
			</div>
		<?php endif; ?>
	<?php
		if ($custom_fields['client']['Bankverbindung']):
	?>
        <div class="invoice-bankdetails">
            <b><br/>
			<?php
				_trans('bank_details');
			?></b><br/>
            <?php echo nl2br(htmlsc($custom_fields['client']['Bankverbindung'])); ?>
        </div>
    <?php endif; ?>
</footer>

</body>
</html>
