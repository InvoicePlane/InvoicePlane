<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('ip.quote') #{{ $quote->number }}</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            color: #001028;
            background: #FFFFFF;
            font-family: DejaVu Sans, Helvetica, sans-serif;
            font-size: 12px;
            margin-bottom: 50px;
        }

        a {
            color: #5D6975;
            border-bottom: 1px solid currentColor;
            text-decoration: none;
        }

        h1 {
            color: #5D6975;
            font-size: 2.8em;
            line-height: 1.4em;
            font-weight: bold;
            margin: 0;
        }

        table {
            width: 100%;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        th, .section-header {
            padding: 5px 10px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
            text-align: center;
        }

        td {
            padding: 10px;
        }

        table.alternate tr:nth-child(odd) td {
            background: #F5F5F5;
        }

        th.amount, td.amount {
            text-align: right;
        }

        .info {
            color: #5D6975;
            font-weight: bold;
        }

        .terms {
            padding: 10px;
        }

        .footer {
            position: fixed;
            height: 50px;
            width: 100%;
            bottom: 0;
            text-align: center;
        }

    </style>
</head>
<body>

<table>
    <tr>
        <td style="width: 50%;" valign="top">
            <h1>{{ mb_strtoupper(trans('ip.quote')) }}</h1>
            <span class="info">{{ mb_strtoupper(trans('ip.quote')) }} #</span>{{ $quote->number }}<br>
            <span class="info">{{ mb_strtoupper(trans('ip.issued')) }}</span> {{ $quote->formatted_created_at }}<br>
            <span class="info">{{ mb_strtoupper(trans('ip.expires')) }}</span> {{ $quote->formatted_expires_at }}
            <br><br>
            <span class="info">{{ mb_strtoupper(trans('ip.bill_to')) }}</span><br>{{ $quote->client->name }}<br>
            @if ($quote->client->address) {!! $quote->client->formatted_address !!}<br>@endif
        </td>
        <td style="width: 50%; text-align: right;" valign="top">
            {!! $quote->companyProfile->logo() !!}<br>
            {{ $quote->companyProfile->company }}<br>
            {!! $quote->companyProfile->formatted_address !!}<br>
            @if ($quote->companyProfile->phone) {{ $quote->companyProfile->phone }}<br>@endif
            @if ($quote->user->email) <a href="mailto:{{ $quote->user->email }}">{{ $quote->user->email }}</a>@endif
        </td>
    </tr>
</table>

<table class="alternate">
    <thead>
    <tr>
        <th>{{ mb_strtoupper(trans('ip.product')) }}</th>
        <th>{{ mb_strtoupper(trans('ip.description')) }}</th>
        <th class="amount">{{ mb_strtoupper(trans('ip.quantity')) }}</th>
        <th class="amount">{{ mb_strtoupper(trans('ip.price')) }}</th>
        <th class="amount">{{ mb_strtoupper(trans('ip.total')) }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($quote->items as $item)
        <tr>
            <td>{!! $item->name !!}</td>
            <td>{!! $item->formatted_description !!}</td>
            <td nowrap class="amount">{{ $item->formatted_quantity }}</td>
            <td nowrap class="amount">{{ $item->formatted_price }}</td>
            <td nowrap class="amount">{{ $item->amount->formatted_subtotal }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('ip.subtotal')) }}</td>
        <td class="amount">{{ $quote->amount->formatted_subtotal }}</td>
    </tr>

    @if ($quote->discount > 0)
        <tr>
            <td colspan="4" class="amount">{{ mb_strtoupper(trans('ip.discount')) }}</td>
            <td class="amount">{{ $quote->amount->formatted_discount }}</td>
        </tr>
    @endif

    @foreach ($quote->summarized_taxes as $tax)
        <tr>
            <td colspan="4" class="amount">{{ mb_strtoupper($tax->name) }} ({{ $tax->percent }})</td>
            <td class="amount">{{ $tax->total }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('ip.total')) }}</td>
        <td class="amount">{{ $quote->amount->formatted_total }}</td>
    </tr>
    </tbody>
</table>

@if ($quote->terms)
    <div class="section-header">{{ mb_strtoupper(trans('ip.terms_and_conditions')) }}</div>
    <div class="terms">{!! $quote->formatted_terms !!}</div>
@endif

<div class="footer">{!! $quote->formatted_footer !!}</div>

</body>
</html>