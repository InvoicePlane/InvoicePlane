<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>{{ config('ip.headerTitleText') }}</title>

    @include('layouts.partials.head')

    @include('layouts.partials.js_global')

    @yield('head')

    @yield('javascript')

</head>
<body>

<div id="app">

    <aside id="sidebar">
        @include('layouts.partials.sidebar')
    </aside>
    <div class="sidebar-backdrop sidebar-toggle"></div>

    <section id="main">

        @include('layouts.partials.topbar')

        @hasSection('content-form')
            @yield('content-form')
        @endif

        @hasSection('content-header')
            <section id="content-header">
                @yield('content-header')
            </section>
        @endif

        <section id="content">
            @include('layouts._alerts')
            @yield('content')
        </section>

        @hasSection('content-form')
            {!! Form::close() !!}
        @endif

    </section>

</div>

<div id="modal-placeholder"></div>

</body>
</html>
