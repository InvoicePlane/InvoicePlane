<script type="text/javascript">
    $(function () {
        $('#enter-expense').modal('show');
        $('#enter-expense').on('shown', function () {
            $('#expense_amount').focus();
        });
        $('#btn_modal_expense_submit').click(function () {
            $.post("<?php echo site_url('expenses/ajax/add'); ?>", {
                    invoice_id: $('#invoice_id').val(),
                    expense_amount: $('#expense_amount').val(),
                    payment_method_id: $('#payment_method_id').val(),
                    expense_date: $('#expense_date').val(),
                    expense_note: $('#expense_note').val()
                },
                function (data) {
                    <?php echo (IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        // The validation was successful and expense was added
                        window.location = "<?php echo $_SERVER['HTTP_REFERER']; ?>";
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });
</script>

<div id="enter-expense" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_enter_expense" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo lang('enter_expense'); ?></h3>
        </div>

        <div class="modal-body">
            <form>

                <div class="form-group">
                    <label for="expense_amount"><?php echo lang('amount'); ?></label>

                    <div class="controls">
                        <input type="text" name="expense_amount" id="expense_amount" class="form-control"
                               value="<?php echo (isset($invoice_balance) ? format_amount($invoice_balance):''); ?>">
                    </div>
                </div>

                <div class="form-group has-feedback">

                    <label class="expense_date"><?php echo lang('expense_date'); ?></label>

                    <div class="input-group">
                        <input name="expense_date" id="expense_date"
                               class="form-control datepicker"
                               value="<?php echo date(date_format_setting()); ?>">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar fa-fw"></i>
                        </span>
                    </div>

                </div>

                <div class="form-group">
                    <label for="payment_method_id"><?php echo lang('payment_method'); ?></label>

                    <div class="controls">

                        <?php
                        // Add a hidden input field if a payment method was set to pass the disabled attribute
                        if ($this->mdl_expenses->form_value('payment_method_id')) { ?>
                            <input type="hidden" name="payment_method_id" class="hidden"
                                   value="<?php echo $this->mdl_expenses->form_value('payment_method_id'); ?>">
                        <?php } ?>

                        <select name="payment_method_id" id="payment_method_id" class="form-control"
                            <?php echo(!empty($invoice_payment_method) ? 'disabled="disabled"' : ''); ?>>
                            <option value=""></option>
                            <?php foreach ($payment_methods as $payment_method) { ?>
                                <option value="<?php echo $payment_method->payment_method_id; ?>"
                                    <?php if (isset($invoice_payment_method)
                                            && $invoice_payment_method == $payment_method->payment_method_id) {
                                        echo 'selected="selected"';
                                    }?>>
                                    <?php echo $payment_method->payment_method_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="expense_note"><?php echo lang('note'); ?></label>

                    <div class="controls">
                        <textarea name="expense_note" id="expense_note" class="form-control"></textarea>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success" id="btn_modal_expense_submit" type="button">
                    <i class="fa fa-check"></i>
                    <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>