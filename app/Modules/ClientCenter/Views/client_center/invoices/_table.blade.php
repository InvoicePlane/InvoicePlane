<table class="table table-hover">
    <thead>
    <tr>
        <th>@lang('ip.status')</th>
        <th>@lang('ip.invoice')</th>
        <th>@lang('ip.date')</th>
        <th>@lang('ip.due')</th>
        <th>@lang('ip.summary')</th>
        <th>@lang('ip.total')</th>
        <th>@lang('ip.balance')</th>
        <th>@lang('ip.options')</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($invoices as $invoice)
        <tr>
            <td>
                <span class="label label-{{ $invoiceStatuses[$invoice->invoice_status_id] }}">{{ trans('ip.' . $invoiceStatuses[$invoice->invoice_status_id]) }}</span>
                @if ($invoice->viewed)
                    <span class="label label-success">@lang('ip.viewed')</span>
                @else
                    <span class="label label-default">@lang('ip.not_viewed')</span>
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
                        @lang('ip.options') <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('clientCenter.public.invoice.pdf', [$invoice->url_key]) }}"
                               target="_blank"><i class="fa fa-print"></i> @lang('ip.pdf')</a></li>
                        <li><a href="{{ route('clientCenter.public.invoice.show', [$invoice->url_key]) }}"
                               target="_blank"><i class="fa fa-search"></i> @lang('ip.view')</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>