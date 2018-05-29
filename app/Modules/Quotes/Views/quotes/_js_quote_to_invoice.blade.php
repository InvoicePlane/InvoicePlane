<script type="text/javascript">

  $(function () {
    // Display the create quote modal
    $('#modal-quote-to-invoice').modal('show');

    $('#to_invoice_date').datepicker({format: '{{ config('fi.datepickerFormat') }}', autoclose: true});

    // Creates the invoice
    $('#btn-quote-to-invoice-submit').click(function () {
      $.post('{{ route('quoteToInvoice.store') }}', {
        quote_id: {{ $quote_id }},
        client_id: {{ $client_id }},
        invoice_date: $('#to_invoice_date').val(),
        group_id: $('#to_invoice_group_id').val(),
        user_id: {{ $user_id }}



      }).done(function (response) {
        window.location = response.redirectTo;
      }).fail(function (response) {
        if (response.status == 400) {
          showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
        } else {
          alert('{{ trans('fi.unknown_error') }}');
        }
      });
    });
  });

</script>