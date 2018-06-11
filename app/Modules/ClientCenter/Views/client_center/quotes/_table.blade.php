<table class="table table-hover">
    <thead>
    <tr>
        <th>@lang('ip.status')</th>
        <th>@lang('ip.quote')</th>
        <th>@lang('ip.date')</th>
        <th>@lang('ip.expires')</th>
        <th>@lang('ip.summary')</th>
        <th>@lang('ip.total')</th>
        <th>@lang('ip.options')</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($quotes as $quote)
        <tr>
            <td>
                <span class="label label-{{ $quoteStatuses[$quote->quote_status_id] }}">{{ trans('ip.' . $quoteStatuses[$quote->quote_status_id]) }}</span>
                @if ($quote->viewed)
                    <span class="label label-success">@lang('ip.viewed')</span>
                @else
                    <span class="label label-default">@lang('ip.not_viewed')</span>
                @endif
            </td>
            <td>{{ $quote->number }}</td>
            <td>{{ $quote->formatted_created_at }}</td>
            <td>{{ $quote->formatted_expires_at }}</td>
            <td>{{ $quote->summary }}</td>
            <td>{{ $quote->amount->formatted_total }}</td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                        @lang('ip.options') <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('clientCenter.public.quote.pdf', [$quote->url_key]) }}" target="_blank"><i
                                        class="fa fa-print"></i> @lang('ip.pdf')</a></li>
                        <li><a href="{{ route('clientCenter.public.quote.show', [$quote->url_key]) }}"
                               target="_blank"><i class="fa fa-search"></i> @lang('ip.view')</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>