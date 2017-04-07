<script>
    $(function () {
        var $invoice_id = $('#invoice_id');
        $invoice_id.focus();

        amounts = JSON.parse('<?php echo $amounts; ?>');
        invoice_payment_methods = JSON.parse('<?php echo $invoice_payment_methods; ?>');
        $invoice_id.change(function () {
            var invoice_identifier = "invoice" + $('#invoice_id').val();
            $('#payment_amount').val(amounts[invoice_identifier].replace("&nbsp;", " "));
            $('#payment_method_id').find('option[value="' + invoice_payment_methods[invoice_identifier] + '"]').prop('selected', true);

            if (invoice_payment_methods[invoice_identifier] != 0) {
                $('.payment-method-wrapper').append("<input type='hidden' name='payment_method_id' id='payment-method-id-hidden' class='hidden' value='" + invoice_payment_methods[invoice_identifier] + "'>");
                $('#payment_method_id').prop('disabled', true);
            } else {
                $('#payment-method-id-hidden').remove();
                $('#payment_method_id').prop('disabled', false);
            }
        });

    });
</script>

<form method="post" class="form-horizontal">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <?php if ($payment_id) { ?>
        <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>">
    <?php } ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('payment_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="invoice_id" class="control-label"><?php _trans('invoice'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="invoice_id" id="invoice_id" class="form-control simple-select">
                    <?php if (!$payment_id) { ?>
                        <?php foreach ($open_invoices as $invoice) { ?>
                            <option value="<?php echo $invoice->invoice_id; ?>"
                                <?php check_select($this->mdl_payments->form_value('invoice_id'), $invoice->invoice_id); ?>>
                                <?php echo $invoice->invoice_number . ' - ' . format_client($invoice) . ' - ' . format_currency($invoice->invoice_balance); ?>
                            </option>
                        <?php } ?>
                    <?php } else { ?>
                        <option value="<?php echo $payment->invoice_id; ?>">
                            <?php echo $payment->invoice_number . ' - ' . format_client($payment) . ' - ' . format_currency($payment->invoice_balance); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_date" class="control-label"><?php _trans('date'); ?></label>
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
                <label for="payment_amount" class="control-label"><?php _trans('amount'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="payment_amount" id="payment_amount" class="form-control"
                       value="<?php echo format_amount($this->mdl_payments->form_value('payment_amount')); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_id" class="control-label">
                    <?php _trans('payment_method'); ?>
                </label>
            </div>
            <div class="col-xs-12 col-sm-6 payment-method-wrapper">

                <?php
                // Add a hidden input field if a payment method was set to pass the disabled attribute
                if ($this->mdl_payments->form_value('payment_method_id')) { ?>
                    <input type="hidden" name="payment_method_id" class="hidden"
                           value="<?php echo $this->mdl_payments->form_value('payment_method_id'); ?>">
                <?php } ?>

                <select id="payment_method_id" name="payment_method_id" class="form-control simple-select"
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
                <label for="payment_note" class="control-label"><?php _trans('note'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="payment_note"
                          class="form-control"><?php echo $this->mdl_payments->form_value('payment_note', true); ?></textarea>
            </div>

        </div>

        <?php
        $cv = $this->controller->view_data["custom_values"];
        foreach ($custom_fields as $custom_field) {
            print_field($this->mdl_payments, $custom_field, $cv, "col-xs-12 col-sm-2 text-right text-left-xs", "col-xs-12 col-sm-6");
        } ?>

    </div>

</form>
