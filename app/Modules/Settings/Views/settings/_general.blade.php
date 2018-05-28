@section('javascript')
    @parent
    <script type="text/javascript">
        $().ready(function () {
            $('#btn-check-update').click(function () {
                $.get("{{ route('settings.updateCheck') }}")
                    .done(function (response) {
                        alert(response.message);
                    })
                    .fail(function (response) {
                        alert("{{ trans('fi.unknown_error') }}");
                    });
            });
        });
    </script>
@stop

<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ trans('fi.header_title_text') }}: </label>
            {!! Form::text('setting[headerTitleText]', config('fi.headerTitleText'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ trans('fi.default_company_profile') }}: </label>
            {!! Form::select('setting[defaultCompanyProfile]', $companyProfiles, config('fi.defaultCompanyProfile'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ trans('fi.version') }}: </label>

            <div class="input-group">
                {!! Form::text('version', config('fi.version'), ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                <span class="input-group-btn">
					<button class="btn btn-default" id="btn-check-update"
                            type="button">{{ trans('fi.check_for_update') }}</button>
				</span>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-2">
        <div class="form-group">
            <label>{{ trans('fi.skin') }}: </label>
            {!! Form::select('setting[skin]', $skins, config('fi.skin'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label>{{ trans('fi.language') }}: </label>
            {!! Form::select('setting[language]', $languages, config('fi.language'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label>{{ trans('fi.date_format') }}: </label>
            {!! Form::select('setting[dateFormat]', $dateFormats, config('fi.dateFormat'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('fi.use_24_hour_time_format') }}: </label>
            {!! Form::select('setting[use24HourTimeFormat]', $yesNoArray, config('fi.use24HourTimeFormat'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('fi.timezone') }}: </label>
            {!! Form::select('setting[timezone]', $timezones, config('fi.timezone'), ['class' => 'form-control']) !!}
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6">

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ trans('fi.display_client_unique_name') }}: </label>
                    {!! Form::select('setting[displayClientUniqueName]', $clientUniqueNameOptions, config('fi.displayClientUniqueName'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ trans('fi.quantity_price_decimals') }}: </label>
                            {!! Form::select('setting[amountDecimals]', $amountDecimalOptions, config('fi.amountDecimals'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ trans('fi.round_tax_decimals') }}: </label>
                            {!! Form::select('setting[roundTaxDecimals]', $roundTaxDecimalOptions, config('fi.roundTaxDecimals'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ trans('fi.address_format') }}: </label>
            {!! Form::textarea('setting[addressFormat]', config('fi.addressFormat'), ['class' => 'form-control', 'rows' => 5]) !!}
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ trans('fi.base_currency') }}: </label>
                    {!! Form::select('setting[baseCurrency]', $currencies, config('fi.baseCurrency'), ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ trans('fi.exchange_rate_mode') }}: </label>
                    {!! Form::select('setting[exchangeRateMode]', $exchangeRateModes, config('fi.exchangeRateMode'), ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ trans('fi.results_per_page') }}:</label>
                    {!! Form::select('setting[resultsPerPage]', $resultsPerPage, config('fi.resultsPerPage'), ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ trans('fi.force_https') }}:</label>
                    {!! Form::select('setting[forceHttps]', $yesNoArray, config('fi.forceHttps'), ['class' => 'form-control']) !!}
                    <p class="help-block">{{ trans('fi.force_https_help') }}</p>
                </div>
            </div>
        </div>

    </div>
</div>