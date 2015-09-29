<script type="text/javascript">
    $(function () {
        $('#invoice_id').focus();

        amounts = JSON.parse('<?php echo $amounts; ?>');
        invoice_payment_methods = JSON.parse('<?php echo $invoice_payment_methods; ?>');
        $('#invoice_id').change(function () {
            var invoice_identifier = "invoice" + $('#invoice_id').val();
            $('#payment_amount').val(amounts[invoice_identifier]);
            $('#payment_method_id option[value="' + invoice_payment_methods[invoice_identifier] + '"]').prop('selected', true);
            if (invoice_payment_methods[invoice_identifier] != 0) {
                $('#payment_method_id').prop('disabled', true);
            } else {
                $('#payment_method_id').prop('disabled', false);
            }
            ;
        });

    });
</script>

<form method="post" class="form-horizontal">

    <?php if ($payment_id) { ?>
        <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>">
    <?php } ?>

    <div id="headerbar">
        <h1><?php echo lang('payment_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="invoice_id" class="control-label"><?php echo lang('invoice'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="invoice_id" id="invoice_id" class="form-control">
                    <?php if (!$payment_id) { ?>
                        <option value=""></option>
                        <?php foreach ($open_invoices as $invoice) { ?>
                            <option value="<?php echo $invoice->invoice_id; ?>"
                                    <?php if ($this->mdl_payments->form_value('invoice_id') == $invoice->invoice_id) { ?>selected="selected"<?php } ?>><?php echo $invoice->invoice_number . ' - ' . $invoice->client_name . ' - ' . format_currency($invoice->invoice_balance); ?></option>
                        <?php } ?>
                    <?php } else { ?>
                        <option
                            value="<?php echo $payment->invoice_id; ?>"><?php echo $payment->invoice_number . ' - ' . $payment->client_name . ' - ' . format_currency($payment->invoice_balance); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_date" class="control-label"><?php echo lang('date'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="input-group">
                    <input name="payment_date" id="payment_date"
                           class="form-control datepicker"
                           value="<?php echo date_from_mysql($this->mdl_payments->form_value('payment_date')); ?>">
                  <span class="input-group-addon">
                      <i class="fa fa-calendar fa-fw"></i>
                  </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_amount" class="control-label"><?php echo lang('amount'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="payment_amount" id="payment_amount" class="form-control"
                       value="<?php echo format_amount($this->mdl_payments->form_value('payment_amount')); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_id" class="control-label">
                    <?php echo lang('payment_method'); ?>
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select id="payment_method_id" name="payment_method_id" class="form-control"
                    <?php echo($this->mdl_payments->form_value('payment_method_id') ? 'disabled="disabled"' : ''); ?>>

                    <?php foreach ($payment_methods as $payment_method) { ?>
                        <option value="<?php echo $payment_method->payment_method_id; ?>"
                                <?php if ($this->mdl_payments->form_value('payment_method_id') == $payment_method->payment_method_id) { ?>selected="selected"<?php } ?>>
                            <?php echo $payment_method->payment_method_name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_note" class="control-label"><?php echo lang('note'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="payment_note"
                          class="form-control"><?php echo $this->mdl_payments->form_value('payment_note'); ?></textarea>
            </div>

        </div>

        <?php foreach ($custom_fields as $custom_field) { ?>
            <div class="form-group">
                <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                    <label><?php echo $custom_field->custom_field_label; ?>: </label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?php
                    switch ($custom_field->custom_field_type) {
                        case 'ip_fieldtype_input':
                            ?>
                            <input type="text" name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                   id="<?php echo $custom_field->custom_field_column; ?>"
                                   class="form-control"
                                   value="<?php echo form_prep($this->mdl_payments->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                            <?php
                            break;

                        case 'ip_fieldtype_textarea':
                            ?>
                            <textarea name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                      id="<?php echo $custom_field->custom_field_column; ?>"
                                      class="form-control"><?php echo form_prep($this->mdl_payments->form_value('custom[' . $custom_field->custom_field_column . ']')); ?></textarea>
                            <?php
                            break;
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

    </div>

</form>
