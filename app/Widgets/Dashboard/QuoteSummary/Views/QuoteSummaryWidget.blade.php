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

    <div class="card">
        <div class="card-header clearfix">
            <span class="card-title">
                <i class="fa fa-file-text-o mr-2"></i> @lang('ip.quote_summary')
            </span>
            <div class="float-right">
                <span class="text-muted mr-2">{{ $quoteDashboardTotalOptions[config('fi.widgetQuoteSummaryDashboardTotals')] }}</span>
                <div class="dropdown d-inline-block">
                    <span class="clickable dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                         <i class="fa fa-cog"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-right">
                        <h6 class="dropdown-header">
                            <i class="fa fa-calendar mr-2"></i> {{ $quoteDashboardTotalOptions[config('fi.widgetQuoteSummaryDashboardTotals')] }}
                        </h6>
                        @foreach ($quoteDashboardTotalOptions as $key => $option)
                            <li>
                                @if ($key === 'custom_date_range')
                                    <a href="#" onclick="return false;" class="dropdown-item" data-toggle="modal"
                                            data-target="#quote-summary-widget-modal">
                                        {{ $option }}
                                    </a>
                                @else
                                    <a href="#" onclick="return false;"
                                            class="dropdown-item quote-dashboard-total-change-option"
                                            data-id="{{ $key }}">
                                        {{ $option }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body py-2">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="callout border-0 bg-status-draft-light p-3">
                        <h6>@lang('ip.draft_quotes')</h6>
                        <h3>{{ $quotesTotalDraft }}</h3>
                        <a href="{{ route('quotes.index') }}?status=draft" class="small text-dark">
                            @lang('ip.view_draft_quotes') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                    <div class="callout border-0 bg-status-rejected-light p-3">
                        <h6>@lang('ip.rejected_quotes')</h6>
                        <h3>{{ $quotesTotalRejected }}</h3>
                        <a href="{{ route('quotes.index') }}?status=rejected" class="small text-dark">
                            @lang('ip.view_rejected_quotes') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="callout border-0 bg-status-sent-light p-3">
                        <h6>@lang('ip.sent_quotes')</h6>
                        <h3>{{ $quotesTotalSent }}</h3>
                        <a href="{{ route('quotes.index') }}?status=sent" class="small text-dark">
                            @lang('ip.view_sent_quotes') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                    <div class="callout border-0 bg-status-approved-light p-3">
                        <h6>@lang('ip.approved_quotes')</h6>
                        <h3>{{ $quotesTotalApproved }}</h3>
                        <a href="{{ route('quotes.index') }}?status=approved" class="small text-dark">
                            @lang('ip.view_approved_quotes') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="quote-summary-widget-modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('ip.custom_date_range')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="@lang('ip.close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                    <button type="button" class="btn btn-outline-secondary"
                            data-dismiss="modal">@lang('ip.cancel')</button>
                    <button type="button" class="btn btn-success quote-dashboard-total-change-option"
                            data-id="custom_date_range" data-dismiss="modal">@lang('ip.save')</button>
                </div>
            </div>
        </div>
    </div>

</div>
