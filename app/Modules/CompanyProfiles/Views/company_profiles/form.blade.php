@extends('layouts.master')

@section('content')

    <script type="text/javascript">
      $(function () {
        $('#name').focus();

          @if ($editMode == true)
          $('#btn-delete-logo').click(function () {
            $.post("{{ route('companyProfiles.deleteLogo', [$companyProfile->id]) }}").done(function () {
              $('#div-logo').html('');
            });
          });
          @endif
      });
    </script>

    @if ($editMode == true)
        {!! Form::model($companyProfile, ['route' => ['companyProfiles.update', $companyProfile->id], 'files' => true]) !!}
    @else
        {!! Form::open(['route' => 'companyProfiles.store', 'files' => true]) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('ip.company_profile_form') }}
        </h1>
        <div class="pull-right">
            {!! Form::submit(trans('ip.save'), ['class' => 'btn btn-primary']) !!}
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="form-group">
                            <label>{{ trans('ip.company') }}: </label>
                            {!! Form::text('company', null, ['id' => 'company', 'class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('ip.address') }}: </label>
                            {!! Form::textarea('address', null, ['id' => 'address', 'class' => 'form-control', 'rows' => 4]) !!}
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.city') }}: </label>
                                    {!! Form::text('city', null, ['id' => 'city', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.state') }}: </label>
                                    {!! Form::text('state', null, ['id' => 'state', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.postal_code') }}: </label>
                                    {!! Form::text('zip', null, ['id' => 'zip', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.country') }}: </label>
                                    {!! Form::text('country', null, ['id' => 'country', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.phone') }}: </label>
                                    {!! Form::text('phone', null, ['id' => 'phone', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.fax') }}: </label>
                                    {!! Form::text('fax', null, ['id' => 'fax', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.mobile') }}: </label>
                                    {!! Form::text('mobile', null, ['id' => 'mobile', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.web') }}: </label>
                                    {!! Form::text('web', null, ['id' => 'web', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ trans('ip.logo') }}: </label>
                                    @if (!config('app.demo'))
                                        <div id="div-logo">
                                            @if ($editMode and $companyProfile->logo)
                                                <p>{!! $companyProfile->logo(100) !!}</p>
                                                <a href="javascript:void(0)"
                                                   id="btn-delete-logo">{{ trans('ip.remove_logo') }}</a>
                                            @endif
                                        </div>
                                        {!! Form::file('logo') !!}
                                    @else
                                        Disabled for demo
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ trans('ip.default_invoice_template') }}:</label>
                                    {!! Form::select('invoice_template', $invoiceTemplates, ((isset($companyProfile)) ? $companyProfile->invoice_template : config('fi.invoiceTemplate')), ['id' => 'invoice_template', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ trans('ip.default_quote_template') }}:</label>
                                    {!! Form::select('quote_template', $quoteTemplates, ((isset($companyProfile)) ? $companyProfile->quote_template : config('fi.quoteTemplate')), ['id' => 'invoice_template', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        @if ($customFields->count())
                            @include('custom_fields._custom_fields')
                        @endif


                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop