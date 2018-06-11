@extends('setup.master')

@section('content')

    <section class="content-header">
        <h1>{{ trans('ip.setup') }}</h1>
    </section>

    <section class="content">

        {!! Form::open() !!}

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-body">

                        <p>@lang('fi.setup_welcome')</p>

                        {!! Form::submit(trans('ip.continue'), ['class' => 'btn btn-primary']) !!}

                    </div>
                </div>

            </div>
        </div>

        {!! Form::close() !!}

    </section>

@stop