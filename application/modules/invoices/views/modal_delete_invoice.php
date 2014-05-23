<script type="text/javascript">
	$(function()
	{
		$('#modal_delete_invoice_confirm').click(function()
		{
			// alert($(this).data('invoice-id'));
			invoice_id = $(this).data('invoice-id');
			window.location = '<?php echo site_url('invoices/delete'); ?>/' + invoice_id;
		});
	});
</script>

<div id="delete-invoice" class="modal hide">
	<div class="modal-header">
		<a data-dismiss="modal" class="close">×</a>
		<h3><?php echo lang('delete_invoice'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo lang('delete_invoice_warning'); ?></p>
	</div>
	<div class="modal-footer">
		<a href="#" id="modal_delete_invoice_confirm" class="btn btn-primary" data-invoice-id="<?php echo $invoice->invoice_id; ?>"><?php echo lang('yes'); ?></a>
		<a href="#" class="btn btn-danger" data-dismiss="modal"><?php echo lang('no'); ?></a>
	</div>
</div>