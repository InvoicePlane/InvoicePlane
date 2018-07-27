@extends('setup.master')

@section('title')
    @lang('ip.installation_complete')
@endsection

@section('content')

    <p>@lang('ip.you_may_now_sign_in')</p>

    <a href="{{ route('session.login') }}" class="btn btn-primary">@lang('ip.sign_in')</a>

@endsection
