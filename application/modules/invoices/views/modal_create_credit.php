<script>
    $(function () {
        $('#modal-create-credit-invoice').modal('show');
        $('#create-credit-confirm').click(function () {
            $.post("<?php echo site_url('invoices/ajax/create_credit'); ?>", {
                    invoice_id: <?php echo $invoice_id; ?>,
                    client_id: $('#client_id').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    invoice_time_created: '<?php echo date('H:i:s') ?>',
                    invoice_password: $('#invoice_password').val(),
                    user_id: $('#user_id').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
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

<div id="modal-create-credit-invoice" class="modal modal-lg" role="dialog" aria-labelledby="modal-create-credit-invoice"
     aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('create_credit_invoice'); ?></h4>
        </div>
        <div class="modal-body">

            <input type="hidden" name="user_id" id="user_id" class="form-control"
                   value="<?php echo $invoice->user_id; ?>">

            <input type="hidden" name="parent_id" id="parent_id"
                   value="<?php echo $invoice->invoice_id; ?>">

            <input type="hidden" name="client_id" id="client_id" class="hidden"
                   value="<?php echo $invoice->client_id; ?>">

            <input type="hidden" name="invoice_date_created" id="invoice_date_created"
                   value="<?php $credit_date = date_from_mysql(date('Y-m-d', time()), true);
                   echo $credit_date; ?>">

            <div class="form-group">
                <label for="invoice_password"><?php _trans('invoice_password'); ?></label>
                <input type="text" name="invoice_password" id="invoice_password" class="form-control"
                       value="<?php echo get_setting('invoice_pre_password') == '' ? '' : get_setting('invoice_pre_password'); ?>"
                       style="margin: 0 auto;" autocomplete="off">
            </div>

            <div>
                <select name="invoice_group_id" id="invoice_group_id" class="hidden">
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                            <?php if (get_setting('default_invoice_group') == $invoice_group->invoice_group_id) {
                                echo 'selected="selected"';
                                $credit_invoice_group = htmlsc($invoice_group->invoice_group_name);
                            } ?>>
                            <?php echo $credit_invoice_group; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <p><strong><?php _trans('credit_invoice_details'); ?></strong></p>

            <ul>
                <li><?php _trans('client') . ': ' . htmlsc($invoice->client_name); ?></li>
                <li><?php echo trans('credit_invoice_date') . ': ' . $credit_date; ?></li>
                <li><?php echo trans('invoice_group') . ': ' . $credit_invoice_group; ?></li>
            </ul>

            <div class="alert alert-danger no-margin">
                <?php _trans('create_credit_invoice_alert'); ?>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="create-credit-confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('confirm'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
