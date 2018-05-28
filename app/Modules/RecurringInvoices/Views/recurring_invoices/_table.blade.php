<table class="table table-hover">

    <thead>
    <tr>
        <th>{!! Sortable::link('id', trans('fi.id'), 'recurring_invoices') !!}</th>
        <th>{!! Sortable::link('clients.name', trans('fi.client'), 'recurring_invoices') !!}</th>
        <th class="hidden-sm hidden-xs">{!! Sortable::link('summary', trans('fi.summary'), 'recurring_invoices') !!}</th>
        <th>{!! Sortable::link('next_date', trans('fi.next_date'), 'recurring_invoices') !!}</th>
        <th>{!! Sortable::link('stop_date', trans('fi.stop_date'), 'recurring_invoices') !!}</th>
        <th>{{ trans('fi.every') }}</th>
        <th style="text-align: right; padding-right: 25px;">{!! Sortable::link('recurring_invoice_amounts.total', trans('fi.total'), 'recurring_invoices') !!}</th>
        <th>{{ trans('fi.options') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($recurringInvoices as $recurringInvoice)
        <tr>
            <td>
                <a href="{{ route('recurringInvoices.edit', [$recurringInvoice->id]) }}" title="{{ trans('fi.edit') }}">{{ $recurringInvoice->id }}</a>
            </td>
            <td>
                <a href="{{ route('clients.show', [$recurringInvoice->client->id]) }}" title="{{ trans('fi.view_client') }}">{{ $recurringInvoice->client->unique_name }}</a>
            </td>
            <td class="hidden-sm hidden-xs">{{ $recurringInvoice->summary }}</td>
            <td>{{ $recurringInvoice->formatted_next_date }}</td>
            <td>{{ $recurringInvoice->formatted_stop_date }}</td>
            <td>{{ $recurringInvoice->recurring_frequency . ' ' . $frequencies[$recurringInvoice->recurring_period] }}</td>
            <td style="text-align: right; padding-right: 25px;">{{ $recurringInvoice->amount->formatted_total }}</td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                        {{ trans('fi.options') }} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('recurringInvoices.edit', [$recurringInvoice->id]) }}"><i
                                    class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                        <li><a href="{{ route('recurringInvoices.delete', [$recurringInvoice->id]) }}"
                               onclick="return confirm('{{ trans('fi.delete_record_warning') }}');"><i
                                    class="fa fa-trash-o"></i> {{ trans('fi.delete') }}</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>

</table>