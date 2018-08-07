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

    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">@lang('ip.invoice_summary')</h3>

                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-calendar"></i> {{ $invoiceDashboardTotalOptions[config('ip.widgetInvoiceSummaryDashboardTotals')] }}
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            @foreach ($invoiceDashboardTotalOptions as $key => $option)
                                <li>
                                    @if ($key != 'custom_date_range')
                                        <a href="#" onclick="return false;"
                                           class="invoice-dashboard-total-change-option"
                                           data-id="{{ $key }}">{{ $option }}</a>
                                    @else
                                        <a href="#" onclick="return false;" data-toggle="modal"
                                           data-target="#invoice-summary-widget-modal">{{ $option }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <button class="btn btn-box-tool create-invoice"><i
                                class="fa fa-plus"></i> @lang('ip.create_invoice')</button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>{{ $invoicesTotalDraft }}</h3>

                                <p>@lang('ip.draft_invoices')</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-edit"></i>
                            </div>
                            <a href="{{ route('invoices.index') }}?status=draft" class="small-box-footer">
                                @lang('ip.view_draft_invoices') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $invoicesTotalSent }}</h3>

                                <p>@lang('ip.sent_invoices')</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-share"></i>
                            </div>
                            <a class="small-box-footer" href="{{ route('invoices.index') }}?status=sent">
                                @lang('ip.view_sent_invoices') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>{{ $invoicesTotalOverdue }}</h3>

                                <p>@lang('ip.overdue_invoices')</p>
                            </div>
                            <div class="icon"><i class="ion ion-alert"></i></div>
                            <a class="small-box-footer" href="{{ route('invoices.index') }}?status=overdue">
                                @lang('ip.view_overdue_invoices') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>{{ $invoicesTotalPaid }}</h3>

                                <p>@lang('ip.payments_collected')</p>
                            </div>
                            <div class="icon"><i class="ion ion-heart"></i></div>
                            <a class="small-box-footer" href="{{ route('payments.index') }}">
                                @lang('ip.view_payments') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="invoice-summary-widget-modal" tabindex="-1" role="dialog">
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
                        {!! Form::text('setting_widgetInvoiceSummaryDashboardTotalsFromDate', config('ip.widgetInvoiceSummaryDashboardTotalsFromDate'), ['class' => 'form-control', 'id' => 'invoice-dashboard-total-setting-from-date']) !!}
                    </div>

                    <div class="form-group">
                        <label>@lang('ip.to_date') (yyyy-mm-dd):</label>
                        {!! Form::text('setting_widgetInvoiceSummaryDashboardTotalsToDate', config('ip.widgetInvoiceSummaryDashboardTotalsToDate'), ['class' => 'form-control', 'id' => 'invoice-dashboard-total-setting-to-date']) !!}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('ip.cancel')</button>
                    <button type="button" class="btn btn-primary invoice-dashboard-total-change-option"
                            data-id="custom_date_range" data-dismiss="modal">@lang('ip.save')</button>
                </div>
            </div>
        </div>
    </div>
</div>