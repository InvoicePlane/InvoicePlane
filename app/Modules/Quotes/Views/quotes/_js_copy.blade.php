<script>

    $(function () {
        $('#modal-copy-quote').modal();

        $('#modal-copy-quote').on('shown.bs.modal', function () {
            $('#client_name').focus();
        });

        $('#copy_quote_date').datepicker({format: '{{ config('ip.datepickerFormat') }}', autoclose: true});

        var clients = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.num);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '{{ route('clients.ajax.lookup') }}' + '?query=%QUERY'
        });

        clients.initialize();

        $('#copy_client_name').typeahead(null, {
            minLength: 3,
            source: clients.ttAdapter()
        });

        // Creates the quote
        $('#btn-copy-quote-submit').click(function () {
            $.post('{{ route('quoteCopy.store') }}', {
                quote_id: {{ $quote->id }},
                client_name: $('#copy_client_name').val(),
                company_profile_id: $('#copy_company_profile_id').val(),
                quote_date: $('#copy_quote_date').val(),
                group_id: $('#copy_group_id').val(),
                user_id: {{ $user_id }}
            }).done(function (response) {
                window.location = '{{ url('quotes') }}' + '/' + response.id + '/edit';
            }).fail(function (response) {
                showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
            });
        });
    });

</script>
