<script>
    $(function () {
        // Display the create quote modal
        $('#create-quote').modal('show');

        $('.simple-select').select2();

        <?php $this->layout->load_view('clients/script_select2_client_id.js'); ?>

        // Creates the quote
        $('#quote_create_confirm').click(function () {
            console.log('clicked');
            // Posts the data to validate and create the quote;
            // will create the new client if necessary
            $.post("<?php echo site_url('quotes/ajax/create'); ?>", {
                    client_id: $('#create_quote_client_id').val(),
                    quote_date_created: $('#quote_date_created').val(),
                    quote_password: $('#quote_password').val(),
                    user_id: '<?php echo $this->session->userdata('user_id'); ?>',
                    invoice_group_id: $('#invoice_group_id').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        // The validation was successful and quote was created
                        window.location = "<?php echo site_url('quotes/view'); ?>/" + response.quote_id;
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

<div id="create-quote" class="modal modal-lg" role="dialog" aria-labelledby="modal_create_quote" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('create_quote'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="create_quote_client_id"><?php _trans('client'); ?></label>
                <select name="client_id" id="create_quote_client_id" class="client-id-select form-control"
                        autofocus="autofocus">
                    <?php if (!empty($client)) : ?>
                        <option value="<?php echo $client->client_id; ?>"><?php _htmlsc(format_client($client)); ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group has-feedback">
                <label for="quote_date_created">
                    <?php _trans('quote_date'); ?>
                </label>

                <div class="input-group">
                    <input name="quote_date_created" id="quote_date_created"
                           class="form-control datepicker"
                           value="<?php echo date(date_format_setting()); ?>">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="quote_password"><?php _trans('quote_password'); ?></label>
                <input type="text" name="quote_password" id="quote_password" class="form-control"
                       value="<?php echo get_setting('quote_pre_password') ? '' : get_setting('quote_pre_password') ?>"
                       autocomplete="off">
            </div>

            <div class="form-group">
                <label for="invoice_group_id"><?php _trans('invoice_group'); ?>: </label>
                <select name="invoice_group_id" id="invoice_group_id" class="form-control simple-select">
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                            <?php check_select(get_setting('default_quote_group'), $invoice_group->invoice_group_id); ?>>
                            <?php _htmlsc($invoice_group->invoice_group_name); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax-loader" id="quote_create_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
