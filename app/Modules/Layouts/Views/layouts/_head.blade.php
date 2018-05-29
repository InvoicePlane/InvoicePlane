<link href="{{ asset('favicon.png') }}" rel="icon" type="image/png">
<link href="{{ asset('assets/dist/app.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/dist/adminlte/css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/dist/adminlte/css/skins/' . $skin) }}" rel="stylesheet" type="text/css"/>

@if (file_exists(base_path('custom/custom.css')))
    <link href="{{ asset('custom/custom.css') }}" rel="stylesheet" type="text/css"/>
@endif

<script src="{{ asset('assets/dist/dependencies.js') }}"></script>
