<script type="text/javascript">
	$(function()
	{
		$('#quote_tax_submit').click(function()
		{
			$.post("<?php echo site_url('quotes/ajax/save_quote_tax_rate'); ?>", { 
				quote_id: <?php echo $quote_id; ?>,
				tax_rate_id: $('#tax_rate_id').val(),
				include_item_tax: $('#include_item_tax').val()
			},
			function(data) {
				var response = JSON.parse(data);
				if (response.success == 1)
				{
					window.location = "<?php echo site_url('quotes/view'); ?>/" + <?php echo $quote_id; ?>;
				}
			});
		});
	});
</script>

<div id="add-quote-tax" class="modal hide">
	<form class="form-horizontal">
		<div class="modal-header">
			<a data-dismiss="modal" class="close">×</a>
			<h3><?php echo lang('add_quote_tax'); ?></h3>
		</div>
		<div class="modal-body">

			<div class="control-group">
				<label class="control-label"><?php echo lang('tax_rate'); ?>: </label>
				<div class="controls">
					<select name="tax_rate_id" id="tax_rate_id">
						<option value="0"><?php echo lang('none'); ?></option>
						<?php foreach ($tax_rates as $tax_rate) { ?>
						<option value="<?php echo $tax_rate->tax_rate_id; ?>"><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('tax_rate_placement'); ?></label>
				<div class="controls">
					<select name="include_item_tax" id="include_item_tax">
						<option value="0"><?php echo lang('apply_before_item_tax'); ?></option>
						<option value="1"><?php echo lang('apply_after_item_tax'); ?></option>
					</select>
				</div>
			</div>
			
		</div>

		<div class="modal-footer">
            <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="icon-white icon-remove"></i> <?php echo lang('cancel'); ?></button>
			<button class="btn btn-primary" id="quote_tax_submit" type="button"><i class="icon-white icon-ok"></i> <?php echo lang('submit'); ?></button>
		</div>

	</form>

</div>