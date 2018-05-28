@extends('layouts.master')

@section('javascript')

    <script type="text/javascript">
        $(function () {

            $('#btn-run-report').click(function () {

                var company_profile_id = $('#company_profile_id').val();
                var year = $('#year').val();

                $.post("{{ route('reports.revenueByClient.validate') }}", {
                    company_profile_id: company_profile_id,
                    year: year
                }).done(function () {
                    clearErrors();
                    $('#form-validation-placeholder').html('');
                    var output_type = $("input[name=output_type]:checked").val();
                    query_string = "?company_profile_id=" + company_profile_id + "&year=" + year;
                    if (output_type == 'preview') {
                        $('#preview').show();
                        $('#preview-results').attr('src', "{{ route('reports.revenueByClient.html') }}" + query_string);
                    }
                    else if (output_type == 'pdf') {
                        window.location.href = "{{ route('reports.revenueByClient.pdf') }}" + query_string;
                    }
                }).fail(function (response) {
                    showErrors($.parseJSON(response.responseText).errors, '#form-validation-placeholder');
                });
            });
        });
    </script>

@stop

@section('content')

    <section class="content-header">
        <h1 class="pull-left">{{ trans('fi.revenue_by_client') }}</h1>

        <div class="pull-right">
            <button class="btn btn-primary" id="btn-run-report">{{ trans('fi.run_report') }}</button>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        <div id="form-validation-placeholder"></div>

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('fi.options') }}</h3>
                    </div>
                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('fi.company_profile') }}:</label>
                                    {!! Form::select('company_profile_id', $companyProfiles, null, ['id' => 'company_profile_id', 'class' => 'form-control'])  !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('fi.year') }}:</label>
                                        {!! Form::select('year', $years, date('Y'), ['id' => 'year', 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>{{ trans('fi.output_type') }}:</label><br>
                                    <label class="radio-inline">
                                        <input type="radio" name="output_type" value="preview"
                                               checked="checked"> {{ trans('fi.preview') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="output_type"
                                               value="pdf"> {{ trans('fi.pdf') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="row" id="preview"
             style="height: 100%; background-color: #e6e6e6; padding: 25px; margin: 0; display: none;">
            <div class="col-lg-10 col-lg-offset-1" style="background-color: white;">
                <iframe src="about:blank" id="preview-results" frameborder="0" style="width: 100%;" scrolling="no"
                        onload="resizeIframe(this, 500);"></iframe>
            </div>
        </div>

    </section>

@stop