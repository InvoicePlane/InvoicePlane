<script type="text/javascript">
    $(function () {
        // Display the create quote modal
        $('#modal_quote_to_invoice').modal('show');

        // Creates the invoice
        $('#quote_to_invoice_confirm').click(function () {
            $.post("<?php echo site_url('quotes/ajax/quote_to_invoice'); ?>", {
                    quote_id: <?php echo $quote_id; ?>,
                    client_name: $('#client_name').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    invoice_password: $('#invoice_password').val(),
                    user_id: $('#user_id').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?php echo site_url('invoices/view'); ?>/" + response.invoice_id;
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

<div id="modal_quote_to_invoice" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_quote_to_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo trans('quote_to_invoice'); ?></h3>
        </div>
        <div class="modal-body">

            <input type="hidden" name="client_name" id="client_name"
                   value="<?php echo $quote->client_name; ?>">
            <input type="hidden" name="user_id" id="user_id"
                   value="<?php echo $quote->user_id; ?>">

            <div class="form-group has-feedback">
                <label for="invoice_date_created">
                    <?php echo trans('invoice_date'); ?>
                </label>

                <div class="input-group">
                    <input name="invoice_date_created" id="invoice_date_created"
                           class="form-control datepicker">
						<span class="input-group-addon">
								<i class="fa fa-calendar fa-fw"></i>
						</span>
                </div>
            </div>

            <div class="form-group">
                <label for="invoice_password"><?php echo trans('invoice_password'); ?></label>
                <input type="text" name="invoice_password" id="invoice_password" class="form-control"
                       value="<?php if ($this->mdl_settings->setting('invoice_pre_password') == '') {
                           echo '';
                       } else {
                           echo $this->mdl_settings->setting('invoice_pre_password');
                       } ?>" style="margin: 0 auto;" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="invoice_group_id">
                    <?php echo trans('invoice_group'); ?>
                </label>
                <select name="invoice_group_id" id="invoice_group_id" class="form-control">
                    <option value=""></option>
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                                <?php if ($this->mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>>
                            <?php echo $invoice_group->invoice_group_name; ?></option>
                    <?php } ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo trans('cancel'); ?>
                </button>
                <button class="btn btn-success" id="quote_to_invoice_confirm" type="button">
                    <i class="fa fa-check"></i> <?php echo trans('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
