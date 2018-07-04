@include('layouts._datepicker')


<div id="invoice-dashboard-totals-widget">
    <script type="text/javascript">
      $(function () {
        $('.invoice-dashboard-total-change-option').click(function () {
          var option = $(this).data('id');

          $.post("{{ route('widgets.dashboard.invoiceSummary.renderPartial') }}", {
            widgetInvoiceSummaryDashboardTotals: option,
            widgetInvoiceSummaryDashboardTotalsFromDate: $('#invoice-dashboard-total-setting-from-date').val(),
            widgetInvoiceSummaryDashboardTotalsToDate: $('#invoice-dashboard-total-setting-to-date').val()
          }, function (data) {
            $('#invoice-dashboard-totals-widget').html(data);
          });

        });

        $('#invoice-dashboard-total-setting-from-date').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true
        });
        $('#invoice-dashboard-total-setting-to-date').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true
        });
      });
    </script>

    <div class="card">
        <div class="card-header clearfix">
            <span class="card-title">
                <i class="fa fa-file-text mr-2"></i> @lang('ip.invoice_summary')
            </span>
            <div class="float-right">
                <span class="text-muted mr-2">{{ $invoiceDashboardTotalOptions[config('fi.widgetInvoiceSummaryDashboardTotals')] }}</span>
                <div class="dropdown d-inline-block">
                    <span class="clickable dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                         <i class="fa fa-cog"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-right">
                        <h6 class="dropdown-header">
                            <i class="fa fa-calendar mr-2"></i> {{ $invoiceDashboardTotalOptions[config('fi.widgetInvoiceSummaryDashboardTotals')] }}
                        </h6>
                        @foreach ($invoiceDashboardTotalOptions as $key => $option)
                            <li>
                                @if ($key === 'custom_date_range')
                                    <a href="#" onclick="return false;" class="dropdown-item" data-toggle="modal"
                                            data-target="#invoice-summary-widget-modal">
                                        {{ $option }}
                                    </a>
                                @else
                                    <a href="#" onclick="return false;"
                                            class="dropdown-item invoice-dashboard-total-change-option"
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
                        <h6>@lang('ip.draft_invoices')</h6>
                        <h3>{{ $invoicesTotalDraft }}</h3>
                        <a href="{{ route('invoices.index') }}?status=draft" class="small text-dark">
                            @lang('ip.view_draft_invoices') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                    <div class="callout border-0 bg-status-overdue-light p-3">
                        <h6>@lang('ip.overdue_invoices')</h6>
                        <h3>{{ $invoicesTotalOverdue }}</h3>
                        <a href="{{ route('invoices.index') }}?status=overdue" class="small text-dark">
                            @lang('ip.view_overdue_invoices') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="callout border-0 bg-status-sent-light p-3">
                        <h6>@lang('ip.sent_invoices')</h6>
                        <h3>{{ $invoicesTotalSent }}</h3>
                        <a href="{{ route('invoices.index') }}?status=sent" class="small text-dark">
                            @lang('ip.view_sent_invoices') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                    <div class="callout border-0 bg-status-paid-light p-3">
                        <h6>@lang('ip.payments_collected')</h6>
                        <h3>{{ $invoicesTotalPaid }}</h3>
                        <a href="{{ route('payments.index') }}" class="small text-dark">
                            @lang('ip.view_payments') <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="invoice-summary-widget-modal" class="modal" tabindex="-1" role="dialog">
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
                        {!! Form::text('setting_widgetInvoiceSummaryDashboardTotalsFromDate', config('fi.widgetInvoiceSummaryDashboardTotalsFromDate'), ['class' => 'form-control', 'id' => 'invoice-dashboard-total-setting-from-date']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.to_date') (yyyy-mm-dd):</label>
                        {!! Form::text('setting_widgetInvoiceSummaryDashboardTotalsToDate', config('fi.widgetInvoiceSummaryDashboardTotalsToDate'), ['class' => 'form-control', 'id' => 'invoice-dashboard-total-setting-to-date']) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">@lang('ip.cancel')</button>
                    <button type="button" class="btn btn-success invoice-dashboard-total-change-option"
                            data-id="custom_date_range" data-dismiss="modal">@lang('ip.save')</button>
                </div>
            </div>
        </div>
    </div>

</div>