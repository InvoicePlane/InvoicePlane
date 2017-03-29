<?php $this->layout->load_view('clients/jquery_client_lookup'); ?>

<script>
    $(function () {
        // Display the create invoice modal
        $('#change-client').modal('show');

        $("#client_id").select2({
            placeholder: "<?php echo htmlentities(trans('client')); ?>",
            ajax: {
                url: "<?php echo site_url('clients/ajax/name_query'); ?>",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        query: params.term,
                        page: params.page,
                        _ip_csrf: Cookies.get('ip_csrf_cookie')
                    };
                },
                processResults: function (data) {
                    console.log(data);
                    return {
                        results: data
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 2
        });

        // Creates the invoice
        $('#client_change_confirm').click(function () {
            // Posts the data to validate and create the invoice;
            // will create the new client if necessary
            $.post("<?php echo site_url('quotes/ajax/change_client'); ?>", {
                    client_id: $('#client_id').val(),
                    quote_id: $('#quote_id').val(),
                    _ip_csrf: csrf()
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

<div id="change-client" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo trans('change_client'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <select name="client_id" id="client_id" class="form-control" autofocus="autofocus"></select>
            </div>

            <input class="hidden" id="quote_id" value="<?php echo $quote_id; ?>">

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="client_change_confirm" type="button">
                    <i class="fa fa-check"></i> <?php echo trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
