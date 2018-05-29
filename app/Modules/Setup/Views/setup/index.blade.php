@extends('setup.master')

@section('content')

    <section class="content-header">
        <h1>{{ trans('fi.setup') }}</h1>
    </section>

    <section class="content">

        {!! Form::open() !!}

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <p>Welcome to the InvoicePlane Setup.</p>

                        {!! Form::submit(trans('fi.continue'), ['class' => 'btn btn-primary']) !!}

                    </div>

                </div>

            </div>

        </div>

        {!! Form::close() !!}

    </section>

@stop