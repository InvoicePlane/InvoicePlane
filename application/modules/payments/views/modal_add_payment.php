<script type="text/javascript">
    $(function()
    {
        $('#enter-payment').modal('show');
        
        $('#enter-payment').on('shown', function() {
            $('#payment_amount').focus();
        });
        
        $('.datepicker').datepicker({ format: '<?php echo date_format_datepicker(); ?>'});

        $('#btn_modal_payment_submit').click(function()
        {
            $.post("<?php echo site_url('payments/ajax/add'); ?>", {
                invoice_id: $('#invoice_id').val(),
                payment_amount: $('#payment_amount').val(),
                payment_method_id: $('#payment_method_id').val(),
                payment_date: $('#payment_date').val(),
                payment_note: $('#payment_note').val()
            },
            function(data) {
                var response = JSON.parse(data);
                if (response.success == '1')
                {
                    // The validation was successful and payment was added
                    window.location = "<?php echo $_SERVER['HTTP_REFERER']; ?>";
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

<div id="enter-payment" class="modal hide">
	<div class="modal-header">
		<a data-dismiss="modal" class="close">×</a>
		<h3><?php echo lang('enter_payment'); ?></h3>
	</div>
	<div class="modal-body">
		<form class="form-horizontal">
			
			<input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $invoice_id; ?>">

			<div class="control-group">

				<label class="control-label"><?php echo lang('amount'); ?>: </label>
				<div class="controls">
					<input type="text" name="payment_amount" id="payment_amount" value="<?php echo format_amount($invoice_balance); ?>">
				</div>

			</div>

			<div class="control-group">

				<label class="control-label"><?php echo lang('payment_date'); ?>: </label>
				<div class="controls input-append date datepicker">
					<input size="16" type="text" name="payment_date" id="payment_date" value="<?php echo date(date_format_setting()); ?>" readonly>
					<span class="add-on"><i class="icon-th"></i></span>
				</div>

			</div>

			<div class="control-group">

				<label class="control-label"><?php echo lang('payment_method'); ?>: </label>
				<div class="controls">
					<select name="payment_method_id" id="payment_method_id">
						<option value=""></option>
						<?php foreach ($payment_methods as $payment_method) { ?>
						<option value="<?php echo $payment_method->payment_method_id; ?>"><?php echo $payment_method->payment_method_name; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="control-group">

				<label class="control-label"><?php echo lang('note'); ?>: </label>
				<div class="controls">
					<textarea name="payment_note" id="payment_note"></textarea>
				</div>

			</div>
		</form>
	</div>

	<div class="modal-footer">
        <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="icon-white icon-remove"></i> <?php echo lang('cancel'); ?></button>
		<button class="btn btn-primary" id="btn_modal_payment_submit" type="button"><i class="icon-white icon-ok"></i> <?php echo lang('submit'); ?></button>
	</div>

</div>