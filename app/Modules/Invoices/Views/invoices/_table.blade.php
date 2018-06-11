<table class="table table-hover">

    <thead>
    <tr>
        <th>
            <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
        </th>
        <th class="hidden-sm hidden-xs">@lang('ip.status')</th>
        <th>{!! Sortable::link('number', trans('ip.invoice'), 'invoices') !!}</th>
        <th class="hidden-xs">{!! Sortable::link('invoice_date', trans('ip.date'), 'invoices') !!}</th>
        <th class="hidden-md hidden-sm hidden-xs">{!! Sortable::link('due_at', trans('ip.due'), 'invoices') !!}</th>
        <th>{!! Sortable::link('clients.name', trans('ip.client'), 'invoices') !!}</th>
        <th class="hidden-sm hidden-xs">{!! Sortable::link('summary', trans('ip.summary'), 'invoices') !!}</th>
        <th style="text-align: right; padding-right: 25px;">{!! Sortable::link('invoice_amounts.total', trans('ip.total'), 'invoices') !!}</th>
        <th class="hidden-sm hidden-xs"
            style="text-align: right; padding-right: 25px;">{!! Sortable::link('invoice_amounts.balance', trans('ip.balance'), 'invoices') !!}</th>
        <th>@lang('ip.options')</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($invoices as $invoice)
        <tr>
            <td><input type="checkbox" class="bulk-record" data-id="{{ $invoice->id }}"></td>
            <td class="hidden-sm hidden-xs">
                <span class="label label-{{ $statuses[$invoice->invoice_status_id] }}">{{ trans('ip.' . $statuses[$invoice->invoice_status_id]) }}</span>
                @if ($invoice->viewed)
                    <span class="label label-success">@lang('ip.viewed')</span>
                @else
                    <span class="label label-default">@lang('ip.not_viewed')</span>
                @endif
            </td>
            <td><a href="{{ route('invoices.edit', [$invoice->id]) }}"
                   title="@lang('ip.edit')">{{ $invoice->number }}</a></td>
            <td class="hidden-xs">{{ $invoice->formatted_invoice_date }}</td>
            <td class="hidden-md hidden-sm hidden-xs"
                @if ($invoice->isOverdue) style="color: red; font-weight: bold;" @endif>{{ $invoice->formatted_due_at }}</td>
            <td><a href="{{ route('clients.show', [$invoice->client->id]) }}"
                   title="@lang('ip.view_client')">{{ $invoice->client->unique_name }}</a></td>
            <td class="hidden-sm hidden-xs">{{ $invoice->summary }}</td>
            <td style="text-align: right; padding-right: 25px;">{{ $invoice->amount->formatted_total }}</td>
            <td class="hidden-sm hidden-xs"
                style="text-align: right; padding-right: 25px;">{{ $invoice->amount->formatted_balance }}</td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                        @lang('ip.options') <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('invoices.edit', [$invoice->id]) }}"><i
                                        class="fa fa-edit"></i> @lang('ip.edit')</a></li>
                        <li><a href="{{ route('invoices.pdf', [$invoice->id]) }}" target="_blank"
                               id="btn-pdf-invoice"><i class="fa fa-print"></i> @lang('ip.pdf')</a></li>
                        <li><a href="javascript:void(0)" class="email-invoice" data-invoice-id="{{ $invoice->id }}"
                               data-redirect-to="{{ request()->fullUrl() }}"><i
                                        class="fa fa-envelope"></i> @lang('ip.email')</a></li>
                        <li><a href="{{ route('clientCenter.public.invoice.show', [$invoice->url_key]) }}"
                               target="_blank" id="btn-public-invoice"><i
                                        class="fa fa-globe"></i> @lang('ip.public')</a></li>
                        @if ($invoice->isPayable or config('fi.allowPaymentsWithoutBalance'))
                            <li><a href="javascript:void(0)" id="btn-enter-payment" class="enter-payment"
                                   data-invoice-id="{{ $invoice->id }}"
                                   data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                                   data-redirect-to="{{ request()->fullUrl() }}"><i
                                            class="fa fa-credit-card"></i> @lang('ip.enter_payment')</a></li>
                        @endif
                        <li><a href="{{ route('invoices.delete', [$invoice->id]) }}"
                               onclick="return confirm('@lang('ip.delete_record_warning')');"><i
                                        class="fa fa-trash-o"></i> @lang('ip.delete')</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>

</table>