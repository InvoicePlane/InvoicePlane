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

    <section class="widget-content">

        <div class="card">
            <div class="card-header d-sm-flex flex-wrap align-items-center justify-content-between ">

                <h5>@lang('ip.invoice_summary')</h5>

                <div class="widget-actions mt-2 mt-sm-0">
                    <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-calendar"></i> {{ $invoiceDashboardTotalOptions[config('ip.widgetInvoiceSummaryDashboardTotals')] }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach ($invoiceDashboardTotalOptions as $key => $option)
                            @if ($key !== 'custom_date_range')
                                <a href="#" class="dropdown-item small invoice-dashboard-total-change-option"
                                        onclick="return false;" data-id="{{ $key }}">
                                    {{ $option }}
                                </a>
                            @else
                                <a href="#" onclick="return false;" class="dropdown-item small"
                                        data-toggle="modal" data-target="#invoice-summary-widget-modal">
                                    {{ $option }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    <button class="btn btn-sm create-invoice" data-toggle="tooltip" title="@lang('ip.create_invoice')">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>

            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-12 col-xl-6">
                        <div class="card card-body bg-status-draft-light">
                            <div class="mb-1">
                                <h3 class="mb-3">{{ $invoicesTotalDraft }}</h3>
                                <span>@lang('ip.draft_quotes')</span>
                            </div>
                            <a class="text-light small" href="{{ route('invoices.index') }}?status=draft">
                                @lang('ip.view_draft_invoices') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-12 col-xl-6 mt-3 mt-sm-0 mt-md-3 mt-xl-0">
                        <div class="card card-body bg-status-sent">
                            <div class="mb-1">
                                <h3 class="mb-3">{{ $invoicesTotalSent }}</h3>
                                <span>@lang('ip.sent_invoices')</span>
                            </div>
                            <a class="text-light small" href="{{ route('invoices.index') }}?status=sent">
                                @lang('ip.view_sent_invoices') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row mt-sm-3 mt-md-0 mt-xl-3">
                    <div class="col-12 col-sm-6 col-md-12 col-xl-6 mt-3 mt-sm-0 mt-md-3 mt-xl-0">
                        <div class="card card-body bg-status-rejected">
                            <div class="mb-1">
                                <h3 class="mb-3">{{ $invoicesTotalOverdue }}</h3>
                                <span>@lang('ip.overdue_invoices')</span>
                            </div>
                            <a class="text-light small" href="{{ route('invoices.index') }}?status=overdue">
                                @lang('ip.view_overdue_invoices') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-12 col-xl-6 mt-3 mt-sm-0 mt-md-3 mt-xl-0">
                        <div class="card card-body bg-status-approved">
                            <div class="mb-1">
                                <h3 class="mb-3">{{ $invoicesTotalPaid }}</h3>
                                <span>@lang('ip.payments_collected')</span>
                            </div>
                            <a class="text-light small" href="{{ route('payments.index') }}">
                                @lang('ip.view_payments') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>

    <div id="invoice-summary-widget-modal" class="modal fade" tabindex="-1" role="dialog">
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
                        {!! Form::text('setting_widgetInvoiceSummaryDashboardTotalsFromDate',
                            config('ip.widgetInvoiceSummaryDashboardTotalsFromDate'), [
                            'class' => 'form-control',
                            'id' => 'invoice-dashboard-total-setting-from-date',
                        ]) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.to_date') (yyyy-mm-dd):</label>
                        {!! Form::text('setting_widgetInvoiceSummaryDashboardTotalsToDate',
                            config('ip.widgetInvoiceSummaryDashboardTotalsToDate'), [
                            'class' => 'form-control',
                            'id' => 'invoice-dashboard-total-setting-to-date',
                        ]) !!}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        @lang('ip.cancel')
                    </button>
                    <button type="button" class="btn btn-primary invoice-dashboard-total-change-option"
                            data-id="custom_date_range" data-dismiss="modal">
                        @lang('ip.save')
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>