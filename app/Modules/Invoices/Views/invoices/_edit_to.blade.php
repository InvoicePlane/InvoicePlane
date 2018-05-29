@include('invoices._js_edit_to')

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">{{ trans('fi.to') }}</h3>

        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" id="btn-change-client"><i
                        class="fa fa-exchange"></i> {{ trans('fi.change') }}</button>
            <button class="btn btn-default btn-sm" id="btn-edit-client" data-client-id="{{ $invoice->client->id }}"><i
                        class="fa fa-pencil"></i> {{ trans('fi.edit') }}</button>
        </div>
    </div>
    <div class="box-body">
        <strong>{{ $invoice->client->name }}</strong><br>
        {!! $invoice->client->formatted_address !!}<br>
        {{ trans('fi.phone') }}: {{ $invoice->client->phone }}<br>
        {{ trans('fi.email') }}: {{ $invoice->client->email }}
    </div>
</div>