<script>
    $(function () {
        $('#btn-submit-change-client').click(function () {

            $.post('{{ route('clients.ajax.checkName') }}', {
                client_name: $('#change_client_name').val()
            }).done(function (response) {

                $('#modal-lookup-client').modal('hide');

                $.post('{{ $updateClientIdRoute }}', {
                    client_id: response.client_id,
                    id: '{{ $id }}'
                }).done(function () {
                    $('#col-to').load('{{ $refreshToRoute }}', {
                        id: '{{ $id }}'
                    });
                });

            }).fail(function (response) {
                if (response.status === 400) {
                    showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
                } else {
                    alert('@lang('ip.unknown_error')');
                }
            });
        });
    });
</script>
