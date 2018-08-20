<div class="row">

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.default_quote_template'): </label>
            {!! Form::select('setting[quoteTemplate]', $quoteTemplates, config('ip.quoteTemplate'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.default_group'): </label>
            {!! Form::select('setting[quoteGroup]', $groups, config('ip.quoteGroup'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.quotes_expire_after'): </label>
            {!! Form::text('setting[quotesExpireAfter]', config('ip.quotesExpireAfter'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.default_status_filter'): </label>
            {!! Form::select('setting[quoteStatusFilter]', $quoteStatuses, config('ip.quoteStatusFilter'), ['class' => 'form-control']) !!}
        </div>
    </div>

</div>

<div class="form-group">
    <label>@lang('ip.convert_quote_when_approved'): </label>
    {!! Form::select('setting[convertQuoteWhenApproved]', $yesNoArray, config('ip.convertQuoteWhenApproved'), ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    <label>@lang('ip.convert_quote_setting'): </label>
    {!! Form::select('setting[convertQuoteTerms]', $convertQuoteOptions, config('ip.convertQuoteTerms'), ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    <label>@lang('ip.default_terms'): </label>
    {!! Form::textarea('setting[quoteTerms]', config('ip.quoteTerms'), ['class' => 'form-control', 'rows' => 5]) !!}
</div>

<div class="form-group">
    <label>@lang('ip.default_footer'): </label>
    {!! Form::textarea('setting[quoteFooter]', config('ip.quoteFooter'), ['class' => 'form-control', 'rows' => 5]) !!}
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.if_quote_is_emailed_while_draft'): </label>
            {!! Form::select('setting[resetQuoteDateEmailDraft]', $quoteWhenDraftOptions, config('ip.resetQuoteDateEmailDraft'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.recalculate_quotes'): </label><br>
            <button type="button" class="btn btn-default" id="btn-recalculate-quotes"
                data-loading-text="@lang('ip.recalculating_wait')">@lang('ip.recalculate')</button>
            <p class="help-block">@lang('ip.recalculate_help_text')</p>
        </div>
    </div>
</div>
