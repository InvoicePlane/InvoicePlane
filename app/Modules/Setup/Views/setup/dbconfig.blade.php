@extends('setup.master')

@section('title')
    @lang('ip.database_setup')
@endsection

@section('content')

    {!! Form::open(['route' => 'setup.postDbconfig', 'class' => 'form-install']) !!}

    <p>@lang('ip.database_configuration_info')</p>

    <div class="form-group">
        <label for="db_host">@lang('ip.database_host')</label>
        {!! Form::text('db_host', old('db_host') ?: 'localhost', [
            'class' => 'form-control',
            'placeholder' => trans('ip.database_host')
        ]) !!}
    </div>

    <div class="form-group">
        <label for="db_port">@lang('ip.database_port')</label>
        {!! Form::text('db_port', old('db_port') ?: '3306', [
            'class' => 'form-control',
            'placeholder' => trans('ip.database_port')
        ]) !!}
    </div>

    <div class="form-group">
        <label for="db_database">@lang('ip.database_database')</label>
        {!! Form::text('db_database', old('db_database'), [
            'class' => 'form-control',
            'placeholder' => trans('ip.database_database')
        ]) !!}
    </div>

    <div class="form-group">
        <label for="db_username">@lang('ip.database_user')</label>
        {!! Form::text('db_username', old('db_username'), [
            'class' => 'form-control',
            'placeholder' => trans('ip.database_user')
        ]) !!}
    </div>

    <div class="form-group">
        <label for="db_password">@lang('ip.database_pass')</label>
        {!! Form::password('db_password', [
            'class' => 'form-control',
            'placeholder' => trans('ip.database_pass')
        ]) !!}
    </div>

    <div class="form-group">
        <label for="db_prefix">@lang('ip.database_prefix')</label>
        {!! Form::text('db_prefix', old('db_prefix'), [
            'class' => 'form-control',
            'placeholder' => trans('ip.database_prefix')
        ]) !!}
    </div>

    <button class="btn btn-primary" type="submit">
        @lang('ip.continue')
    </button>

    {!! Form::close() !!}

@endsection
