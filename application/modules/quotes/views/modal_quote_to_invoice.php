<script type="text/javascript">
	$(function()
	{
		$('.datepicker').datepicker( {autoclose: true, format: '<?php echo date_format_datepicker(); ?>'} );
		
		// Display the create quote modal
		$('#modal_quote_to_invoice').modal('show');
		
		// Creates the invoice
		$('#quote_to_invoice_confirm').click(function()
		{
			$.post("<?php echo site_url('quotes/ajax/quote_to_invoice'); ?>", { 
				quote_id: <?php echo $quote_id; ?>,
				client_name: $('#client_name').val(),
				invoice_date_created: $('#invoice_date_created').val(),
				invoice_group_id: $('#invoice_group_id').val(),
				user_id: $('#user_id').val()
			},
			function(data) {
				var response = JSON.parse(data);
				if (response.success == '1')
				{
					window.location = "<?php echo site_url('invoices/view'); ?>/" + response.invoice_id;
				}
				else
				{
					// The validation was not successful
					$('.control-group').removeClass('error');
					for (var key in response.validation_errors) {
						$('#' + key).parent().parent().addClass('error');
					}
				}
			});
		});
	});
	
</script>

<div id="modal_quote_to_invoice" class="modal hide">
	<form class="form-horizontal">
		<div class="modal-header">
			<a data-dismiss="modal" class="close">x</a>
			<h3><?php echo lang('quote_to_invoice'); ?></h3>
		</div>
		<div class="modal-body">

				<input type="hidden" name="client_name" id="client_name" value="<?php echo $quote->client_name; ?>">
				<input type="hidden" name="user_id" id="user_id" value="<?php echo $quote->user_id; ?>">

			<div class="control-group">
				<label class="control-label"><?php echo lang('invoice_date'); ?>: </label>
				<div class="controls input-append date datepicker">
					<input size="16" type="text" name="invoice_date_created" id="invoice_date_created" readonly>
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('invoice_group'); ?>: </label>
				<div class="controls">
					<select name="invoice_group_id" id="invoice_group_id">
						<option value=""></option>
						<?php foreach ($invoice_groups as $invoice_group) { ?>
						<option value="<?php echo $invoice_group->invoice_group_id; ?>" <?php if ($this->mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>><?php echo $invoice_group->invoice_group_name; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

		</div>

		<div class="modal-footer">
            <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="icon-white icon-remove"></i> <?php echo lang('cancel'); ?></button>
			<button class="btn btn-primary" id="quote_to_invoice_confirm" type="button"><i class="icon-white icon-ok"></i> <?php echo lang('submit'); ?></button>
		</div>

	</form>

</div>