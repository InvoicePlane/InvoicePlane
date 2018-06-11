@extends('layouts.master')

@section('content')

    <script type="text/javascript">
      $(function () {
        $('#name').focus();

        $('#btn-generate-api-keys').click(function () {
          $.post("{{ route('api.generateKeys') }}", function (response) {
            $('#api_public_key').val(response.api_public_key);
            $('#api_secret_key').val(response.api_secret_key);
          });
        });

        $('#btn-clear-api-keys').click(function () {
          $('#api_public_key').val('');
          $('#api_secret_key').val('');
        });
      });
    </script>

    @if ($editMode == true)
        {!! Form::model($user, ['route' => ['users.update', $user->id, 'admin']]) !!}
    @else
        {!! Form::open(['route' => ['users.store', 'admin']]) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.admin') . ' ' . trans('ip.user_form') }}
        </h1>
        <div class="pull-right">
            <button class="btn btn-primary"><i class="fa fa-save"></i> @lang('ip.save')</button>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('ip.name'): </label>
                                    {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('ip.email'): </label>
                                    {!! Form::text('email', null, ['id' => 'email', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        @if (!$editMode)
                            <div class="form-group">
                                <label>@lang('ip.password'): </label>
                                {!! Form::password('password', ['id' => 'password', 'class' => 'form-control']) !!}
                            </div>

                            <div class="form-group">
                                <label>@lang('ip.password_confirmation'): </label>
                                {!! Form::password('password_confirmation', ['id' => 'password_confirmation',
                                'class' => 'form-control']) !!}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('ip.api_public_key'): </label>
                                    {!! Form::text('api_public_key', null, ['id' => 'api_public_key', 'class' => 'form-control', 'readonly' => 'readonly']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('ip.api_secret_key'): </label>
                                    {!! Form::text('api_secret_key', null, ['id' => 'api_secret_key', 'class' => 'form-control', 'readonly' => 'readonly']) !!}
                                </div>
                            </div>
                        </div>

                        <a href="#" class="btn btn-default"
                           id="btn-generate-api-keys">@lang('ip.generate_keys')</a>
                        <a href="#" class="btn btn-default" id="btn-clear-api-keys">@lang('ip.clear_keys')</a>

                    </div>

                </div>

                @if ($customFields->count())
                    <div class="box box-primary">

                        <div class="box-header">
                            <h3 class="box-title">@lang('ip.custom_fields')</h3>
                        </div>

                        <div class="box-body">

                            @include('custom_fields._custom_fields')

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop