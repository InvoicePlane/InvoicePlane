<script type="text/javascript">
	$(function()
	{
		$('#modal_delete_quote_confirm').click(function()
		{
			quote_id = $(this).data('quote-id');
			window.location = '<?php echo site_url('quotes/delete'); ?>/' + quote_id;
		});
	});
</script>

<div id="delete-quote" class="modal hide">
	<div class="modal-header">
		<a data-dismiss="modal" class="close">×</a>
		<h3><?php echo lang('delete_quote'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo lang('delete_quote_warning'); ?></p>
	</div>
	<div class="modal-footer">
		<a href="#" id="modal_delete_quote_confirm" class="btn btn-primary" data-quote-id="<?php echo $quote->quote_id; ?>"><?php echo lang('yes'); ?></a>
		<a href="#" class="btn btn-danger" data-dismiss="modal"><?php echo lang('no'); ?></a>
	</div>
</div>