@extends('reports.layouts.master')

@section('content')

    <h1 style="margin-bottom: 0;">@lang('ip.expense_list')</h1>
    <h3 style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>

    <table class="alternate">
        <thead>
        <tr>
            <th style="width: 10%; text-align: left;">@lang('ip.date')</th>
            <th style="width: 25%; text-align: left;">@lang('ip.client')</th>
            <th style="width: 25%; text-align: left;">@lang('ip.category')</th>
            <th style="width: 25%; text-align: left;">@lang('ip.vendor')</th>
            <th style="width: 5%; text-align: left;">@lang('ip.billed')</th>
            <th class="amount" style="width: 10%;">@lang('ip.amount')</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($results['expenses'] as $expense)
            <tr>
                <td>{{ $expense['date'] }}</td>
                <td>{{ $expense['client'] }}</td>
                <td>{{ $expense['category'] }}</td>
                <td>{{ $expense['vendor'] }}</td>
                <td>{{ ($expense['billed']) ? trans('ip.yes') : trans('ip.no') }}</td>
                <td class="amount">{{ $expense['amount'] }}</td>
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