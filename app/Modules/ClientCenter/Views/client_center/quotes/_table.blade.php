<table class="table table-hover">
    <thead>
    <tr>
        <th>{{ trans('ip.status') }}</th>
        <th>{{ trans('ip.quote') }}</th>
        <th>{{ trans('ip.date') }}</th>
        <th>{{ trans('ip.expires') }}</th>
        <th>{{ trans('ip.summary') }}</th>
        <th>{{ trans('ip.total') }}</th>
        <th>{{ trans('ip.options') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($quotes as $quote)
        <tr>
            <td>
                <span class="label label-{{ $quoteStatuses[$quote->quote_status_id] }}">{{ trans('ip.' . $quoteStatuses[$quote->quote_status_id]) }}</span>
                @if ($quote->viewed)
                    <span class="label label-success">{{ trans('ip.viewed') }}</span>
                @else
                    <span class="label label-default">{{ trans('ip.not_viewed') }}</span>
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
                        {{ trans('ip.options') }} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('clientCenter.public.quote.pdf', [$quote->url_key]) }}" target="_blank"><i
                                        class="fa fa-print"></i> {{ trans('ip.pdf') }}</a></li>
                        <li><a href="{{ route('clientCenter.public.quote.show', [$quote->url_key]) }}"
                               target="_blank"><i class="fa fa-search"></i> {{ trans('ip.view') }}</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>