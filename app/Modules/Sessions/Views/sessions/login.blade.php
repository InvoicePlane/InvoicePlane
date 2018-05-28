<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ trans('fi.welcome') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link href="{{ asset('favicon.png') }}" rel="icon" type="image/png">
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/dist/css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/style.css') }}" rel="stylesheet" type="text/css"/>

    @if (file_exists(base_path('custom/custom.css')))
        <link href="{{ asset('custom/custom.css') }}" rel="stylesheet" type="text/css"/>
    @endif

</head>
<body class="login-page">
<div class="login-box">
    <div class="login-box-body">
        @include('layouts._alerts')
        {!! Form::open() !!}
        <div class="form-group has-feedback">
            <input type="email" name="email" id="email" class="form-control" placeholder="{{ trans('fi.email') }}">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" name="password" class="form-control" placeholder="{{ trans('fi.password') }}">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox">
                    <label>
                        <input type="hidden" name="remember_me" value="0">
                        <input type="checkbox" name="remember_me" value="1"> {{ trans('fi.remember_me') }}
                    </label>
                </div>
            </div>
            <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('fi.sign_in') }}</button>
            </div>
        </div>
        {!! Form::close() !!}

    </div>
</div>

<script src="{{ asset('assets/plugins/jQuery/jQuery.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
        $('#email').focus();
    });
</script>

</body>
</html>