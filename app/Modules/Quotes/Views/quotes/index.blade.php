@extends('layouts.master')

@section('javascript')
    @include('quotes._js_index')
@stop

@section('content')

    <section class="content-header">
        <h1 class="pull-left">{{ trans('fi.quotes') }}</h1>

        <div class="pull-right">

            <div class="btn-group bulk-actions">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    {{ trans('fi.change_status') }} <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    @foreach ($keyedStatuses as $key => $status)
                        <li><a href="javascript:void(0)" class="bulk-change-status" data-status="{{ $key }}">{{ $status }}</a></li>
                    @endforeach
                </ul>
            </div>

            <a href="javascript:void(0)" class="btn btn-default bulk-actions" id="btn-bulk-delete"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>

            <div class="btn-group">
                {!! Form::open(['method' => 'GET', 'id' => 'filter']) !!}
                {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'quote_filter_options form-control inline']) !!}
                {!! Form::select('status', $statuses, request('status'), ['class' => 'quote_filter_options form-control inline']) !!}
                {!! Form::close() !!}
            </div>
            <a href="javascript:void(0)" class="btn btn-primary create-quote"><i class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
        </div>

        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        @include('quotes._table')
                    </div>

                </div>

                <div class="pull-right">
                    {!! $quotes->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop