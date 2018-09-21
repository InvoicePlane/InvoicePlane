@extends('layouts.master')

@section('javascript')
    @include('recurring_invoices._js_index')
@stop

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            @lang('ip.recurring_invoices')
        </h1>

        <div class="pull-right">
            <div class="btn-group">
                {!! Form::open(['method' => 'GET', 'id' => 'filter']) !!}
                {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'recurring_invoice_filter_options form-control inline']) !!}
                {!! Form::select('status', $statuses, request('status'), ['class' => 'recurring_invoice_filter_options form-control inline']) !!}
                {!! Form::close() !!}
            </div>
            <a href="javascript:void(0)" class="btn btn-primary create-recurring-invoice"><i
                    class="fa fa-plus"></i> @lang('ip.new')</a>
        </div>

        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        @include('recurring_invoices._table')
                    </div>

                </div>

                <div class="pull-right">
                    {!! $recurringInvoices->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop
