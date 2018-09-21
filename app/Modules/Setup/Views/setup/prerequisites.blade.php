@extends('setup.master')

@section('title')
    @lang('ip.prerequisites')
@endsection

@section('content')

    <p>@lang('ip.step_prerequisites')</p>

    <ul>
        @foreach ($errors as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>

    <a href="{{ route('setup.prerequisites') }}" class="btn btn-primary">
        @lang('ip.try_again')
    </a>

@endsection
