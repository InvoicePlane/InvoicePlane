<script type="text/javascript">
  $(function () {
    $('#btn-edit-client').click(function () {
      $('#modal-placeholder').load('{{ route('clients.ajax.modalEdit') }}', {
        client_id: $(this).data('client-id'),
        refresh_to_route: '{{ route('recurringInvoiceEdit.refreshTo') }}',
        id: {{ $recurringInvoice->id }}
      });
    });

    $('#btn-change-client').click(function () {
      $('#modal-placeholder').load('{{ route('clients.ajax.modalLookup') }}', {
        id: {{ $recurringInvoice->id }},
        update_client_id_route: '{{ route('recurringInvoiceEdit.updateClient') }}',
        refresh_to_route: '{{ route('recurringInvoiceEdit.refreshTo') }}'
      });
    });
  });
</script>