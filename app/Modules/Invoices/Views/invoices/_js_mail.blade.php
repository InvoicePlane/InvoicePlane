<script>

    $(function () {

        var attachPdf = 0;

        $('#modal-mail-invoice').modal({backdrop: 'static'}).on('shown.bs.modal', function () {
            $('#to').chosen();
            $('#cc').chosen();
            $('#bcc').chosen();
        });

        $('#btn-submit-mail-invoice').click(function () {

            var $btn = $(this).button('loading');

            if ($('#attach_pdf').prop('checked') == true) {
                attachPdf = 1;
            }

            $.post('{{ route('invoiceMail.store') }}', {
                invoice_id: {{ $invoiceId }},
                to: $('#to').val(),
                cc: $('#cc').val(),
                bcc: $('#bcc').val(),
                subject: $('#subject').val(),
                body: $('#body').val(),
                attach_pdf: attachPdf
            }).done(function (response) {
                $('#modal-status-placeholder').html('<div class="alert alert-success">' + '@lang('ip.sent')' + '</div>');
                setTimeout('window.location=\'' + decodeURIComponent('{{ $redirectTo }}') + '\'', 1000);
            }).fail(function (response) {
                $btn.button('reset');
                showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
            });
        });

    });

</script>
