@section('javascript')
    @parent
    <script>
        $().ready(function () {
            $('#btn-check-update').click(function () {
                $.get("{{ route('settings.updateCheck') }}")
                    .done(function (response) {
                        alert(response.message);
                    })
                    .fail(function (response) {
                        alert("@lang('ip.unknown_error')");
                    });
            });
        });
    </script>
@stop

<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('ip.header_title_text'): </label>
            {!! Form::text('setting[headerTitleText]', config('ip.headerTitleText'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('ip.default_company_profile'): </label>
            {!! Form::select('setting[defaultCompanyProfile]', $companyProfiles, config('ip.defaultCompanyProfile'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('ip.version'): </label>

            <div class="input-group">
                {!! Form::text('version', config('ip.version'), ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                <span class="input-group-btn">
					<button class="btn btn-default" id="btn-check-update"
                        type="button">@lang('ip.check_for_update')</button>
				</span>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-2">
        <div class="form-group">
            <label>@lang('ip.skin'): </label>
            {!! Form::select('setting[skin]', $skins, config('ip.skin'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label>@lang('ip.language'): </label>
            {!! Form::select('setting[language]', $languages, config('ip.language'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label>@lang('ip.date_format'): </label>
            {!! Form::select('setting[dateFormat]', $dateFormats, config('ip.dateFormat'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('ip.use_24_hour_time_format') }}: </label>
            {!! Form::select('setting[use24HourTimeFormat]', $yesNoArray, config('ip.use24HourTimeFormat'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>@lang('ip.timezone'): </label>
            {!! Form::select('setting[timezone]', $timezones, config('ip.timezone'), ['class' => 'form-control']) !!}
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6">

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('ip.display_client_unique_name'): </label>
                    {!! Form::select('setting[displayClientUniqueName]', $clientUniqueNameOptions, config('ip.displayClientUniqueName'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('ip.quantity_price_decimals'): </label>
                            {!! Form::select('setting[amountDecimals]', $amountDecimalOptions, config('ip.amountDecimals'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('ip.round_tax_decimals'): </label>
                            {!! Form::select('setting[roundTaxDecimals]', $roundTaxDecimalOptions, config('ip.roundTaxDecimals'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('ip.address_format'): </label>
            {!! Form::textarea('setting[addressFormat]', config('ip.addressFormat'), ['class' => 'form-control', 'rows' => 5]) !!}
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('ip.base_currency'): </label>
                    {!! Form::select('setting[baseCurrency]', $currencies, config('ip.baseCurrency'), ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('ip.exchange_rate_mode'): </label>
                    {!! Form::select('setting[exchangeRateMode]', $exchangeRateModes, config('ip.exchangeRateMode'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('ip.results_per_page'):</label>
                    {!! Form::select('setting[resultsPerPage]', $resultsPerPage, config('ip.resultsPerPage'), ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('ip.force_https'):</label>
                    {!! Form::select('setting[forceHttps]', $yesNoArray, config('ip.forceHttps'), ['class' => 'form-control']) !!}
                    <p class="help-block">@lang('ip.force_https_help')</p>
                </div>
            </div>
        </div>

    </div>
</div>
