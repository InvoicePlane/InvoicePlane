@extends('setup.master')

@section('javascript')
    <script type="text/javascript">
        $(function () {
            var form_driver = $('.form_db_connection'),
                form_port = $('.form_db_port'),
                form_host = $('.form_db_host'),
                form_username = $('.form_db_username'),
                form_password = $('.form_db_password'),
                form_sqlite_info = $('.form_db_sqlite');

            function db_form_update () {
                if(form_driver.val().trim().localeCompare('sqlite') === 0) {
                    form_host.hide();
                    form_port.hide();
                    form_username.hide();
                    form_password.hide();
                    form_sqlite_info.show();
                } else {
                    form_host.show();
                    form_port.show();
                    form_username.show();
                    form_password.show();
                    form_sqlite_info.hide();
                }
            }
            db_form_update();

            form_driver.change(db_form_update);
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>@lang('ip.database_setup')</h1>
    </section>

    <section class="content">

        {!! Form::open(['route' => 'setup.postDbconfig', 'class' => 'form-install']) !!}

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-body">

                        @include('layouts._alerts')

                        <h4>@lang('ip.database_setup')</h4>

                        <p>@lang('ip.database_configuration_info')</p>

                        <div class="row">
                            <div class="col-xs-12 col-md-6">

                                <div class="form-group">
                                    <label for="db_host">@lang('ip.database_connection')</label>
                                    {!! Form::select('db_connection', $available_drivers, old('db_connection') ?: $default_driver, [
                                        'class' => 'form-control form_db_connection'
                                    ]) !!}
                                </div>

                                <div class="form-group form_db_host">
                                    <label for="db_host">@lang('ip.database_host')</label>
                                    {!! Form::text('db_host', old('db_host') ?: $default['host'], [
                                        'class' => 'form-control', 'placeholder' => trans('ip.database_host')
                                    ]) !!}
                                </div>

                                <div class="form-group form_db_port">
                                    <label for="db_port">@lang('ip.database_port')</label>
                                    {!! Form::text('db_port', old('db_port') ?: $default['port'], [
                                        'class' => 'form-control', 'placeholder' => trans('ip.database_port')
                                    ]) !!}
                                </div>

                                <div class="form-group">
                                    <label for="db_database">@lang('ip.database_database')</label>
                                    <p class="form_db_sqlite">@lang('ip.database_sqlite_info')</p>
                                    {!! Form::text('db_database', old('db_database') ?: $default['database'], [
                                        'class' => 'form-control', 'placeholder' => trans('ip.database_database')
                                    ]) !!}
                                </div>

                                <div class="form-group form_db_username">
                                    <label for="db_username">@lang('ip.database_user')</label>
                                    {!! Form::text('db_username', old('db_username') ?: $default['username'], [
                                        'class' => 'form-control', 'placeholder' => trans('ip.database_user')
                                    ]) !!}
                                </div>

                                <div class="form-group form_db_password">
                                    <label for="db_password">@lang('ip.database_pass')</label>
                                    {!! Form::password('db_password', [
                                        'class' => 'form-control', 'placeholder' => trans('ip.database_pass')
                                    ]) !!}
                                </div>

                                <div class="form-group">
                                    <label for="db_prefix">@lang('ip.database_prefix')</label>
                                    {!! Form::text('db_prefix', old('db_prefix') ?: $default['prefix'], [
                                        'class' => 'form-control', 'placeholder' => trans('ip.database_prefix')
                                    ]) !!}
                                </div>

                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit">@lang('ip.continue')</button>

                    </div>

                </div>

            </div>

        </div>

        {!! Form::close() !!}

    </section>

@stop