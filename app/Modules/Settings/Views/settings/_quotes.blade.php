<div class="row">

    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('ip.default_quote_template') }}: </label>
            {!! Form::select('setting[quoteTemplate]', $quoteTemplates, config('fi.quoteTemplate'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('ip.default_group') }}: </label>
            {!! Form::select('setting[quoteGroup]', $groups, config('fi.quoteGroup'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('ip.quotes_expire_after') }}: </label>
            {!! Form::text('setting[quotesExpireAfter]', config('fi.quotesExpireAfter'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('ip.default_status_filter') }}: </label>
            {!! Form::select('setting[quoteStatusFilter]', $quoteStatuses, config('fi.quoteStatusFilter'), ['class' => 'form-control']) !!}
        </div>
    </div>

</div>

<div class="form-group">
    <label>{{ trans('ip.convert_quote_when_approved') }}: </label>
    {!! Form::select('setting[convertQuoteWhenApproved]', $yesNoArray, config('fi.convertQuoteWhenApproved'), ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    <label>{{ trans('ip.convert_quote_setting') }}: </label>
    {!! Form::select('setting[convertQuoteTerms]', $convertQuoteOptions, config('fi.convertQuoteTerms'), ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    <label>{{ trans('ip.default_terms') }}: </label>
    {!! Form::textarea('setting[quoteTerms]', config('fi.quoteTerms'), ['class' => 'form-control', 'rows' => 5]) !!}
</div>

<div class="form-group">
    <label>{{ trans('ip.default_footer') }}: </label>
    {!! Form::textarea('setting[quoteFooter]', config('fi.quoteFooter'), ['class' => 'form-control', 'rows' => 5]) !!}
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('ip.if_quote_is_emailed_while_draft') }}: </label>
            {!! Form::select('setting[resetQuoteDateEmailDraft]', $quoteWhenDraftOptions, config('fi.resetQuoteDateEmailDraft'), ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('ip.recalculate_quotes') }}: </label><br>
            <button type="button" class="btn btn-default" id="btn-recalculate-quotes"
                    data-loading-text="{{ trans('ip.recalculating_wait') }}">{{ trans('ip.recalculate') }}</button>
            <p class="help-block">{{ trans('ip.recalculate_help_text') }}</p>
        </div>
    </div>
</div>