<script>
    $(function () {

        $('#form-edit-client').on('submit', function (e) {

            e.preventDefault();

            $.post(this.action, $(this).serialize())
                .done(function () {
                    $('#modal-edit-client').modal('hide');
                    $('#col-to').load('{{ $refreshToRoute }}', {
                        id: '{{ $id }}'
                    });
                }).fail(function (response) {
                showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
            });
        });

    });
</script>
