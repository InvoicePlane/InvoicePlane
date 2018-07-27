<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/ip_logo_64x64.png') }}">

    <title>@lang('ip.welcome')</title>

    @include('layouts.partials.head')
    @include('layouts.partials.js_global')

</head>
<body class="login-page">

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col col-md-8 col-lg-6">

            <div class="card">

                <div class="card-header">
                    <h5 class="mb-0">@lang('ip.sign_in')</h5>
                </div>

                <div class="card-body">

                    @include('layouts._alerts')

                    {!! Form::open() !!}

                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-fw fa-envelope"></i>
                                </div>
                            </div>
                            <input type="email" name="email" id="email" class="form-control"
                                    placeholder="@lang('ip.email')" aria-label="@lang('ip.email')">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-fw fa-lock"></i>
                                </div>
                            </div>
                            <input type="password" name="password" id="password" class="form-control"
                                    placeholder="@lang('ip.password')" aria-label="@lang('ip.password')">
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-8">

                            <div class="custom-control custom-checkbox pt-1">
                                <input type="hidden" name="remember_me" value="0">
                                <input type="checkbox" class="custom-control-input" id="remember_me">

                                <label class="custom-control-label" for="remember_me">
                                    @lang('ip.remember_me')
                                </label>
                            </div>

                        </div>
                        <div class="col-4">

                            <button type="submit" class="btn btn-primary btn-block">
                                @lang('ip.sign_in')
                            </button>

                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
