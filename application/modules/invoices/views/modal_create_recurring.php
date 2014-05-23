<script type="text/javascript">
	$(function()
	{
		$('.datepicker').datepicker( {autoclose: true, format: '<?php echo date_format_datepicker(); ?>'} );
		
		// Display the create quote modal
		$('#modal_create_recurring').modal('show');
        
        get_recur_start_date();
        
        $('#recur_frequency').change(function()
        {
            get_recur_start_date();
        });
		
		// Creates the invoice
		$('#create_recurring_confirm').click(function()
		{
			$.post("<?php echo site_url('invoices/ajax/create_recurring'); ?>", { 
				invoice_id: <?php echo $invoice_id; ?>,
				recur_start_date: $('#recur_start_date').val(),
				recur_end_date: $('#recur_end_date').val(),
				recur_frequency: $('#recur_frequency').val()
			},
			function(data) {
				var response = JSON.parse(data);
				if (response.success == '1')
				{
					window.location = "<?php echo site_url('invoices/view'); ?>/<?php echo $invoice_id; ?>";
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
        
        function get_recur_start_date()
        {
            $.post("<?php echo site_url('invoices/ajax/get_recur_start_date'); ?>", {
                invoice_date: $('#invoice_date_created').val(),
                recur_frequency: $('#recur_frequency').val()
            }, 
            function(data) {
                $('#recur_start_date').val(data);
            });
        }
	});
	
</script>

<div id="modal_create_recurring" class="modal hide">
	<form class="form-horizontal">
		<div class="modal-header">
			<a data-dismiss="modal" class="close">x</a>
			<h3><?php echo lang('create_recurring'); ?></h3>
		</div>
		<div class="modal-body">
            
			<div class="control-group">
				<label class="control-label"><?php echo lang('every'); ?>: </label>
				<div class="controls">
                    <select name="recur_frequency" id="recur_frequency" style="width: 150px;">
                        <?php foreach ($recur_frequencies as $key=>$lang) { ?>
                        <option value="<?php echo $key; ?>" <?php if ($key == '1M') { ?>selected="selected"<?php } ?>><?php echo lang($lang); ?></option>
                        <?php } ?>
                    </select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('start_date'); ?>: </label>
				<div class="controls input-append date datepicker">
					<input size="16" type="text" name="recur_start_date" id="recur_start_date" readonly>
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
			</div>
            
			<div class="control-group">
				<label class="control-label"><?php echo lang('end_date'); ?>: </label>
				<div class="controls input-append date datepicker">
					<input size="16" type="text" name="recur_end_date" id="recur_end_date" readonly>
					<span class="add-on"><i class="icon-th"></i></span>
                    <span class="help-block"><?php echo lang('optional'); ?></span>
				</div>
			</div>
            
		</div>

		<div class="modal-footer">
            <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="icon-white icon-remove"></i> <?php echo lang('cancel'); ?></button>
			<button class="btn btn-primary" id="create_recurring_confirm" type="button"><i class="icon-white icon-ok"></i> <?php echo lang('submit'); ?></button>
		</div>

	</form>

</div>