<script type="text/javascript">

    $(function() {

        $('#btn_generate_pdf').click(function() {
            window.location = '<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>';
        });

    });

</script>

<div class="headerbar">
	<h1><?php echo lang('invoice'); ?> #<?php echo $invoice->invoice_number; ?></h1>

	<div class="pull-right">
        <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>" class="btn" id="btn_generate_pdf" data-invoice-id="<?php echo $invoice_id; ?>" data-invoice-balance="<?php echo $invoice->invoice_balance; ?>"><i class="icon-print"></i> <?php echo lang('download_pdf'); ?></a>
	</div>

</div>

<?php echo $this->layout->load_view('layout/alerts'); ?>

<div class="content">
	
	<form id="invoice_form" class="form-horizontal">

		<div class="invoice">

			<div class="cf">

				<div class="pull-left">

					<h2><?php echo $invoice->client_name; ?></h2><br>
					<span>
						<?php echo ($invoice->client_address_1) ? $invoice->client_address_1 . '<br>' : ''; ?>
						<?php echo ($invoice->client_address_2) ? $invoice->client_address_2 . '<br>' : ''; ?>
						<?php echo ($invoice->client_city) ? $invoice->client_city : ''; ?>
						<?php echo ($invoice->client_state) ? $invoice->client_state : ''; ?>
						<?php echo ($invoice->client_zip) ? $invoice->client_zip : ''; ?>
						<?php echo ($invoice->client_country) ? '<br>' . $invoice->client_country : ''; ?>
					</span>
					<br><br>
					<?php if ($invoice->client_phone) { ?>
					<span><strong><?php echo lang('phone'); ?>:</strong> <?php echo $invoice->client_phone; ?></span><br>
					<?php } ?>
					<?php if ($invoice->client_email) { ?>
					<span><strong><?php echo lang('email'); ?>:</strong> <?php echo $invoice->client_email; ?></span>
					<?php } ?>

				</div>

				<table style="width: auto" class="pull-right table table-striped table-bordered">
                    
                    <tbody>
                        <tr>
                            <td><?php echo lang('invoice'); ?> #</td>
                            <td><?php echo $invoice->invoice_number; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('date'); ?></td>
                            <td><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('due_date'); ?></td>
                            <td><?php echo date_from_mysql($invoice->invoice_date_due); ?></td>
                        </tr>

                    </tbody>

				</table>

			</div>

            <table id="item_table" class="items table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?php echo lang('item'); ?></th>
                        <th><?php echo lang('description'); ?></th>
                        <th><?php echo lang('quantity'); ?></th>
                        <th><?php echo lang('price'); ?></th>
                        <th><?php echo lang('subtotal'); ?></th>
                        <th><?php echo lang('tax'); ?></th>
                        <th><?php echo lang('total'); ?></th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($items as $item) { ?>
                    <tr class="item">
                        <td><?php echo $item->item_name; ?></td>
                        <td><?php echo nl2br($item->item_description); ?></td>
                        <td><?php echo $item->item_quantity; ?></td>
                        <td><?php echo format_currency($item->item_price); ?></td>
                        <td><?php echo format_currency($item->item_subtotal); ?></td>
                        <td><?php echo format_currency($item->item_tax_total); ?></td>
                        <td><?php echo format_currency($item->item_total); ?></td>
                    </tr>
                    <?php } ?>

                </tbody>

            </table>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?php echo lang('subtotal'); ?></th>
                        <th><?php echo lang('item_tax'); ?></th>
                        <th><?php echo lang('invoice_tax'); ?></th>
                        <th><?php echo lang('total'); ?></th>
                        <th><?php echo lang('paid'); ?></th>
                        <th><?php echo lang('balance'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
                        <td><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
                        <td>
                            <?php if ($invoice_tax_rates) { foreach ($invoice_tax_rates as $invoice_tax_rate) { ?>
                                <strong><?php echo anchor('invoices/delete_invoice_tax/' . $invoice->invoice_id . '/' . $invoice_tax_rate->invoice_tax_rate_id, lang('remove')) . ' ' . $invoice_tax_rate->invoice_tax_rate_name . ' ' . $invoice_tax_rate->invoice_tax_rate_percent; ?>%:</strong>				
                                <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?><br>
                            <?php } } else { echo format_currency('0'); }?>
                        </td>
                        <td><?php echo format_currency($invoice->invoice_total); ?></td>
                        <td><?php echo format_currency($invoice->invoice_paid); ?></td>
                        <td><?php echo format_currency($invoice->invoice_balance); ?></td>
                    </tr>
                </tbody>
            </table>
			
			<p><strong><?php echo lang('invoice_terms'); ?></strong></p>
			<p><?php echo nl2br($invoice->invoice_terms); ?></p>

		</div>
		
	</form>

</div>