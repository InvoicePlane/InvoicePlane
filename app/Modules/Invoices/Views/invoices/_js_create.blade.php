<script type="text/javascript">

    $(function () {

        $('#create-invoice').modal();

        $('#create-invoice').on('shown.bs.modal', function () {
            $("#create_client_name").focus();
            $('#create_client_name').typeahead('val', clientName);
        });

        $('#create_invoice_date').datepicker({format: '{{ config('fi.datepickerFormat') }}', autoclose: true});

        $('#invoice-create-confirm').click(function () {

            $.post('{{ route('invoices.store') }}', {
                user_id: $('#user_id').val(),
                company_profile_id: $('#company_profile_id').val(),
                client_name: $('#create_client_name').val(),
                invoice_date: $('#create_invoice_date').val(),
                group_id: $('#create_group_id').val()
            }).done(function (response) {
                window.location = '{{ url('invoices') }}' + '/' + response.id + '/edit';
            }).fail(function (response) {
                showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
            });
        });

    });

</script>