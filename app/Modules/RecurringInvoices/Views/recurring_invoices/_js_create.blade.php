<script>

    $(function () {

        $('#create-recurring-invoice').modal();

        $('#create-recurring-invoice').on('shown.bs.modal', function () {
            $('#create_client_name').focus();
            $('#create_client_name').typeahead('val', clientName);
        });

        $('#create_next_date').datepicker({format: '{{ config('ip.datepickerFormat') }}', autoclose: true});
        $('#create_stop_date').datepicker({format: '{{ config('ip.datepickerFormat') }}', autoclose: true});

        $('#recurring-invoice-create-confirm').click(function () {

            $.post('{{ route('recurringInvoices.store') }}', {
                user_id: $('#user_id').val(),
                company_profile_id: $('#company_profile_id').val(),
                client_name: $('#create_client_name').val(),
                group_id: $('#create_group_id').val(),
                next_date: $('#create_next_date').val(),
                stop_date: $('#create_stop_date').val(),
                recurring_frequency: $('#recurring_frequency').val(),
                recurring_period: $('#recurring_period').val()
            }).done(function (response) {
                window.location = response.url;
            }).fail(function (response) {
                showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
            });
        });

    });

</script>
