<script type="text/javascript">

  $(function () {

    $('#modal-enter-payment').modal();

    $('#payment_date').datepicker({format: '{{ config('ip.datepickerFormat') }}', autoclose: true});

    $('#enter-payment-confirm').click(function () {

      var custom_fields = {};

      var $btn = $(this).button('loading');

      $('#payment-custom-fields .custom-form-field').each(function () {
        custom_fields[$(this).data('payments-field-name')] = $(this).val();
      });

      $.post('{{ route('payments.store') }}', {
        invoice_id: $('#invoice_id').val(),
        amount: $('#payment_amount').val(),
        payment_method_id: $('#payment_method_id').val(),
        paid_at: $('#payment_date').val(),
        note: $('#payment_note').val(),
        custom: custom_fields,
        email_payment_receipt: $('#email_payment_receipt').prop('checked')
      }).done(function () {
        window.location = '{!! $redirectTo !!}';
      }).fail(function (response) {
        $btn.button('reset');
        showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
      });
    });

  });

</script>