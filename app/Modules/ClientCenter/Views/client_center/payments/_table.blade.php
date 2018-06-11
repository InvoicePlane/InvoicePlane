<table class="table table-hover">
    <thead>
    <tr>
        <th>@lang('ip.date')</th>
        <th>@lang('ip.invoice')</th>
        <th>@lang('ip.summary')</th>
        <th>@lang('ip.amount')</th>
        <th>@lang('ip.payment_method')</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($payments as $payment)
        <tr>
            <td>{{ $payment->formatted_paid_at }}</td>
            <td>{{ $payment->invoice->number }}</td>
            <td>{{ $payment->invoice->summary }}</td>
            <td>{{ $payment->formatted_amount }}</td>
            <td>{{ $payment->paymentMethod->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>