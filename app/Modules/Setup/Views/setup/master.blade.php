<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/ip_logo_64x64.png') }}">

    <title>{{ config('ip.headerTitleText') }}</title>

    @include('layouts.partials.head')
    @include('layouts.partials.js_global')

    @yield('head')
    @yield('javascript')

</head>
<body class="setup">

<div class="container pt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col col-md-10 col-lg-8">

            <div class="logo-wrapper text-center">
                <img src="{{ asset('assets/img/ip_logo.svg') }}" alt="@lang('ip.setup')"
                    width="300px" class="mb-5">
            </div>

            <div class="card">

                <div class="card-header">
                    <h5 class="mb-0">
                        @yield('title')
                    </h5>
                </div>

                <div class="card-body">

                    @include('layouts._alerts')

                    @yield('content')

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
