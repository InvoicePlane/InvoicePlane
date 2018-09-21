@extends('setup.master')

@section('title')
    @lang('ip.account_setup')
@endsection

@section('content')

    {!! Form::open(['route' => 'setup.postAccount', 'class' => 'form-install']) !!}

    <h5>@lang('ip.user_account')</h5>

    <div class="row">
        <div class="col col-md-6 form-group">
            {!! Form::text('user[name]', null, [
                'class' => 'form-control',
                'placeholder' => trans('ip.name'),
                'required' => 'required',
            ]) !!}
        </div>

        <div class="col col-md-6 form-group">
            {!! Form::email('user[email]', null, [
                'class' => 'form-control',
                'placeholder' => trans('ip.email'),
                'required' => 'required',
            ]) !!}
        </div>
    </div>

    <div class="row mt-3">
        <div class="col col-md-6 form-group">
            {!! Form::password('user[password]', [
                'class' => 'form-control',
                'placeholder' => trans('ip.password'),
                'required' => 'required',
                'min' => '8',
            ]) !!}
        </div>

        <div class="col col-md-6 form-group">
            {!! Form::password('user[password_confirmation]', [
                'class' => 'form-control',
                'placeholder' => trans('ip.password_confirmation'),
                'required' => 'required',
                'min' => '8',
            ]) !!}
        </div>
    </div>

    <h5 class="mt-3">@lang('ip.company_profile')</h5>

    <div class="row">
        <div class="col form-group">
            {!! Form::text('company_profile[company]', null, [
                'class' => 'form-control',
                'placeholder' => trans('ip.company'),
                'required' => 'required',
            ]) !!}
        </div>
    </div>

    <div class="row">
        <div class="col form-group">
            {!! Form::textarea('company_profile[address]', null, [
            'class' => 'form-control',
            'placeholder' => trans('ip.address'),
            'rows' => 4,
            'required' => 'required',
            ]) !!}
        </div>
    </div>

    <div class="row">
        <div class="col col-md-6">
            <div class="form-group">
                {!! Form::text('company_profile[city]', null, [
                    'id' => 'city',
                    'class' => 'form-control',
                    'placeholder' => trans('ip.city')
                ]) !!}
            </div>
        </div>
        <div class="col col-md-6">
            <div class="form-group">
                {!! Form::text('company_profile[state]', null, [
                    'id' => 'state',
                    'class' => 'form-control',
                    'placeholder' => trans('ip.state')
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col col-md-6">
            <div class="form-group">
                {!! Form::text('company_profile[zip]', null, [
                    'id' => 'zip',
                    'class' => 'form-control',
                    'placeholder' => trans('ip.postal_code')
                ]) !!}
            </div>
        </div>
        <div class="col col-md-6">
            <div class="form-group">
                {!! Form::text('company_profile[country]', null, [
                    'id' => 'country',
                    'class' => 'form-control',
                    'placeholder' => trans('ip.country')
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col col-md-6 form-group">
            {!! Form::text('company_profile[phone]', null, [
                'class' => 'form-control',
                'placeholder' => trans('ip.phone')
            ]) !!}
        </div>

        <div class="col col-md-6 form-group">
            {!! Form::text('company_profile[mobile]', null, [
                'class' => 'form-control',
                'placeholder' => trans('ip.mobile')
            ]) !!}
        </div>
    </div>

    <div class="row">
        <div class="col col-md-6 form-group">
            {!! Form::text('company_profile[fax]', null, [
                'class' => 'form-control',
                'placeholder' => trans('ip.fax')
            ]) !!}
        </div>

        <div class="col col-md-6 form-group">
            {!! Form::text('company_profile[web]', null, [
                'class' => 'form-control',
                'placeholder' => trans('ip.web')
            ]) !!}
        </div>
    </div>

    <button class="btn btn-primary mt-3" type="submit">@lang('ip.continue')</button>

    {!! Form::close() !!}

@endsection
