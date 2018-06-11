@extends('client_center.layouts.logged_in')

@section('content')

    <section class="content-header">
        <h1>{{ trans('ip.dashboard') }}</h1>
    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('ip.recent_quotes') }}</h3>
                    </div>
                    @if (count($quotes))
                        <div class="box-body no-padding">
                            @include('client_center.quotes._table')
                            <p style="text-align: center;"><a href="{{ route('clientCenter.quotes') }}"
                                                              class="btn btn-default">{{ trans('ip.view_all') }}</a></p>
                        </div>
                    @else
                        <div class="box-body">
                            <p>{{ trans('ip.no_records_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('ip.recent_invoices') }}</h3>
                    </div>
                    @if (count($invoices))
                        <div class="box-body no-padding">
                            @include('client_center.invoices._table')
                            <p style="text-align: center;"><a href="{{ route('clientCenter.invoices') }}"
                                                              class="btn btn-default">{{ trans('ip.view_all') }}</a></p>
                        </div>
                    @else
                        <div class="box-body">
                            <p>{{ trans('ip.no_records_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('ip.recent_payments') }}</h3>
                    </div>
                    @if (count($payments))
                        <div class="box-body no-padding">
                            @include('client_center.payments._table')
                            <p style="text-align: center;"><a href="{{ route('clientCenter.payments') }}"
                                                              class="btn btn-default">{{ trans('ip.view_all') }}</a></p>
                        </div>
                    @else
                        <div class="box-body">
                            <p>{{ trans('ip.no_records_found') }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>

@stop