@include('recurring_invoices._js_edit_from')

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">@lang('ip.from')</h3>

        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" id="btn-change-company-profile">
                <i class="fa fa-exchange"></i> @lang('ip.change')
            </button>
        </div>
    </div>
    <div class="box-body">
        <strong>{{ $recurringInvoice->companyProfile->company }}</strong><br>
        {!! $recurringInvoice->companyProfile->formatted_address !!}<br>
        @lang('ip.phone'): {{ $recurringInvoice->companyProfile->phone }}<br>
        @lang('ip.email'): {{ $recurringInvoice->user->email }}
    </div>
</div>