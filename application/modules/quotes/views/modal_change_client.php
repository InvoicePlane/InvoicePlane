<script>
    $(function () {
        // Display the create invoice modal
        $('#change-client').modal('show');

        <?php $this->layout->load_view('clients/script_select2_client_id.js'); ?>

        // Creates the invoice
        $('#client_change_confirm').click(function () {
            $.post("<?php echo site_url('quotes/ajax/change_client'); ?>", {
                    client_id: $('#change_client_id').val(),
                    quote_id: $('#quote_id').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        // The validation was successful and invoice was created
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

<div id="change-client" class="modal modal-lg" role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('change_client'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="change_client_id"><?php _trans('client'); ?></label>
                <select name="client_id" id="change_client_id" class="client-id-select form-control"
                        autofocus="autofocus"></select>
            </div>

            <input class="hidden" id="quote_id" value="<?php echo $quote_id; ?>">

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="client_change_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
