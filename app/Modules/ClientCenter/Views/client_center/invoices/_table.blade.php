<table class="table table-hover">
    <thead>
    <tr>
        <th>{{ trans('fi.status') }}</th>
        <th>{{ trans('fi.invoice') }}</th>
        <th>{{ trans('fi.date') }}</th>
        <th>{{ trans('fi.due') }}</th>
        <th>{{ trans('fi.summary') }}</th>
        <th>{{ trans('fi.total') }}</th>
        <th>{{ trans('fi.balance') }}</th>
        <th>{{ trans('fi.options') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($invoices as $invoice)
        <tr>
            <td>
                <span class="label label-{{ $invoiceStatuses[$invoice->invoice_status_id] }}">{{ trans('fi.' . $invoiceStatuses[$invoice->invoice_status_id]) }}</span>
                @if ($invoice->viewed)
                    <span class="label label-success">{{ trans('fi.viewed') }}</span>
                @else
                    <span class="label label-default">{{ trans('fi.not_viewed') }}</span>
                @endif
            </td>
            <td>{{ $invoice->number }}</td>
            <td>{{ $invoice->formatted_created_at }}</td>
            <td>{{ $invoice->formatted_due_at }}</td>
            <td>{{ $invoice->summary }}</td>
            <td>{{ $invoice->amount->formatted_total }}</td>
            <td>{{ $invoice->amount->formatted_balance }}</td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                        {{ trans('fi.options') }} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('clientCenter.public.invoice.pdf', [$invoice->url_key]) }}" target="_blank"><i class="fa fa-print"></i> {{ trans('fi.pdf') }}</a></li>
                        <li><a href="{{ route('clientCenter.public.invoice.show', [$invoice->url_key]) }}" target="_blank"><i class="fa fa-search"></i> {{ trans('fi.view') }}</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>