@extends('reports.layouts.master')

@section('content')

    <style>
        table {
            font-size: .9em;
        }
    </style>

    <h1 style="margin-bottom: 0;">@lang('ip.payments_collected')</h1>
    <h3 style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>

    <table class="alternate">
        <thead>
        <tr>
            <th>@lang('ip.date')</th>
            <th>@lang('ip.invoice')</th>
            <th>@lang('ip.client')</th>
            <th>@lang('ip.payment_method')</th>
            <th>@lang('ip.note')</th>
            <th class="amount">@lang('ip.amount')</th>
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
            <td class="amount"><strong>@lang('ip.total')</strong></td>
            <td class="amount"><strong>{{ $results['total'] }}</strong></td>
        </tr>
        </tbody>
    </table>

@stop