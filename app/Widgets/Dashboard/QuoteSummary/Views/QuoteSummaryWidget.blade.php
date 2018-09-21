@include('layouts._datepicker')

<div id="quote-dashboard-totals-widget">
    <script>
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

    <section class="widget-content">

        <div class="card">
            <div class="card-header d-sm-flex flex-wrap align-items-center justify-content-between">

                <h5>@lang('ip.quote_summary')</h5>

                <div class="widget-actions mt-2 mt-sm-0">
                    <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-calendar"></i> {{ $quoteDashboardTotalOptions[config('ip.widgetQuoteSummaryDashboardTotals')] }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach ($quoteDashboardTotalOptions as $key => $option)
                            @if ($key !== 'custom_date_range')
                                <a href="#" class="dropdown-item small quote-dashboard-total-change-option"
                                        onclick="return false;" data-id="{{ $key }}">
                                    {{ $option }}
                                </a>
                            @else
                                <a href="#" onclick="return false;" class="dropdown-item small"
                                        data-toggle="modal" data-target="#quote-summary-widget-modal">
                                    {{ $option }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    <button class="btn btn-sm create-quote" data-toggle="tooltip" title="@lang('ip.create_quote')">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-12 col-xl-6">
                        <div class="card card-body bg-status-draft-light">
                            <div class="mb-1">
                                <h3 class="mb-3">{{ $quotesTotalDraft }}</h3>
                                <span>@lang('ip.draft_quotes')</span>
                            </div>
                            <a class="text-light small" href="{{ route('quotes.index') }}?status=draft">
                                @lang('ip.view_draft_quotes') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-12 col-xl-6 mt-3 mt-sm-0 mt-md-3 mt-xl-0">
                        <div class="card card-body bg-status-sent">
                            <div class="mb-1">
                                <h3 class="mb-3">{{ $quotesTotalSent }}</h3>
                                <span>@lang('ip.sent_quotes')</span>
                            </div>
                            <a class="text-light small" href="{{ route('quotes.index') }}?status=sent">
                                @lang('ip.view_sent_quotes') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row mt-sm-3 mt-md-0 mt-xl-3">
                    <div class="col-12 col-sm-6 col-md-12 col-xl-6 mt-3 mt-sm-0 mt-md-3 mt-xl-0">
                        <div class="card card-body bg-status-rejected">
                            <div class="mb-1">
                                <h3 class="mb-3">{{ $quotesTotalRejected }}</h3>
                                <span>@lang('ip.rejected_quotes')</span>
                            </div>
                            <a class="text-light small" href="{{ route('quotes.index') }}?status=rejected">
                                @lang('ip.view_rejected_quotes') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-12 col-xl-6 mt-3 mt-sm-0 mt-md-3 mt-xl-0">
                        <div class="card card-body bg-status-approved">
                            <div class="mb-1">
                                <h3 class="mb-3">{{ $quotesTotalApproved }}</h3>
                                <span>@lang('ip.approved_quotes')</span>
                            </div>
                            <a class="text-light small" href="{{ route('quotes.index') }}?status=approved">
                                @lang('ip.view_approved_quotes') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>

    <div id="quote-summary-widget-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('ip.custom_date_range')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>@lang('ip.from_date') (yyyy-mm-dd):</label>
                        {!! Form::text('setting_widgetQuoteSummaryDashboardTotalsFromDate', config('ip.widgetQuoteSummaryDashboardTotalsFromDate'), ['class' => 'form-control', 'id' => 'quote-dashboard-total-setting-from-date']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.to_date') (yyyy-mm-dd):</label>
                        {!! Form::text('setting_widgetQuoteSummaryDashboardTotalsToDate', config('ip.widgetQuoteSummaryDashboardTotalsToDate'), ['class' => 'form-control', 'id' => 'quote-dashboard-total-setting-to-date']) !!}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        @lang('ip.cancel')
                    </button>
                    <button type="button" class="btn btn-primary quote-dashboard-total-change-option"
                            data-id="custom_date_range" data-dismiss="modal">
                        @lang('ip.save')
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>