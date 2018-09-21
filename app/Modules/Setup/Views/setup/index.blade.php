@extends('setup.master')

@section('title')
    @lang('ip.setup')
@endsection

@section('content')

    {!! Form::open() !!}

    <p>@lang('ip.setup_welcome')</p>

    {!! Form::submit(trans('ip.continue'), ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}

@endsection
