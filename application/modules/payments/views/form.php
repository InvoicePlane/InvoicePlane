<script type="text/javascript">
$(function() {
   $('#invoice_id').focus();
   
   amounts = JSON.parse('<?php echo $amounts; ?>');
   
   $('#invoice_id').change(function() {
       $('#payment_amount').val(amounts["invoice" + $('#invoice_id').val()]);
   });
   
});
</script>

<form method="post" class="form-horizontal">
    
    <?php if ($payment_id) { ?>
    <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>">
    <?php } ?>

	<div class="headerbar">
		<h1><?php echo lang('payment_form'); ?></h1>
		<?php $this->layout->load_view('layout/header_buttons'); ?>
	</div>

	<div class="content">

		<?php $this->layout->load_view('layout/alerts'); ?>

			<div class="control-group">
				<label class="control-label"><?php echo lang('invoice'); ?>: </label>
				<div class="controls">
					
					<select name="invoice_id" id="invoice_id">
						<?php if (!$payment_id) { ?>
						<option value=""></option>
						<?php foreach ($open_invoices as $invoice) { ?>
						<option value="<?php echo $invoice->invoice_id; ?>" <?php if ($this->mdl_payments->form_value('invoice_id') == $invoice->invoice_id) { ?>selected="selected"<?php } ?>><?php echo $invoice->invoice_number . ' - ' . $invoice->client_name . ' - ' . format_currency($invoice->invoice_balance); ?></option>
						<?php } ?>
						<?php } else { ?>
						<option value="<?php echo $payment->invoice_id; ?>"><?php echo $payment->invoice_number . ' - ' . $payment->client_name . ' - ' . format_currency($payment->invoice_balance); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('date'); ?>: </label>
				<div class="controls input-append date datepicker">
					<input type="text" name="payment_date" id="payment_date" value="<?php echo date_from_mysql($this->mdl_payments->form_value('payment_date')); ?>" readonly>
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('amount'); ?>: </label>
				<div class="controls">
					<input type="text" name="payment_amount" id="payment_amount" value="<?php echo format_amount($this->mdl_payments->form_value('payment_amount')); ?>">
				</div>
			</div>

			<div class="control-group">

				<label class="control-label"><?php echo lang('payment_method'); ?>: </label>
				<div class="controls">
					<select name="payment_method_id">
						<option value="0"></option>
						<?php foreach ($payment_methods as $payment_method) { ?>
						<option value="<?php echo $payment_method->payment_method_id; ?>" <?php if ($this->mdl_payments->form_value('payment_method_id') == $payment_method->payment_method_id) { ?>selected="selected"<?php } ?>>
							<?php echo $payment_method->payment_method_name; ?>
						</option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="control-group">

				<label class="control-label"><?php echo lang('note'); ?>: </label>
				<div class="controls">
					<textarea name="payment_note"><?php echo $this->mdl_payments->form_value('payment_note'); ?></textarea>
				</div>

			</div>

            <?php foreach ($custom_fields as $custom_field) { ?>
            <div class="control-group">
                <label class="control-label"><?php echo $custom_field->custom_field_label; ?>: </label>
                <div class="controls">
                    <input type="text" name="custom[<?php echo $custom_field->custom_field_column; ?>]" id="<?php echo $custom_field->custom_field_column; ?>" value="<?php echo form_prep($this->mdl_payments->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                </div>
            </div>
            <?php } ?>

	</div>

</form>