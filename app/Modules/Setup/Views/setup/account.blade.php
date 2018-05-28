@extends('setup.master')

@section('content')

    <section class="content-header">
        <h1>{{ trans('fi.account_setup') }}</h1>
    </section>

    <section class="content">

        {!! Form::open(['route' => 'setup.postAccount', 'class' => 'form-install']) !!}

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        @include('layouts._alerts')

                        <h4>{{ trans('fi.user_account') }}</h4>

                        <div class="row">

                            <div class="col-md-3 form-group">
                                {!! Form::text('user[name]', null, ['class' => 'form-control', 'placeholder' => trans('fi.name')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::text('user[email]', null, ['class' => 'form-control', 'placeholder' => trans('fi.email')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::password('user[password]', ['class' => 'form-control', 'placeholder' => trans('fi.password')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::password('user[password_confirmation]', ['class' => 'form-control', 'placeholder' => trans('fi.password_confirmation')]) !!}
                            </div>

                        </div>

                        <h4>{{ trans('fi.company_profile') }}</h4>

                        <div class="row">
                            <div class="col-md-12 form-group">
                                {!! Form::text('company_profile[company]', null, ['class' => 'form-control', 'placeholder' => trans('fi.company')]) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 form-group">
                                {!! Form::textarea('company_profile[address]', null, ['class' => 'form-control', 'placeholder' => trans('fi.address'), 'rows' => 4]) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('company_profile[city]', null, ['id' => 'city', 'class' => 'form-control', 'placeholder' => trans('fi.city')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('company_profile[state]', null, ['id' => 'state', 'class' => 'form-control', 'placeholder' => trans('fi.state')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('company_profile[zip]', null, ['id' => 'zip', 'class' => 'form-control', 'placeholder' => trans('fi.postal_code')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::text('company_profile[country]', null, ['id' => 'country', 'class' => 'form-control', 'placeholder' => trans('fi.country')]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-3 form-group">
                                {!! Form::text('company_profile[phone]', null, ['class' => 'form-control', 'placeholder' => trans('fi.phone')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::text('company_profile[mobile]', null, ['class' => 'form-control', 'placeholder' => trans('fi.mobile')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::text('company_profile[fax]', null, ['class' => 'form-control', 'placeholder' => trans('fi.fax')]) !!}
                            </div>

                            <div class="col-md-3 form-group">
                                {!! Form::text('company_profile[web]', null, ['class' => 'form-control', 'placeholder' => trans('fi.web')]) !!}
                            </div>

                        </div>

                        <button class="btn btn-primary" type="submit">{{ trans('fi.continue') }}</button>

                    </div>

                </div>

            </div>

        </div>

        {!! Form::close() !!}

    </section>

@stop