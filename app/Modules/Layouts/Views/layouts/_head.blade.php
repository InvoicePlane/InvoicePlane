<link href="{{ asset('favicon.png') }}" rel="icon" type="image/png">
<link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/ionicons/css/ionicons.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/dist/css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/dist/css/skins/' . $skin) }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/style.css') }}" rel="stylesheet" type="text/css"/>

@if (file_exists(base_path('custom/custom.css')))
    <link href="{{ asset('custom/custom.css') }}" rel="stylesheet" type="text/css"/>
@endif

<script src="{{ asset('assets/plugins/jQuery/jQuery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/slimScroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src='{{ asset('assets/plugins/fastclick/fastclick.min.js') }}'></script>
<script src='{{ asset('assets/plugins/notify/bootstrap-notify.min.js') }}'></script>

<script src="{{ asset('assets/dist/js/app.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/autosize/jquery-autosize.min.js') }}" type="text/javascript"></script>