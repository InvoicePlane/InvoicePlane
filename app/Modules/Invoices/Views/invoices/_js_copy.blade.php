<script type="text/javascript">

  $(function () {
    $('#modal-copy-invoice').modal();

    $('#modal-copy-invoice').on('shown.bs.modal', function () {
      $('#client_name').focus();
    });

    $('#copy_invoice_date').datepicker({format: '{{ config('ip.datepickerFormat') }}', autoclose: true});

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

    // Creates the invoice
    $('#btn-copy-invoice-submit').click(function () {
      $.post('{{ route('invoiceCopy.store') }}', {
        invoice_id: {{ $invoice->id }},
        client_name: $('#copy_client_name').val(),
        company_profile_id: $('#copy_company_profile_id').val(),
        invoice_date: $('#copy_invoice_date').val(),
        group_id: $('#copy_group_id').val(),
        user_id: {{ $user_id }}
      }).done(function (response) {
        window.location = '{{ url('invoices') }}' + '/' + response.id + '/edit';
      }).fail(function (response) {
        showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
      });
    });
  });

</script>