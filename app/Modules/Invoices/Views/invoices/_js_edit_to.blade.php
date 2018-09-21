<script>
    $(function () {
        $('#btn-edit-client').click(function () {
            $('#modal-placeholder').load('{{ route('clients.ajax.modalEdit') }}', {
                client_id: $(this).data('client-id'),
                refresh_to_route: '{{ route('invoiceEdit.refreshTo') }}',
                id: {{ $invoice->id }}
            });
        });

        $('#btn-change-client').click(function () {
            $('#modal-placeholder').load('{{ route('clients.ajax.modalLookup') }}', {
                id: {{ $invoice->id }},
                update_client_id_route: '{{ route('invoiceEdit.updateClient') }}',
                refresh_to_route: '{{ route('invoiceEdit.refreshTo') }}'
            });
        });
    });
</script>
