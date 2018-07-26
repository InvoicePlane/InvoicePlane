<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>{{ config('fi.headerTitleText') }}</title>

    @include('layouts.partials.head')

    @include('layouts.partials.js_global')

    @yield('head')

    @yield('javascript')

</head>
<body>

<div id="app">

    <div id="sidebar">
        @include('layouts.partials.sidebar')
    </div>
    <div class="sidebar-backdrop sidebar-toggle"></div>

    <div id="main">

        @include('layouts.partials.topbar')

        <div id="content">
            @yield('content')
        </div>

    </div>

</div>

<div id="modal-placeholder"></div>

</body>
</html>