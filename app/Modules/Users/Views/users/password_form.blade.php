@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#password').focus();
        });
    </script>

    {!! Form::open(['route' => ['users.password.update', $user->id]]) !!}

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('fi.reset_password') }}: {{ $user->name }} ({{ $user->email }})
        </h1>
        <div class="pull-right">
            {!! Form::submit(trans('fi.reset_password'), ['class' => 'btn btn-primary']) !!}
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
                            <label>{{ trans('fi.password') }}: </label>
                            {!! Form::password('password', ['id' => 'password', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.password_confirmation') }}: </label>
                            {!! Form::password('password_confirmation', ['id' => 'password_confirmation', 'class' => 'form-control']) !!}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop