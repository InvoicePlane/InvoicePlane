@extends('reports.layouts.master')

@section('content')

    <h1 style="margin-bottom: 0;">{{ trans('fi.client_statement') }}</h1>
    <h3 style="margin-top: 0; margin-bottom: 0;">{{ $results['client_name'] }}</h3>
    <h3 style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>
    <br>
    <table class="alternate">
        <thead>
        <tr>
            <th>{{ trans('fi.date') }}</th>
            <th>{{ trans('fi.invoice') }}</th>
            <th>{{ trans('fi.summary') }}</th>
            <th class="amount">{{ trans('fi.subtotal') }}</th>
            <th class="amount">{{ trans('fi.discount') }}</th>
            <th class="amount">{{ trans('fi.tax') }}</th>
            <th class="amount">{{ trans('fi.total') }}</th>
            <th class="amount">{{ trans('fi.paid') }}</th>
            <th class="amount">{{ trans('fi.balance') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($results['records'] as $result)
            <tr>
                <td>{{ $result['formatted_invoice_date'] }}</td>
                <td>{{ $result['number'] }}</td>
                <td>{{ $result['summary'] }}</td>
                <td class="amount">{{ $result['formatted_subtotal'] }}</td>
                <td class="amount">{{ $result['formatted_discount'] }}</td>
                <td class="amount">{{ $result['formatted_tax'] }}</td>
                <td class="amount">{{ $result['formatted_total'] }}</td>
                <td class="amount">{{ $result['formatted_paid'] }}</td>
                <td class="amount">{{ $result['formatted_balance'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3"></td>
            <td class="amount" style="font-weight: bold;">{{ $results['subtotal'] }}</td>
            <td class="amount" style="font-weight: bold;">{{ $results['discount'] }}</td>
            <td class="amount" style="font-weight: bold;">{{ $results['tax'] }}</td>
            <td class="amount" style="font-weight: bold;">{{ $results['total'] }}</td>
            <td class="amount" style="font-weight: bold;">{{ $results['paid'] }}</td>
            <td class="amount" style="font-weight: bold;">{{ $results['balance'] }}</td>
        </tr>
        </tbody>
    </table>

@stop