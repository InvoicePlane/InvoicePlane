@extends('setup.master')

@section('javascript')
    <script>
        $(function () {
            $('#btn-run-migration').click(function () {

                $('#btn-run-migration').hide();
                $('#btn-running-migration').show();

                $.post('{{ route('setup.postMigration') }}').done(function () {
                    $('#div-exception').hide();
                    $('#btn-running-migration').hide();
                    $('#btn-migration-complete').show();
                }).fail(function (response) {
                    $('#div-exception').show().html($.parseJSON(response.responseText).exception);
                    $('#btn-running-migration').hide();
                    $('#btn-run-migration').show();
                });
            });
        });
    </script>
@endsection

@section('title')
    @lang('ip.database_setup')
@endsection

@section('content')

    <section class="content-header">
        <h1>@lang('ip.database_setup')</h1>
    </section>

    <div class="alert alert-error" id="div-exception" style="display: none;"></div>

    <p>@lang('ip.step_database_setup')</p>

    <button type="button" class="btn btn-primary" id="btn-run-migration">@lang('ip.continue')</button>

    <button type="button" class="btn btn-primary" id="btn-running-migration" style="display: none;" disabled="disabled">
        @lang('ip.installing_please_wait')
    </button>

    <a href="{{ route('setup.account') }}" class="btn btn-success" id="btn-migration-complete" style="display: none;">
        @lang('ip.continue')
    </a>

@endsection
