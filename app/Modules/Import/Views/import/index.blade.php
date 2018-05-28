@extends('layouts.master')

@section('content')

    {!! Form::open(['route' => 'import.upload', 'files' => true]) !!}

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('fi.import_data') }}
        </h1>
        <div class="pull-right">
            @if (!config('app.demo'))
                {!! Form::submit(trans('fi.submit'), ['class' => 'btn btn-primary']) !!}
            @endif
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="form-group">
                            <label>{{ trans('fi.what_to_import') }}</label>
                            {!! Form::select('import_type', $importTypes, null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.select_file_to_import') }}</label>
                            @if (!config('app.demo'))
                                {!! Form::file('import_file') !!}
                            @else
                                Imports are disabled in the demo.
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop