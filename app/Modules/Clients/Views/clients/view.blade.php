@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('fi.view_client') }}
        </h1>
        <div class="pull-right">
            <a href="javascript:void(0)" class="btn btn-default create-quote"
               data-unique-name="{{ $client->unique_name }}">{{ trans('fi.create_quote') }}</a>
            <a href="javascript:void(0)" class="btn btn-default create-invoice"
               data-unique-name="{{ $client->unique_name }}">{{ trans('fi.create_invoice') }}</a>
            <a href="{{ route('clients.edit', [$client->id]) }}" class="btn btn-default">{{ trans('fi.edit') }}</a>
            <a class="btn btn-default" href="{{ route('clients.delete', [$client->id]) }}"
               onclick="return confirm('{{ trans('fi.delete_client_warning') }}');"><i
                        class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-details">{{ trans('fi.details') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-quotes">{{ trans('fi.quotes') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-invoices">{{ trans('fi.invoices') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-recurring-invoices">{{ trans('fi.recurring_invoices') }}</a>
                        </li>
                        <li><a data-toggle="tab" href="#tab-payments">{{ trans('fi.payments') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-attachments">{{ trans('fi.attachments') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-notes">{{ trans('fi.notes') }}</a></li>
                    </ul>
                    <div class="tab-content">

                        <div id="tab-details" class="tab-pane active">

                            <div class="row">

                                <div class="col-md-12">

                                    <div class="pull-left">
                                        <h2>{!! $client->name !!}</h2>
                                    </div>

                                    <div class="pull-right" style="text-align: right;">
                                        <p>
                                            <strong>{{ trans('fi.total_billed') }}
                                                :</strong> {{ $client->formatted_total }}<br/>
                                            <strong>{{ trans('fi.total_paid') }}:</strong> {{ $client->formatted_paid }}
                                            <br/>
                                            <strong>{{ trans('fi.total_balance') }}
                                                :</strong> {{ $client->formatted_balance }}
                                        </p>
                                    </div>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12">

                                    <table class="table table-striped">
                                        <tr>
                                            <td class="col-md-2">{{ trans('fi.address') }}</td>
                                            <td class="col-md-10">{!! $client->formatted_address !!}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2">{{ trans('fi.email') }}</td>
                                            <td class="col-md-10"><a
                                                        href="mailto:{!! $client->email !!}">{!! $client->email !!}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2">{{ trans('fi.phone') }}</td>
                                            <td class="col-md-10">{!! $client->phone !!}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2">{{ trans('fi.mobile') }}</td>
                                            <td class="col-md-10">{!! $client->mobile !!}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2">{{ trans('fi.fax') }}</td>
                                            <td class="col-md-10">{!! $client->fax !!}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2">{{ trans('fi.web') }}</td>
                                            <td class="col-md-10"><a href="{!! $client->web !!}"
                                                                     target="_blank">{!! $client->web !!}</a></td>
                                        </tr>
                                        @foreach ($customFields as $customField)
                                            <tr>
                                                <td class="col-md-2">{!! $customField->field_label !!}</td>
                                                <td class="col-md-10">
                                                    @if (isset($client->custom->{$customField->column_name}))
                                                        {!! nl2br($client->custom->{$customField->column_name}) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>

                                </div>

                            </div>

                        </div>

                        <div id="tab-quotes" class="tab-pane">
                            <div class="panel panel-default">
                                @include('quotes._table')
                                <div class="panel-footer"><p class="text-center"><strong><a
                                                    href="{{ route('quotes.index') }}?client={{ $client->id }}">{{ trans('fi.view_all') }}</a></strong>
                                    </p></div>
                            </div>
                        </div>

                        <div id="tab-invoices" class="tab-pane">
                            <div class="panel panel-default">
                                @include('invoices._table')
                                <div class="panel-footer"><p class="text-center"><strong><a
                                                    href="{{ route('invoices.index') }}?client={{ $client->id }}">{{ trans('fi.view_all') }}</a></strong>
                                    </p></div>
                            </div>
                        </div>

                        <div id="tab-recurring-invoices" class="tab-pane">
                            <div class="panel panel-default">
                                @include('recurring_invoices._table')
                                <div class="panel-footer"><p class="text-center"><strong><a
                                                    href="{{ route('recurringInvoices.index') }}?client={{ $client->id }}">{{ trans('fi.view_all') }}</a></strong>
                                    </p></div>
                            </div>
                        </div>

                        <div id="tab-payments" class="tab-pane">
                            <div class="panel panel-default">
                                @include('payments._table')
                                <div class="panel-footer"><p class="text-center"><strong><a
                                                    href="{{ route('payments.index') }}?client={{ $client->id }}">{{ trans('fi.view_all') }}</a></strong>
                                    </p></div>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab-attachments">
                            @include('attachments._table', ['object' => $client, 'model' => 'FI\Modules\Clients\Models\Client'])
                        </div>

                        <div id="tab-notes" class="tab-pane">
                            @include('notes._notes', ['object' => $client, 'model' => 'FI\Modules\Clients\Models\Client', 'hideHeader' => true])
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </section>

@stop