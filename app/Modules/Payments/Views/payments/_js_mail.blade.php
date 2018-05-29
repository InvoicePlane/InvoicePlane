<script type="text/javascript">

  $(function () {

    attachPdf = 0;

    $('#modal-mail-payment').modal({backdrop: 'static'}).on('shown.bs.modal', function () {
      $('#to').chosen();
      $('#cc').chosen();
      $('#bcc').chosen();
    });

    $('#btn-submit-mail-payment').click(function () {

      var $btn = $(this).button('loading');

      if ($('#attach_pdf').prop('checked') == true) {
        attachPdf = 1;
      }

      $.post('{{ route('paymentMail.store') }}', {
        payment_id: {{ $paymentId }},
        to: $('#to').val(),
        cc: $('#cc').val(),
        bcc: $('#bcc').val(),
        subject: $('#subject').val(),
        body: $('#body').val(),
        attach_pdf: attachPdf
      }).done(function (response) {
        $('#modal-status-placeholder').html('<div class="alert alert-success">' + '{{ trans('fi.sent') }}' + '</div>');
        setTimeout('window.location=\'{{ $redirectTo }}\'', 1000);
      }).fail(function (response) {
        $btn.button('reset');
        showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
      });
    });
  });

</script>