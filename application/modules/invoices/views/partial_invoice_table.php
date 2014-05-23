<table class="table table-striped">

	<thead>
		<tr>
			<th><?php echo lang('status'); ?></th>
			<th><?php echo lang('invoice'); ?></th>
			<th><?php echo lang('created'); ?></th>
			<th><?php echo lang('due_date'); ?></th>
			<th><?php echo lang('client_name'); ?></th>
			<th style="text-align: right; padding-right: 25px;"><?php echo lang('amount'); ?></th>
			<th style="text-align: right; padding-right: 25px;"><?php echo lang('balance'); ?></th>
			<th><?php echo lang('options'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($invoices as $invoice) { ?>
		<tr>
			<td>
                <span class="label <?php echo $invoice_statuses[$invoice->invoice_status_id]['class']; ?>"><?php echo $invoice_statuses[$invoice->invoice_status_id]['label']; ?></span>
			</td>
			<td><a href="<?php echo site_url('invoices/view/' . $invoice->invoice_id); ?>" title="<?php echo lang('edit'); ?>"><?php echo $invoice->invoice_number; ?></a></td>
			<td><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
            <td><span class="<?php if ($invoice->is_overdue) { ?>font-overdue<?php } ?>"><?php echo date_from_mysql($invoice->invoice_date_due); ?></span></td>
			<td><a href="<?php echo site_url('clients/view/' . $invoice->client_id); ?>" title="<?php echo lang('view_client'); ?>"><?php echo $invoice->client_name; ?></a></td>
			<td style="text-align: right; padding-right: 25px;"><?php echo format_currency($invoice->invoice_total); ?></td>
			<td style="text-align: right; padding-right: 25px;"><?php echo format_currency($invoice->invoice_balance); ?></td>
			<td>
				<div class="options btn-group">
					<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <?php echo lang('options'); ?></a>
					<ul class="dropdown-menu">
						<li>
							<a href="<?php echo site_url('invoices/view/' . $invoice->invoice_id); ?>">
								<i class="icon-pencil"></i> <?php echo lang('edit'); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo site_url('invoices/generate_pdf/' . $invoice->invoice_id); ?>">
								<i class="icon-print"></i> <?php echo lang('download_pdf'); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo site_url('mailer/invoice/' . $invoice->invoice_id); ?>">
								<i class="icon-envelope"></i> <?php echo lang('send_email'); ?>
							</a>
						</li>
                        <li>
							<a href="#" class="invoice-add-payment" data-invoice-id="<?php echo $invoice->invoice_id; ?>" data-invoice-balance="<?php echo $invoice->invoice_balance; ?>">
								<i class="icon-shopping-cart"></i> <?php echo lang('enter_payment'); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo site_url('invoices/delete/' . $invoice->invoice_id); ?>" onclick="return confirm('<?php echo lang('delete_invoice_warning'); ?>');">
								<i class="icon-trash"></i> <?php echo lang('delete'); ?>
							</a>
						</li>
					</ul>
				</div>
			</td>
		</tr>
		<?php } ?>
	</tbody>

</table>