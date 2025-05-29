<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('invoice'); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">

<style>

@page {
size: 21.0cm 29.7cm;
margin-top: 5.4cm;
margin-left: 2.3cm;
margin-bottom: 2.5cm;
margin-right: 1.25cm;
}

</style>

</head>
<body>
<header class="clearfix">

    <div id="client">
        <div> <?php _htmlsc($invoice->client_name); ?></div>
        <?php if ($invoice->client_surname) {
            echo '<div>' . htmlsc($invoice->client_surname) . '</div>';
        }
		if ($invoice->client_address_1) {
            echo '<div>' . htmlsc($invoice->client_address_1) . '</div>';
        }
        if ($invoice->client_address_2) {
            echo '<div>' . htmlsc($invoice->client_address_2) . '</div>';
        }
        if ($invoice->client_city || $invoice->client_state || $invoice->client_zip) {
            echo '<div>';
            if ($invoice->client_zip) {
                echo htmlsc($invoice->client_zip).' ';
            }
            if ($invoice->client_city) {
                echo htmlsc($invoice->client_city) . ' ';
            }
            echo '</div>';
        }
		?>
    </div>
    
</header>
<br><br><br><br>

<!--
// Example Custom Field
// Kunden-Nr.:  echo $custom_fields['client']['Kundennummer'] 
//  echo htmlsc($invoice->user_city)
-->
 

<?php //markus: some view helper calculations

	// calculate discount
	$discount = round(($invoice->invoice_item_subtotal) / 100 * ($invoice->invoice_discount_percent), 4);
	
	// calculate subtotal
	$invoice_subtotal_minus_discount = round(($invoice->invoice_item_subtotal) - $discount, 4);
	
	// calculate VAT after net discount from subtotal net
	// vwd = correct 'Vat With Discount'
	$vwd = (($invoice_subtotal_minus_discount) / 100 * 19);

	// calculate Paid net 
	$pn = round(($invoice->invoice_paid) / 1.19, 2);
	
	// calculate subtotal
	$invoice_subtotal_minus_dp = round(($invoice->invoice_item_subtotal) - $discount - $pn, 2);
	
	// calculate VAT and total with discount and paid
	$vdp = round((($invoice_subtotal_minus_discount) - ($pn)) / 100 * 19, 2);
	$total_dp = round(($invoice_subtotal_minus_discount) - ($pn) + ($vdp), 2);
	//echo $vwd;
	
?>


