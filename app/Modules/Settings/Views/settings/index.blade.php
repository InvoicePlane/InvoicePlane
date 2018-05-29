@extends('layouts.master')

@section('javascript')
    @parent
    <script type="text/javascript">
      $(function () {
        $('#btn-submit').click(function () {
          $('#form-settings').submit();
        });

        $('#btn-recalculate-invoices').click(function () {
          var $btn = $(this).button('loading');
          $.post("{{ route('invoices.recalculate') }}").done(function (response) {
            alert(response.message);
          }).fail(function (response) {
            alert('{{ trans('fi.error') }}: ' + $.parseJSON(response.responseText).message);
          }).always(function () {
            $btn.button('reset');
          });
        });

        $('#btn-recalculate-quotes').click(function () {
          var $btn = $(this).button('loading');
          $.post("{{ route('quotes.recalculate') }}").done(function (response) {
            alert(response.message);
          }).fail(function (response) {
            alert('{{ trans('fi.error') }}: ' + $.parseJSON(response.responseText).message);
          }).always(function () {
            $btn.button('reset');
          });
        });

        $('#setting-tabs a').click(function (e) {
          var tabId = $(e.target).attr('href').substr(1);
          $.post("{{ route('settings.saveTab') }}", {settingTabId: tabId});
        });

        $('#setting-tabs a[href="#{{ session('settingTabId') }}"]').tab('show');

      });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('fi.system_settings') }}
        </h1>

        <div class="pull-right">
            <button class="btn btn-primary" id="btn-submit"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        {!! Form::open(['route' => 'settings.update', 'files' => true, 'id' => 'form-settings']) !!}

        <div class="row">
            <div class="col-md-12">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="setting-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-general">{{ trans('fi.general') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-dashboard">{{ trans('fi.dashboard') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-invoices">{{ trans('fi.invoices') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-quotes">{{ trans('fi.quotes') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-taxes">{{ trans('fi.taxes') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-email">{{ trans('fi.email') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-pdf">{{ trans('fi.pdf') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-online-payments">{{ trans('fi.online_payments') }}</a></li>
                        <li><a data-toggle="tab" href="#tab-backup">{{ trans('fi.backup') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-general" class="tab-pane active">
                            @include('settings._general')
                        </div>
                        <div id="tab-dashboard" class="tab-pane">
                            @include('settings._dashboard')
                        </div>
                        <div id="tab-invoices" class="tab-pane">
                            @include('settings._invoices')
                        </div>
                        <div id="tab-quotes" class="tab-pane">
                            @include('settings._quotes')
                        </div>
                        <div id="tab-taxes" class="tab-pane">
                            @include('settings._taxes')
                        </div>
                        <div id="tab-email" class="tab-pane">
                            @include('settings._email')
                        </div>
                        <div id="tab-pdf" class="tab-pane">
                            @include('settings._pdf')
                        </div>
                        <div id="tab-online-payments" class="tab-pane">
                            @include('settings._online_payments')
                        </div>
                        <div id="tab-backup" class="tab-pane">
                            @include('settings._backup')
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {!! Form::close() !!}

    </section>

@stop