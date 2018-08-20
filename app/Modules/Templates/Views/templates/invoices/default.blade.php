<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('ip.invoice') #{{ $invoice->number }}</title>

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
            <h1>{{ mb_strtoupper(trans('ip.invoice')) }}</h1>
            <span class="info">{{ mb_strtoupper(trans('ip.invoice')) }} #</span>{{ $invoice->number }}<br>
            <span class="info">{{ mb_strtoupper(trans('ip.issued')) }}</span> {{ $invoice->formatted_created_at }}<br>
            <span class="info">{{ mb_strtoupper(trans('ip.due_date')) }}</span> {{ $invoice->formatted_due_at }}<br><br>
            <span class="info">{{ mb_strtoupper(trans('ip.bill_to')) }}</span><br>{{ $invoice->client->name }}<br>
            @if ($invoice->client->address) {!! $invoice->client->formatted_address !!}<br>@endif
        </td>
        <td style="width: 50%; text-align: right;" valign="top">
            {!! $invoice->companyProfile->logo() !!}<br>
            {{ $invoice->companyProfile->company }}<br>
            {!! $invoice->companyProfile->formatted_address !!}<br>
            @if ($invoice->companyProfile->phone) {{ $invoice->companyProfile->phone }}<br>@endif
            @if ($invoice->user->email) <a
                href="mailto:{{ $invoice->user->email }}">{{ $invoice->user->email }}</a>@endif
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
    @foreach ($invoice->items as $item)
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
        <td class="amount">{{ $invoice->amount->formatted_subtotal }}</td>
    </tr>

    @if ($invoice->discount > 0)
        <tr>
            <td colspan="4" class="amount">{{ mb_strtoupper(trans('ip.discount')) }}</td>
            <td class="amount">{{ $invoice->amount->formatted_discount }}</td>
        </tr>
    @endif

    @foreach ($invoice->summarized_taxes as $tax)
        <tr>
            <td colspan="4" class="amount">{{ mb_strtoupper($tax->name) }} ({{ $tax->percent }})</td>
            <td class="amount">{{ $tax->total }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('ip.total')) }}</td>
        <td class="amount">{{ $invoice->amount->formatted_total }}</td>
    </tr>
    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('ip.paid')) }}</td>
        <td class="amount">{{ $invoice->amount->formatted_paid }}</td>
    </tr>
    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('ip.balance')) }}</td>
        <td class="amount">{{ $invoice->amount->formatted_balance }}</td>
    </tr>
    </tbody>
</table>

@if ($invoice->terms)
    <div class="section-header">{{ mb_strtoupper(trans('ip.terms_and_conditions')) }}</div>
    <div class="terms">{!! $invoice->formatted_terms !!}</div>
@endif

<div class="footer">{!! $invoice->formatted_footer !!}</div>

</body>
</html>
