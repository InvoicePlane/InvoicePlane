<table class="table table-hover" style="height: 100%;">

    <thead>
    <tr>
        <th>
            <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
        </th>
        <th class="hidden-sm hidden-xs">@lang('ip.status')</th>
        <th>{!! Sortable::link('number', trans('ip.quote'), 'quotes') !!}</th>
        <th class="hidden-xs">{!! Sortable::link('quote_date', trans('ip.date'), 'quotes') !!}</th>
        <th class="hidden-sm hidden-xs">{!! Sortable::link('expires_at', trans('ip.expires'), 'quotes') !!}</th>
        <th>{!! Sortable::link('clients.name', trans('ip.client'), 'quotes') !!}</th>
        <th class="hidden-sm hidden-xs">{!! Sortable::link('summary', trans('ip.summary'), 'quotes') !!}</th>
        <th style="text-align: right; padding-right: 25px;">{!! Sortable::link('quote_amounts.total', trans('ip.total'), 'quotes') !!}</th>
        <th>@lang('ip.invoiced')</th>
        <th>@lang('ip.options')</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($quotes as $quote)
        <tr>
            <td><input type="checkbox" class="bulk-record" data-id="{{ $quote->id }}"></td>
            <td class="hidden-sm hidden-xs">
                <span class="label label-{{ $statuses[$quote->quote_status_id] }}">{{ trans('ip.' . $statuses[$quote->quote_status_id]) }}</span>
                @if ($quote->viewed)
                    <span class="label label-success">@lang('ip.viewed')</span>
                @else
                    <span class="label label-default">@lang('ip.not_viewed')</span>
                @endif
            </td>
            <td><a href="{{ route('quotes.edit', [$quote->id]) }}"
                   title="@lang('ip.edit')">{{ $quote->number }}</a></td>
            <td class="hidden-xs">{{ $quote->formatted_quote_date }}</td>
            <td class="hidden-sm hidden-xs">{{ $quote->formatted_expires_at }}</td>
            <td><a href="{{ route('clients.show', [$quote->client->id]) }}"
                   title="@lang('ip.view_client')">{{ $quote->client->unique_name }}</a></td>
            <td class="hidden-sm hidden-xs">{{ $quote->summary }}</td>
            <td style="text-align: right; padding-right: 25px;">{{ $quote->amount->formatted_total }}</td>
            <td class="hidden-xs">
                @if ($quote->invoice)
                    <a href="{{ route('invoices.edit', [$quote->invoice_id]) }}">@lang('ip.yes')</a>
                @else
                    @lang('ip.no')
                @endif
            </td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                        @lang('ip.options') <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('quotes.edit', [$quote->id]) }}"><i
                                        class="fa fa-edit"></i> @lang('ip.edit')</a></li>
                        <li><a href="{{ route('quotes.pdf', [$quote->id]) }}" target="_blank" id="btn-pdf-quote"><i
                                        class="fa fa-print"></i> @lang('ip.pdf')</a></li>
                        <li><a href="javascript:void(0)" class="email-quote" data-quote-id="{{ $quote->id }}"
                               data-redirect-to="{{ request()->fullUrl() }}"><i
                                        class="fa fa-envelope"></i> @lang('ip.email')</a></li>
                        <li><a href="{{ route('clientCenter.public.quote.show', [$quote->url_key]) }}" target="_blank"
                               id="btn-public-quote"><i class="fa fa-globe"></i> @lang('ip.public')</a></li>
                        <li><a href="{{ route('quotes.delete', [$quote->id]) }}"
                               onclick="return confirm('@lang('ip.delete_record_warning')');"><i
                                        class="fa fa-trash-o"></i> @lang('ip.delete')</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>

</table>