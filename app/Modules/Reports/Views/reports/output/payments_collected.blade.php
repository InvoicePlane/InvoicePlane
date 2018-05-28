@extends('reports.layouts.master')

@section('content')

    <style>
        table {
            font-size: .9em;
        }
    </style>

    <h1 style="margin-bottom: 0;">{{ trans('fi.payments_collected') }}</h1>
    <h3 style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>

    <table class="alternate">
        <thead>
        <tr>
            <th>{{ trans('fi.date') }}</th>
            <th>{{ trans('fi.invoice') }}</th>
            <th>{{ trans('fi.client') }}</th>
            <th>{{ trans('fi.payment_method') }}</th>
            <th>{{ trans('fi.note') }}</th>
            <th class="amount">{{ trans('fi.amount') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($results['payments'] as $payment)
            <tr>
                <td>{{ $payment['date'] }}</td>
                <td>{{ $payment['invoice_number'] }}</td>
                <td>{{ $payment['client_name'] }}</td>
                <td>{{ $payment['payment_method'] }}</td>
                <td>{{ $payment['note'] }}</td>
                <td class="amount">{{ $payment['amount'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="amount"><strong>{{ trans('fi.total') }}</strong></td>
            <td class="amount"><strong>{{ $results['total'] }}</strong></td>
        </tr>
        </tbody>
    </table>

@stop