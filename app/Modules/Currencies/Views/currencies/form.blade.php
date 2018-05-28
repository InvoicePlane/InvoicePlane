@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });
    </script>

    @if ($editMode == true)
        {!! Form::model($currency, ['route' => ['currencies.update', $currency->id]]) !!}
    @else
        {!! Form::open(['route' => 'currencies.store']) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('fi.currency_form') }}
        </h1>
        <div class="pull-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="form-group">
                            <label>{{ trans('fi.name') }}: </label>
                            {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control']) !!}
                            <p class="help-block">{{ trans('fi.help_currency_name') }}</p>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ trans('fi.code') }}: </label>
                                    @if ($editMode and $currency->in_use)
                                        {!! Form::text('code', null, ['id' => 'code', 'class' => 'form-control',
                                        'readonly' => 'readonly']) !!}
                                    @else
                                        {!! Form::text('code', null, ['id' => 'code', 'class' => 'form-control'])
                                        !!}
                                    @endif

                                    <p class="help-block">{{ trans('fi.help_currency_code') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ trans('fi.symbol') }}: </label>
                                    {!! Form::text('symbol', null, ['id' => 'symbol', 'class' => 'form-control'])
                                    !!}
                                    <p class="help-block">{{ trans('fi.help_currency_symbol') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ trans('fi.symbol_placement') }}: </label>
                                    {!! Form::select('placement', ['before' => trans('fi.before_amount'), 'after'
                                    => trans('fi.after_amount')], null, ['class' => 'form-control']) !!}
                                    <p class="help-block">{{ trans('fi.help_currency_symbol_placement') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('fi.decimal_point') }}: </label>
                                    {!! Form::text('decimal', null, ['id' => 'decimal', 'class' => 'form-control'])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('fi.thousands_separator') }}: </label>
                                    {!! Form::text('thousands', null, ['id' => 'thousands', 'class' =>
                                    'form-control']) !!}
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop