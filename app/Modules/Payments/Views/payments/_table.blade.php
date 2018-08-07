<table class="table table-hover">

    <thead>
    <tr>
        <th>
            <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
        </th>
        <th>{!! Sortable::link('paid_at', trans('ip.payment_date'), 'payments') !!}</th>
        <th>{!! Sortable::link('invoices.number', trans('ip.invoice'), 'payments') !!}</th>
        <th>{!! Sortable::link('invoices.invoice_date', trans('ip.date'), 'payments') !!}</th>
        <th>{!! Sortable::link('clients.name', trans('ip.client'), 'payments') !!}</th>
        <th>{!! Sortable::link('invoices.summary', trans('ip.summary'), 'payments') !!}</th>
        <th>{!! Sortable::link('amount', trans('ip.amount'), 'payments') !!}</th>
        <th>{!! Sortable::link('payment_methods.name', trans('ip.payment_method'), 'payments') !!}</th>
        <th>{!! Sortable::link('note', trans('ip.note'), 'payments') !!}</th>
        <th>@lang('ip.options')</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($payments as $payment)
        <tr>
            <td><input type="checkbox" class="bulk-record" data-id="{{ $payment->id }}"></td>
            <td>{{ $payment->formatted_paid_at }}</td>
            <td><a href="{{ route('invoices.edit', [$payment->invoice_id]) }}">{{ $payment->invoice->number }}</a></td>
            <td>{{ $payment->invoice->formatted_created_at }}</td>
            <td>
                <a href="{{ route('clients.show', [$payment->invoice->client_id]) }}">{{ $payment->invoice->client->name }}</a>
            </td>
            <td>{{ $payment->invoice->summary }}</td>
            <td>{{ $payment->formatted_amount }}</td>
            <td>@if ($payment->paymentMethod) {{ $payment->paymentMethod->name }} @endif</td>
            <td>{{ $payment->note }}</td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                        @lang('ip.options') <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('payments.edit', [$payment->id]) }}"><i
                                        class="fa fa-edit"></i> @lang('ip.edit')</a></li>
                        <li><a href="{{ route('invoices.pdf', [$payment->invoice->id]) }}" target="_blank"
                               id="btn-pdf-invoice"><i class="fa fa-print"></i> @lang('ip.invoice')</a></li>
                        @if (config('ip.mailConfigured'))
                            <li><a href="javascript:void(0)" class="email-payment-receipt"
                                   data-payment-id="{{ $payment->id }}" data-redirect-to="{{ request()->fullUrl() }}"><i
                                            class="fa fa-envelope"></i> @lang('ip.email_payment_receipt')</a></li>
                        @endif
                        <li><a href="{{ route('payments.delete', [$payment->id]) }}"
                               onclick="return confirm('@lang('ip.delete_record_warning')');"><i
                                        class="fa fa-trash-o"></i> @lang('ip.delete')</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>

</table>