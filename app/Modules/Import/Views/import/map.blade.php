@extends('layouts.master')

@section('content')

    {!! Form::open(['route' => ['import.map.submit', $importType], 'class' => 'form-horizontal']) !!}

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.map_fields_to_import') }}
        </h1>

        <div class="pull-right">
            {!! Form::submit(trans('ip.submit'), ['class' => 'btn btn-primary']) !!}
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                            @foreach ($importFields as $key => $field)
                                <tr>
                                    <td style="width: 20%;">{{ $field }}</td>
                                    <td>{!! Form::select($key, $fileFields, (is_numeric(array_search($key, $fileFields)) ? array_search($key, $fileFields) : null), ['class' => 'form-control']) !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop