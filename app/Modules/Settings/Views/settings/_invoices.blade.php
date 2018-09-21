<div class="row">

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.default_invoice_template'): </label>
            {!! Form::select('setting[invoiceTemplate]', $invoiceTemplates, config('ip.invoiceTemplate'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.default_group'): </label>
            {!! Form::select('setting[invoiceGroup]', $groups, config('ip.invoiceGroup'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.invoices_due_after'): </label>
            {!! Form::text('setting[invoicesDueAfter]', config('ip.invoicesDueAfter'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.default_status_filter'): </label>
            {!! Form::select('setting[invoiceStatusFilter]', $invoiceStatuses, config('ip.invoiceStatusFilter'), ['class' => 'form-control']) !!}
        </div>
    </div>

</div>

<div class="form-group">
    <label>@lang('ip.default_terms'): </label>
    {!! Form::textarea('setting[invoiceTerms]', config('ip.invoiceTerms'), ['class' => 'form-control', 'rows' => 5]) !!}
</div>

<div class="form-group">
    <label>@lang('ip.default_footer'): </label>
    {!! Form::textarea('setting[invoiceFooter]', config('ip.invoiceFooter'), ['class' => 'form-control', 'rows' => 5]) !!}
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.automatic_email_on_recur'): </label>
            {!! Form::select('setting[automaticEmailOnRecur]', ['0' => trans('ip.no'), '1' => trans('ip.yes')], config('ip.automaticEmailOnRecur'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.automatic_email_payment_receipts'): </label>
            {!! Form::select('setting[automaticEmailPaymentReceipts]', ['0' => trans('ip.no'), '1' => trans('ip.yes')], config('ip.automaticEmailPaymentReceipts'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.online_payment_method'): </label>
            {!! Form::select('setting[onlinePaymentMethod]', $paymentMethods, config('ip.onlinePaymentMethod'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.allow_payments_without_balance'): </label>
            {!! Form::select('setting[allowPaymentsWithoutBalance]', $yesNoArray, config('ip.allowPaymentsWithoutBalance'), ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.if_invoice_is_emailed_while_draft'): </label>
            {!! Form::select('setting[resetInvoiceDateEmailDraft]', $invoiceWhenDraftOptions, config('ip.resetInvoiceDateEmailDraft'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.recalculate_invoices'): </label><br>
            <button type="button" class="btn btn-default" id="btn-recalculate-invoices"
                data-loading-text="@lang('ip.recalculating_wait')">@lang('ip.recalculate')</button>
            <p class="help-block">@lang('ip.recalculate_help_text')</p>
        </div>
    </div>
</div>
