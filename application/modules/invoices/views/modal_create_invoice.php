<script>
    $(function () {
        // Display the create invoice modal
        $('#create-invoice').modal('show');

        // Enable select2 for all selects
        $('.simple-select').select2();

        <?php $this->layout->load_view('clients/script_select2_client_id.js'); ?>

        // Creates the invoice
        $('#invoice_create_confirm').click(function () {
            // Posts the data to validate and create the invoice;
            // will create the new client if necessar
            $.post("<?php echo site_url('invoices/ajax/create'); ?>", {
                    client_id: $('#create_invoice_client_id').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    invoice_time_created: '<?php echo date('H:i:s') ?>',
                    invoice_password: $('#invoice_password').val(),
                    user_id: '<?php echo $this->session->userdata('user_id'); ?>',
                    payment_method: $('#payment_method_id').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        // The validation was successful and invoice was created
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

<div id="create-invoice" class="modal modal-lg"
     role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('create_invoice'); ?></h4>
        </div>
        <div class="modal-body">

            <input class="hidden" id="payment_method_id"
                   value="<?php echo get_setting('invoice_default_payment_method'); ?>">

            <div class="form-group">
                <label for="create_invoice_client_id"><?php _trans('client'); ?></label>
                <select name="client_id" id="create_invoice_client_id" class="client-id-select form-control"
                        autofocus="autofocus">
                    <?php if (!empty($client)) : ?>
                        <option value="<?php echo $client->client_id; ?>"><?php _htmlsc(format_client($client)); ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group has-feedback">
                <label for="invoice_date_created"><?php _trans('invoice_date'); ?></label>

                <div class="input-group">
                    <input name="invoice_date_created" id="invoice_date_created"
                           class="form-control datepicker"
                           value="<?php echo date(date_format_setting()); ?>">
                    <span class="input-group-addon">
                    <i class="fa fa-calendar fa-fw"></i>
                </span>
                </div>
            </div>

            <div class="form-group">
                <label for="invoice_password"><?php _trans('invoice_password'); ?></label>
                <input type="text" name="invoice_password" id="invoice_password" class="form-control"
                       value="<?php echo get_setting('invoice_pre_password') == '' ? '' : get_setting('invoice_pre_password'); ?>"
                       style="margin: 0 auto;" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="invoice_group_id"><?php _trans('invoice_group'); ?></label>
                <select name="invoice_group_id" id="invoice_group_id" class="form-control simple-select">
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                                <?php if (get_setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>>
                            <?php _htmlsc($invoice_group->invoice_group_name); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax-loader" id="invoice_create_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
