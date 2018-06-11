@include('quotes._js_edit_from')

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">{{ trans('ip.from') }}</h3>

        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" id="btn-change-company_profile">
                <i class="fa fa-exchange"></i> {{ trans('ip.change') }}
            </button>
        </div>
    </div>
    <div class="box-body">
        <strong>{{ $quote->companyProfile->company }}</strong><br>
        {!! $quote->companyProfile->formatted_address !!}<br>
        {{ trans('ip.phone') }}: {{ $quote->companyProfile->phone }}<br>
        {{ trans('ip.email') }}: {{ $quote->user->email }}
    </div>
</div>