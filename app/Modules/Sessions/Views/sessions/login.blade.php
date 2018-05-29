<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ trans('fi.welcome') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    @include('layouts._head')

    @include('layouts._js_global')

</head>
<body class="login-page">
<div class="login-box">
    <div class="login-box-body">
        @include('layouts._alerts')
        {!! Form::open() !!}
        <div class="form-group has-feedback">
            <input type="email" name="email" id="email" class="form-control" placeholder="{{ trans('fi.email') }}">
            <span class="fa fa-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" name="password" class="form-control" placeholder="{{ trans('fi.password') }}">
            <span class="fa fa-lock form-control-feedback"></span>
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

</body>
</html>
