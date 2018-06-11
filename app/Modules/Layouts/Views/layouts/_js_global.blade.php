<script type="text/javascript">

  function notify (message, type) {
    $.notify({
      message: message
    }, {
      type: type
    });
  }

  function showErrors (errors, placeholder) {

    $('.input-group.has-error').removeClass('has-error');
    $(placeholder).html('');
    if (errors == null && placeholder) {
      return;
    }

    $.each(errors, function (id, message) {
      if (id) $('#' + id).parents('.input-group').addClass('has-error');
      if (placeholder) $(placeholder).append('<div class="alert alert-danger">' + message[0] + '</div>');
    });

  }

  function clearErrors () {
    $('.input-group.has-error').removeClass('has-error');
  }

  $(function () {

    $.notifyDefaults({
      placement: {
        from: 'bottom'
      }
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('.create-quote').click(function () {
      clientName = $(this).data('unique-name');
      $('#modal-placeholder').load('{{ route('quotes.create') }}');
    });

    $('.create-invoice').click(function () {
      clientName = $(this).data('unique-name');
      $('#modal-placeholder').load('{{ route('invoices.create') }}');
    });

    $('.create-recurring-invoice').click(function () {
      clientName = $(this).data('unique-name');
      $('#modal-placeholder').load('{{ route('recurringInvoices.create') }}');
    });

    $(document).on('click', '.email-quote', function () {
      $('#modal-placeholder').load('{{ route('quoteMail.create') }}', {
        quote_id: $(this).data('quote-id'),
        redirectTo: $(this).data('redirect-to')
      }, function (response, status, xhr) {
        if (status == 'error') {
          alert('{{ trans('ip.problem_with_email_template') }}');
        }
      });
    });

    $(document).on('click', '.email-invoice', function () {
      $('#modal-placeholder').load('{{ route('invoiceMail.create') }}', {
        invoice_id: $(this).data('invoice-id'),
        redirectTo: $(this).data('redirect-to')
      }, function (response, status, xhr) {
        if (status == 'error') {
          alert('{{ trans('ip.problem_with_email_template') }}');
        }
      });
    });

    $(document).on('click', '.enter-payment', function () {
      $('#modal-placeholder').load('{{ route('payments.create') }}', {
        invoice_id: $(this).data('invoice-id'),
        invoice_balance: $(this).data('invoice-balance'),
        redirectTo: $(this).data('redirect-to')
      });
    });

    $('#bulk-select-all').click(function () {
      if ($(this).prop('checked')) {
        $('.bulk-record').prop('checked', true);
        if ($('.bulk-record:checked').length > 0) {
          $('.bulk-actions').show();
        }
      }
      else {
        $('.bulk-record').prop('checked', false);
        $('.bulk-actions').hide();
      }
    });

    $('.bulk-record').click(function () {
      if ($('.bulk-record:checked').length > 0) {
        $('.bulk-actions').show();
      }
      else {
        $('.bulk-actions').hide();
      }
    });

    $('.bulk-actions').hide();

  });

  function resizeIframe (obj, minHeight) {
    obj.style.height = '';
    var height = obj.contentWindow.document.body.scrollHeight;

    if (height < minHeight) {
      obj.style.height = minHeight + 'px';
    }
    else {
      obj.style.height = (height + 50) + 'px';
    }
  }
</script>