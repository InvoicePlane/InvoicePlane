<div class="form-group">
    <label>@lang('ip.default_item_tax_rate'): </label>
    {!! Form::select('setting[itemTaxRate]', $taxRates, config('ip.itemTaxRate'), ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    <label>{{ trans('ip.default_item_tax_2_rate') }}: </label>
    {!! Form::select('setting[itemTax2Rate]', $taxRates, config('ip.itemTax2Rate'), ['class' => 'form-control']) !!}
</div>