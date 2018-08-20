@section('javascript')
    @parent
    <script>
        $(function () {

            $('#mailPassword').val('');

            updateEmailOptions();

            $('#mailDriver').change(function () {
                updateEmailOptions();
            });

            function updateEmailOptions () {

                $('.email-option').hide();

                mailDriver = $('#mailDriver').val();

                if (mailDriver == 'smtp') {
                    $('.smtp-option').show();
                }
                else if (mailDriver == 'sendmail') {
                    $('.sendmail-option').show();
                }
                else if (mailDriver == 'mail') {
                    $('.phpmail-option').show();
                }
            }

        });
    </script>
@stop

<div class="form-group">
    <label>@lang('ip.email_send_method'): </label>
    {!! Form::select('setting[mailDriver]', $emailSendMethods, config('ip.mailDriver'), ['id' => 'mailDriver', 'class' => 'form-control']) !!}
</div>

<div class="row smtp-option email-option">
    <div class="col-md-9">
        <div class="form-group smtp-option email-option">
            <label>@lang('ip.smtp_host_address'): </label>
            {!! Form::text('setting[mailHost]', config('ip.mailHost'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>@lang('ip.smtp_host_port'): </label>
            {!! Form::text('setting[mailPort]', config('ip.mailPort'), ['class' => 'form-control']) !!}
        </div>
    </div>
</div>
<div class="row smtp-option email-option">
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>@lang('ip.smtp_username'): </label>
            {!! Form::text('setting[mailUsername]', config('ip.mailUsername'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>@lang('ip.smtp_password'): </label>
            {!! Form::password('setting[mailPassword]', ['id' => 'mailPassword', 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>@lang('ip.smtp_encryption'): </label>
            {!! Form::select('setting[mailEncryption]', $emailEncryptions, config('ip.mailEncryption'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>@lang('ip.allow_self_signed_cert'): </label>
            {!! Form::select('setting[mailAllowSelfSignedCertificate]', $yesNoArray, config('ip.mailAllowSelfSignedCertificate'), ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group sendmail-option email-option">
    <div class="form-group">
        <label>@lang('ip.sendmail_path'): </label>
        {!! Form::text('setting[mailSendmail]', config('ip.mailSendmail'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row smtp-option sendmail-option phpmail-option email-option">
    <div class="col-md-3">
        <div class="form-group smtp-option sendmail-option phpmail-option email-option">
            <label>@lang('ip.always_attach_pdf'): </label>
            {!! Form::select('setting[attachPdf]', $yesNoArray, config('ip.attachPdf'), ['id' => 'attachPdf', 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option sendmail-option phpmail-option email-option">
            <label>@lang('ip.reply_to_address'): </label>
            {!! Form::text('setting[mailReplyToAddress]', config('ip.mailReplyToAddress'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option sendmail-option phpmail-option email-option">
            <label>@lang('ip.always_cc'): </label>
            {!! Form::text('setting[mailDefaultCc]', config('ip.mailDefaultCc'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option sendmail-option phpmail-option email-option">
            <label>@lang('ip.always_bcc'): </label>
            {!! Form::text('setting[mailDefaultBcc]', config('ip.mailDefaultBcc'), ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.quote_email_subject'): </label>
            {!! Form::text('setting[quoteEmailSubject]', config('ip.quoteEmailSubject'), ['class' => 'form-control']) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#quote-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.invoice_email_subject'): </label>
            {!! Form::text('setting[invoiceEmailSubject]', config('ip.invoiceEmailSubject'), ['class' => 'form-control']) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#invoice-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.default_quote_email_body'): </label>
            {!! Form::textarea('setting[quoteEmailBody]', config('ip.quoteEmailBody'), ['class' => 'form-control', 'rows' => 5]) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#quote-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.default_invoice_email_body'): </label>
            {!! Form::textarea('setting[invoiceEmailBody]', config('ip.invoiceEmailBody'), ['class' => 'form-control', 'rows' => 5]) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#invoice-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.overdue_email_subject'): </label>
            {!! Form::text('setting[overdueInvoiceEmailSubject]', config('ip.overdueInvoiceEmailSubject'), ['class' => 'form-control']) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#invoice-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.upcoming_payment_notice_email_subject'): </label>
            {!! Form::text('setting[upcomingPaymentNoticeEmailSubject]', config('ip.upcomingPaymentNoticeEmailSubject'), ['class' => 'form-control']) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#invoice-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.default_overdue_invoice_email_body'): </label>
            {!! Form::textarea('setting[overdueInvoiceEmailBody]', config('ip.overdueInvoiceEmailBody'), ['class' => 'form-control', 'rows' => 5]) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#invoice-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.upcoming_payment_notice_email_body'): </label>
            {!! Form::textarea('setting[upcomingPaymentNoticeEmailBody]', config('ip.upcomingPaymentNoticeEmailBody'), ['class' => 'form-control', 'rows' => 5]) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#invoice-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.overdue_invoice_reminder_frequency'): </label>
            {!! Form::text('setting[overdueInvoiceReminderFrequency]', config('ip.overdueInvoiceReminderFrequency'), ['class' => 'form-control']) !!}
            <span class="help-block">@lang('ip.overdue_invoice_reminder_frequency_help')</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.upcoming_payment_notice_frequency'): </label>
            {!! Form::text('setting[upcomingPaymentNoticeFrequency]', config('ip.upcomingPaymentNoticeFrequency'), ['class' => 'form-control']) !!}
            <span class="help-block">@lang('ip.upcoming_payment_notice_frequency_help')</span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.quote_approved_email_body'): </label>
            {!! Form::textarea('setting[quoteApprovedEmailBody]', config('ip.quoteApprovedEmailBody'), ['class' => 'form-control', 'rows' => 5]) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#quote-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.quote_rejected_email_body'): </label>
            {!! Form::textarea('setting[quoteRejectedEmailBody]', config('ip.quoteRejectedEmailBody'), ['class' => 'form-control', 'rows' => 5]) !!}
            <span class="help-block"><a
                    href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#quote-email-template"
                    target="_blank">@lang('ip.available_fields')</a></span>
        </div>
    </div>
</div>

<div class="form-group">
    <label>@lang('ip.payment_receipt_email_subject'): </label>
    {!! Form::text('setting[paymentReceiptEmailSubject]', config('ip.paymentReceiptEmailSubject'), ['class' => 'form-control']) !!}
    <span class="help-block"><a
            href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#payment-receipt-email-template"
            target="_blank">@lang('ip.available_fields')</a></span>
</div>

<div class="form-group">
    <label>@lang('ip.default_payment_receipt_body'): </label>
    {!! Form::textarea('setting[paymentReceiptBody]', config('ip.paymentReceiptBody'), ['class' => 'form-control', 'rows' => 5]) !!}
    <span class="help-block"><a
            href="https://wiki.invoiceplane.com/en/2.0/customization/email-templates#payment-receipt-email-template"
            target="_blank">@lang('ip.available_fields')</a></span>
</div>
