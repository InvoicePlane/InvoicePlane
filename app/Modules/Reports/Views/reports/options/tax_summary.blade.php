@extends('layouts.master')

@section('javascript')

    @include('layouts._daterangepicker')

    <script type="text/javascript">
      $(function () {
        $('#btn-run-report').click(function () {

          var from_date = $('#from_date').val();
          var to_date = $('#to_date').val();
          var company_profile_id = $('#company_profile_id').val();
          var exclude_unpaid_invoices = $('#exclude_unpaid_invoices').val();

          $.post("{{ route('reports.taxSummary.validate') }}", {
            from_date: from_date,
            to_date: to_date,
            company_profile_id: company_profile_id,
            exclude_unpaid_invoices: exclude_unpaid_invoices
          }).done(function () {
            clearErrors();
            $('#form-validation-placeholder').html('');
            output_type = $('input[name=output_type]:checked').val();
            query_string = '?from_date=' + from_date + '&to_date=' + to_date + '&company_profile_id=' + company_profile_id + '&exclude_unpaid_invoices=' + exclude_unpaid_invoices;
            if (output_type == 'preview') {
              $('#preview').show();
              $('#preview-results').attr('src', "{{ route('reports.taxSummary.html') }}" + query_string);
            }
            else if (output_type == 'pdf') {
              window.location.href = "{{ route('reports.taxSummary.pdf') }}" + query_string;
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
        <h1 class="pull-left">{{ trans('ip.tax_summary') }}</h1>

        <div class="pull-right">
            <button class="btn btn-primary" id="btn-run-report">{{ trans('ip.run_report') }}</button>
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        <div id="form-validation-placeholder"></div>

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('ip.options') }}</h3>
                    </div>
                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.company_profile') }}:</label>
                                    {!! Form::select('company_profile_id', $companyProfiles, null, ['id' => 'company_profile_id', 'class' => 'form-control'])  !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.date_range') }}:</label>
                                    {!! Form::hidden('from_date', null, ['id' => 'from_date']) !!}
                                    {!! Form::hidden('to_date', null, ['id' => 'to_date']) !!}
                                    {!! Form::text('date_range', null, ['id' => 'date_range', 'class' => 'form-control', 'readonly' => 'readonly']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('ip.exclude_unpaid_invoices') }}:</label>
                                    {!! Form::select('exclude_unpaid_invoices', ['0' => trans('ip.no'), '1' => trans('ip.yes')], null, ['id' => 'exclude_unpaid_invoices', 'class' => 'form-control'])  !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>{{ trans('ip.output_type') }}:</label><br>
                                    <label class="radio-inline">
                                        <input type="radio" name="output_type" value="preview"
                                               checked="checked"> {{ trans('ip.preview') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="output_type" value="pdf"> {{ trans('ip.pdf') }}
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
            <div class="col-lg-8 col-lg-offset-2" style="background-color: white;">
                <iframe src="about:blank" id="preview-results" frameborder="0" style="width: 100%;" scrolling="no"
                        onload="resizeIframe(this, 500);"></iframe>
            </div>
        </div>

    </section>

@stop