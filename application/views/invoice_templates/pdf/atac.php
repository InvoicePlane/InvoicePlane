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
        <!-- div>An</div -->
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
				<th width="5%" class="item-name">Art.</th>
				<?php if ($show_item_discounts) { ?>
				<th width="53%" class="item-desc">Beschreibung</th>
				<th width="10%" class="item-amount text-right">Anzahl</th>
				<th width="2%" class="item-unit text-right"></th>
				<th width="10%" class="item-price text-right">Preis</th>
				<th width="10%" class="item-discount text-right">Rabatt</th>

				<?php } else { ?>

				<th width="63%" class="item-desc">Beschreibung</th>
				<th width="10%" class="item-amount text-right">Anzahl</th>
				<th width="2%" class="item-unit text-right"></th>
				<th width="10%" class="item-price text-right">Preis</th>
				<?php } ?>
				<th width="10%" class="item-total text-right">Summe</th>
			</tr>
        </thead>
		<tbody>

        <?php $i=1;
        foreach ($items as $item) { ?>
            <tr class="bb" >
				<!-- td class="" style="vertical-align: top;"><b><?php echo str_pad(($i++), 2, "0", STR_PAD_LEFT);?></b></td -->
				<td class="" style="vertical-align: top;"><?php _trans($item->product_sku); ?></td>
                <td class=""><b><?php _htmlsc($item->item_name); ?></b><br />
                <!-- ?php _htmlsc($item->item_sku); ?><br -->
				
                <?php echo nl2br(htmlsc($item->item_description)); ?></td>
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
                <td class="text-right text-bottom " >
                    <?php echo format_currency($item->item_subtotal); ?>
                </td>
            </tr>
        <?php } ?>
			<tr><td colspan="6">&nbsp;</td></tr>
        </tbody>

		<tbody class="invoice-sums btd bbd">
			<tr><td class="spacer" colspan="6">&nbsp;</td></tr>
			<tr class="bb">
				<td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right"><b>Betrag netto</b></td>
				<td class="text-right"><b><?php echo format_currency($invoice->invoice_item_subtotal); ?></b></td>
			</tr>


<?php if ($invoice->invoice_item_tax_total > 0) { 
// das ist die alte verison. hier habe ich mit item tax gearbeitet, weil das der default im template war und ich das ander enicht anzeiten konnte
// aus kompat bis rechnung 120 muss drin bleiben. stoert auch nicht. jetzt mache ich invoice tax und nicht mehr item tax
?>
            <tr class="bb">
                <td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right"><b>zzgl. 19% MWst.</b></td>
                <td class="text-right">
                    <b><?php echo format_currency($invoice->invoice_item_tax_total); ?></b>
                </td>
            </tr>
        <?php } ?>

<?php if (!empty($invoice_tax_rates)) {
// new style jetzt mit invoice tax haette man gleich so machen sollen. evtl mal in der datenbank alles nachtr. umstellen
$tn = $invoice_tax_rates[0]->invoice_tax_rate_name;
$tr = $invoice_tax_rates[0]->invoice_tax_rate_percent;
$ta = $invoice_tax_rates[0]->invoice_tax_rate_amount;
?>
            <tr class="bb">
                <td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right">
                        <!-- b><?php echo $tn; ?> <?php echo $tr; ?>&nbsp;%</b -->
						<b>zzgl.&nbsp;<?php echo floor($tr); ?>%&nbsp;USt.</b>
                </td>
                <td class="text-right">
                    <b><?php echo format_currency($ta); ?></b>
                </td>
            </tr>
<?php
	}
?>

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right">
                <b>Betrag brutto</b>
            </td>
            <td class="text-right">
                <b><?php echo format_currency($invoice->invoice_total); ?></b>
            </td>
        </tr>
		<tr><td class="spacer" colspan="6">&nbsp;</td></tr>
        </tbody>
    </table>
</main>

<footer>
    <div class="notes">
		<?php if ($payment_method): ?>
			<b><?php echo trans('payment_method') . ': '; ?><?php _htmlsc($payment_method->payment_method_name); ?></b><br />
		<?php endif; ?>
		
		<?php if ($payment_method) $paymethod = ($payment_method->payment_method_name); else $paymethod = false; 
			if (strpos($paymethod, 'Lastschrift') !== false) { ?>
				Der Betrag wird vereinbarungsgemäß von Ihrem Konto abgebucht. <br>
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