<main>
	<table class="topic-table" style="width: 100%;">
		<tr>
			<td><b>Rechnungsnummer: <?php echo $invoice->invoice_number; ?></b></td>
			<td style="text-align: right;"><b><?php echo 'Rechnungsdatum: ' . date_from_mysql($invoice->invoice_date_created, true); ?><b></td>
		</tr>
	</table>
	<br>

    <table class="item-table bt ">
        <thead>
			<tr>
				<th width="8%" class="item-name"><?php _trans('product_sku'); ?></th>
				<?php if ($show_item_discounts) { ?>
				<th width="47%" class="item-desc"><?php _trans('description'); ?></th>
				<th width="10%" class="item-amount text-right"><?php _trans('quantity'); ?></th>
				<th width="4%" class="item-unit text-right"></th>
				<th width="10%" class="item-price text-right"><?php _trans('price'); ?></th>
				<th width="10%" class="item-discount text-right"><?php _trans('item_discount'); ?></th>

				<?php } else { ?>

				<th width="57%" class="item-desc"><?php _trans('description'); ?></th>
				<th width="10%" class="item-amount text-right"><?php _trans('quantity'); ?></th>
				<th width="4%" class="item-unit text-right"></th>
				<th width="10%" class="item-price text-right"><?php _trans('price'); ?></th>
				<?php } ?>
				<th width="11%" class="item-total text-right"><?php _trans('total'); ?></th>
			</tr>
        </thead>
		<tbody>

        <?php $i=1;
        foreach ($items as $item) { ?>
            <tr class="bb" >
				<!-- td class="" style="vertical-align: top;"><b><?php echo str_pad(($i++), 2, "0", STR_PAD_LEFT);?></b></td -->
				<td class="" style="vertical-align: top;"><?php _trans($item->product_sku); ?></td>
                <td class=""><i><?php _htmlsc($item->item_name); ?></i><br />
                <!-- ?php _htmlsc($item->item_sku); ?><br -->
                <!-- ?php echo nl2br(htmlsc($item->item_description)); ? -->
				<?php 
					echo '<ul class="item-description">';
						listformat($item->item_description);
					echo '</ul>';
				?>
				</td>
                <td class="text-right text-bottom" >
					<?php echo format_amount($item->item_quantity); ?>
                </td>
				<td class=" text-bottom" >
                    <?php if ($item->item_product_unit) : ?>
                        &nbsp;&nbsp;<?php _htmlsc($item->item_product_unit); ?>
                    <?php endif; ?>
                </td>
                <td class="text-right text-bottom ">
                    <?php echo format_currency($item->item_price); ?>
                </td>
				<?php if ($show_item_discounts) : ?>
                    <td class="text-right text-bottom">
                        <?php echo format_currency($item->item_discount); ?>
                    </td>
                <?php endif; ?>
                <td class="text-right text-bottom " >
                    <?php echo format_currency($item->item_total); ?>
                </td>
            </tr>
        <?php } ?>
			<tr><td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> >&nbsp;</td></tr>
        </tbody>

		<tbody class="invoice-sums btd bbd">
			<tr><td class="spacer" colspan="7">&nbsp;</td></tr>
			<tr class="bb">
				<td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right"><i>Betrag netto</i></td>
				<td class="text-right"><i><?php echo format_currency($invoice->invoice_item_subtotal); ?></i></td>
			</tr>

		<?php // BEGIN discount
			if ($invoice->invoice_discount_percent != '0.00') { ?>
            <tr class="bb">
                <td <?php echo $show_item_discounts ? 'colspan="6"' : 'colspan="5"'; ?> class="text-right">
                    <i><?php _trans('discount_minus'); ?>
					<?php echo format_amount($invoice->invoice_discount_percent); ?>&nbsp;%</i>
                </td>
                <td class="text-right">
					<i><?php echo format_currency($discount); ?></i>
                    <!-- ?php echo format_amount(htmlsc($invoice->invoice_discount_percent)); ?> % -->
                </td>
            </tr>
			<tr class="bb">
                <td <?php echo $show_item_discounts ? 'colspan="6"' : 'colspan="5"'; ?> class="text-right">
                    <i><?php _trans('subtotal'); ?></i>
                </td>
                <td class="text-right">
					<i><?php echo format_currency($invoice_subtotal_minus_discount); ?></i>
                </td>
            </tr>
			
        <?php } ?>
		
		<?php if ($invoice->invoice_paid != '0.00') { ?>
			<tr class="bb">
                <td <?php echo $show_item_discounts ? 'colspan="6"' : 'colspan="5"'; ?> class="text-right">
                    <i>abzgl. Anzahlung<?php _trans(''); ?></i>
                </td>
                <td class="text-right">
					<i><?php echo format_currency($pn); ?></i>
                </td>
            </tr>
			<tr class="bb">
                <td <?php echo $show_item_discounts ? 'colspan="6"' : 'colspan="5"'; ?> class="text-right">
                    <i><?php _trans('subtotal'); ?></i>
                </td>
                <td class="text-right">
					<i><?php echo format_currency($invoice_subtotal_minus_dp); ?></i>
                </td>
            </tr>
			
        <?php } ?>
		
        <?php if ($invoice->invoice_discount_amount != '0.00') { ?>
            <tr class="bb">
                <td <?php echo $show_item_discounts ? 'colspan="6"' : 'colspan="5"'; ?> class="text-right">
                    <i><?php _trans('discount'); ?></i>
                </td>
                <td class="text-right">
                    <i><?php echo format_currency(htmlsc($invoice->invoice_discount_amount)); ?></i>
                </td>
            </tr>
        <?php } // END discount ?>

        <?php foreach ($invoice_tax_rates as $invoice_tax_rate) { ?>
            <tr class="bb">
                <td <?php echo $show_item_discounts ? 'colspan="6"' : 'colspan="5"'; ?> class="text-right">
                    <i><?php echo htmlsc($invoice_tax_rate->invoice_tax_rate_name) . ' ' . round(($invoice_tax_rate->invoice_tax_rate_percent), 0) . ' %'; ?></i>
                </td>
                <td class="text-right">
                    <!-- ?php echo format_currency(htmlsc($invoice_tax_rate->invoice_tax_rate_amount)); ? -->
					<i><?php if (($invoice->invoice_discount_percent != '0.00') && ($invoice->invoice_paid != '0.00')) {
						echo format_currency($vdp);
						} elseif ($invoice->invoice_discount_percent != '0.00') {
						echo format_currency($vwd);
						} else {
						echo format_currency(htmlsc($invoice_tax_rate->invoice_tax_rate_amount));
						}
					?></i>
                </td>
            </tr>
        <?php } ?>

			<tr>
				<td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right">
					<i><?php _trans('invoice_amount'); ?></i>
				</td>
				<td class="text-right">
					<i><?php echo format_currency($invoice->invoice_total); ?></i>
				</td>
			</tr>
			<tr><td class="spacer" colspan="7">&nbsp;</td></tr>
        </tbody>
    </table>
</main>

<footer>
    <div class="notes">
		<?php if ($payment_method): ?>
			<i><?php echo trans('payment_method') . ': '; ?><?php _htmlsc($payment_method->payment_method_name); ?></i><br />
		<?php endif; ?>
		
		<?php if ($payment_method) $paymethod = ($payment_method->payment_method_name); else $paymethod = false; 
			if (strpos($paymethod, 'Lastschrift') !== false) { ?>
				Der Rechnungsbetrag wird vereinbarungsgemäß von Ihrem Konto abgebucht. <br>
				<?php
					} else {
				?>
				Bitte überweisen Sie den Betrag ohne Abzug binnen 14 Tagen nach Erhalt der Rechnung auf untenstehendes Konto. <br>
			<?php	
			} 
		?>
		Das Leistungsdatum entspricht, wenn nicht anders angegeben, dem Rechnungsdatum.
		
		<?php if ($invoice->invoice_terms) : ?>
			<?php echo nl2br(htmlsc($invoice->invoice_terms)); ?>
		<?php endif; ?>
		<br /><br />
		Wir danken herzlich für das entgegengebrachte Vertrauen.
    </div>
</footer>

</body>
</html>
<?php 
	// exit; 
?>
