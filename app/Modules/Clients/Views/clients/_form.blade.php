@include('clients._js_unique_name')

<script>
  $(function () {
    $('#name').focus();
  });
</script>

<div class="row">
    <div class="col-12 col-sm-4">
        <div class="form-group">
            <label>@lang('ip.client_name')*</label>
            {!! Form::text('name', null, [
                'id' => 'name',
                'class' => 'form-control',
                'required' => 'required',
            ]) !!}
            <small class="form-text">
                @lang('ip.help_text_client_name')
            </small>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="form-group">
            <label>@lang('ip.unique_name')*</label>
            {!! Form::text('unique_name', null, ['id' => 'unique_name', 'class' => 'form-control']) !!}
            <small class="form-text">
                @lang('ip.help_text_client_unique_name')
            </small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-sm-4">
        <div class="form-group">
            <label>@lang('ip.email_address')</label>
            {!! Form::text('client_email', null, [
                'id' => 'client_email',
                'class' => 'form-control',
                'required' => 'required',
            ]) !!}
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="form-group">
            <label>@lang('ip.active')</label>
            {!! Form::select(
                'active', ['0' => trans('ip.no'), '1' => trans('ip.yes')],
                ((isset($editMode) && $editMode) ? null : 1),
                ['id' => 'active', 'class' => 'form-control']
            ) !!}
        </div>
    </div>
</div>

<div class="form-group">
    <label>@lang('ip.address')</label>
    {!! Form::textarea('address', null, ['id' => 'address', 'class' => 'form-control', 'rows' => 3]) !!}
</div>

<div class="row">
    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.city')</label>
            {!! Form::text('city', null, ['id' => 'city', 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.state')</label>
            {!! Form::text('state', null, ['id' => 'state', 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.postal_code')</label>
            {!! Form::text('zip', null, ['id' => 'zip', 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.country')</label>
            {!! Form::text('country', null, ['id' => 'country', 'class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.phone_number')</label>
            {!! Form::text('phone', null, ['id' => 'phone', 'class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.fax_number')</label>
            {!! Form::text('fax', null, ['id' => 'fax', 'class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.mobile_number')</label>
            {!! Form::text('mobile', null, ['id' => 'mobile', 'class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.web_address')</label>
            {!! Form::text('web', null, ['id' => 'web', 'class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.default_currency')</label>
            {!! Form::select(
                'currency_code',
                $currencies,
                (isset($client)) ? $client->currency_code : config('ip.baseCurrency'),
                ['id' => 'currency_code', 'class' => 'form-control']
            ) !!}
        </div>
    </div>
    <div class="col-12 col-sm-3">
        <div class="form-group">
            <label>@lang('ip.language')</label>
            {!! Form::select(
                'language',
                $languages,
                (isset($client)) ? $client->language : config('ip.language'),
                ['id' => 'language', 'class' => 'form-control']
            ) !!}
        </div>
    </div>
</div>

@if ($customFields->count())
    @include('custom_fields._custom_fields')
@endif
