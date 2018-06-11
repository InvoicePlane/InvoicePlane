@include('layouts._datepicker')

<div id="quote-dashboard-totals-widget">
    <script type="text/javascript">
      $(function () {
        $('.quote-dashboard-total-change-option').click(function () {
          var option = $(this).data('id');

          $.post("{{ route('widgets.dashboard.quoteSummary.renderPartial') }}", {
            widgetQuoteSummaryDashboardTotals: option,
            widgetQuoteSummaryDashboardTotalsFromDate: $('#quote-dashboard-total-setting-from-date').val(),
            widgetQuoteSummaryDashboardTotalsToDate: $('#quote-dashboard-total-setting-to-date').val()
          }, function (data) {
            $('#quote-dashboard-totals-widget').html(data);
          });

        });

        $('#quote-dashboard-total-setting-from-date').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true
        });
        $('#quote-dashboard-total-setting-to-date').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true
        });
      });
    </script>

    <section class="content">

        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">@lang('ip.quote_summary')</h3>
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-calendar"></i> {{ $quoteDashboardTotalOptions[config('fi.widgetQuoteSummaryDashboardTotals')] }}
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            @foreach ($quoteDashboardTotalOptions as $key => $option)
                                <li>
                                    @if ($key != 'custom_date_range')
                                        <a href="#" onclick="return false;" class="quote-dashboard-total-change-option"
                                           data-id="{{ $key }}">{{ $option }}</a>
                                    @else
                                        <a href="#" onclick="return false;" data-toggle="modal"
                                           data-target="#quote-summary-widget-modal">{{ $option }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <button class="btn btn-box-tool create-quote"><i
                                class="fa fa-plus"></i> @lang('ip.create_quote')</button>
                </div>
            </div>
            <div class="box-body">

                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>{{ $quotesTotalDraft }}</h3>

                                <p>@lang('ip.draft_quotes')</p>
                            </div>
                            <div class="icon"><i class="ion ion-edit"></i></div>
                            <a class="small-box-footer" href="{{ route('quotes.index') }}?status=draft">
                                @lang('ip.view_draft_quotes') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="small-box bg-olive">
                            <div class="inner">
                                <h3>{{ $quotesTotalSent }}</h3>

                                <p>@lang('ip.sent_quotes')</p>
                            </div>
                            <div class="icon"><i class="ion ion-share"></i></div>
                            <a class="small-box-footer" href="{{ route('quotes.index') }}?status=sent">
                                @lang('ip.view_sent_quotes') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3>{{ $quotesTotalRejected }}</h3>

                                <p>@lang('ip.rejected_quotes')</p>
                            </div>
                            <div class="icon"><i class="ion ion-thumbsdown"></i></div>
                            <a class="small-box-footer" href="{{ route('quotes.index') }}?status=rejected">
                                @lang('ip.view_rejected_quotes') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>{{ $quotesTotalApproved }}</h3>

                                <p>@lang('ip.approved_quotes')</p>
                            </div>
                            <div class="icon"><i class="ion ion-thumbsup"></i></div>
                            <a class="small-box-footer" href="{{ route('quotes.index') }}?status=approved">
                                @lang('ip.view_approved_quotes') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <div class="modal fade" id="quote-summary-widget-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">@lang('ip.custom_date_range')</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>@lang('ip.from_date') (yyyy-mm-dd):</label>
                        {!! Form::text('setting_widgetQuoteSummaryDashboardTotalsFromDate', config('fi.widgetQuoteSummaryDashboardTotalsFromDate'), ['class' => 'form-control', 'id' => 'quote-dashboard-total-setting-from-date']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.to_date') (yyyy-mm-dd):</label>
                        {!! Form::text('setting_widgetQuoteSummaryDashboardTotalsToDate', config('fi.widgetQuoteSummaryDashboardTotalsToDate'), ['class' => 'form-control', 'id' => 'quote-dashboard-total-setting-to-date']) !!}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('ip.cancel')</button>
                    <button type="button" class="btn btn-primary quote-dashboard-total-change-option"
                            data-id="custom_date_range" data-dismiss="modal">@lang('ip.save')</button>
                </div>
            </div>
        </div>
    </div>
</div>