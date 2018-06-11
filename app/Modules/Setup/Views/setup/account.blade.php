@extends('setup.master')

@section('content')

    <section class="content-header">
        <h1>{{ trans('ip.account_setup') }}</h1>
    </section>

    <section class="content">

        {!! Form::open(['route' => 'setup.postAccount', 'class' => 'form-install']) !!}

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        @include('layouts._alerts')

                        <h4>{{ trans('ip.user_account') }}</h4>

                        <div class="row">

                            <div class="col-md-3 form-group">
                                {!! Form::text('user[name]', null, ['class' => 'form-control', 'placeholder' => trans('ip.name')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::text('user[email]', null, ['class' => 'form-control', 'placeholder' => trans('ip.email')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::password('user[password]', ['class' => 'form-control', 'placeholder' => trans('ip.password')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::password('user[password_confirmation]', ['class' => 'form-control', 'placeholder' => trans('ip.password_confirmation')]) !!}
                            </div>

                        </div>

                        <h4>{{ trans('ip.company_profile') }}</h4>

                        <div class="row">
                            <div class="col-md-12 form-group">
                                {!! Form::text('company_profile[company]', null, ['class' => 'form-control', 'placeholder' => trans('ip.company')]) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 form-group">
                                {!! Form::textarea('company_profile[address]', null, ['class' => 'form-control', 'placeholder' => trans('ip.address'), 'rows' => 4]) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('company_profile[city]', null, ['id' => 'city', 'class' => 'form-control', 'placeholder' => trans('ip.city')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('company_profile[state]', null, ['id' => 'state', 'class' => 'form-control', 'placeholder' => trans('ip.state')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('company_profile[zip]', null, ['id' => 'zip', 'class' => 'form-control', 'placeholder' => trans('ip.postal_code')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('company_profile[country]', null, ['id' => 'country', 'class' => 'form-control', 'placeholder' => trans('ip.country')]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-3 form-group">
                                {!! Form::text('company_profile[phone]', null, ['class' => 'form-control', 'placeholder' => trans('ip.phone')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::text('company_profile[mobile]', null, ['class' => 'form-control', 'placeholder' => trans('ip.mobile')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::text('company_profile[fax]', null, ['class' => 'form-control', 'placeholder' => trans('ip.fax')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::text('company_profile[web]', null, ['class' => 'form-control', 'placeholder' => trans('ip.web')]) !!}
                            </div>

                        </div>

                        <button class="btn btn-primary" type="submit">{{ trans('ip.continue') }}</button>

                    </div>

                </div>

            </div>

        </div>

        {!! Form::close() !!}

    </section>

@stop