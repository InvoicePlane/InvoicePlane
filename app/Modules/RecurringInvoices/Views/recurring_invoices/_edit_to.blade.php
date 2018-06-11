@include('recurring_invoices._js_edit_to')

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">@lang('ip.to')</h3>

        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" id="btn-change-client"><i
                        class="fa fa-exchange"></i> @lang('ip.change')</button>
            <button class="btn btn-default btn-sm" id="btn-edit-client"
                    data-client-id="{{ $recurringInvoice->client->id }}"><i
                        class="fa fa-pencil"></i> @lang('ip.edit')</button>
        </div>
    </div>
    <div class="box-body">
        <strong>{{ $recurringInvoice->client->name }}</strong><br>
        {!! $recurringInvoice->client->formatted_address !!}<br>
        @lang('ip.phone'): {{ $recurringInvoice->client->phone }}<br>
        @lang('ip.email'): {{ $recurringInvoice->client->email }}
    </div>
</div>