@extends('setup.master')

@section('content')

    <section class="content-header">
        <h1>@lang('ip.prerequisites')</h1>
    </section>

    <section class="content">

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <p>@lang('ip.step_prerequisites')</p>

                        <ul>
                            @foreach ($errors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                        <a href="{{ route('setup.prerequisites') }}"
                           class="btn btn-primary">@lang('ip.try_again')</a>

                    </div>

                </div>

            </div>

        </div>

        {!! Form::close() !!}

    </section>

@stop