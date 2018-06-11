<table class="table table-hover">
    <thead>
    <tr>
        <th>{{ trans('ip.date') }}</th>
        <th>{{ trans('ip.invoice') }}</th>
        <th>{{ trans('ip.summary') }}</th>
        <th>{{ trans('ip.amount') }}</th>
        <th>{{ trans('ip.payment_method') }}</th>
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