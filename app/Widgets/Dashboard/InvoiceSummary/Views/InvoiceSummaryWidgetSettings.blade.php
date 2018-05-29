@include('layouts._datepicker')

<script type="text/javascript">
  $(function () {
    $('#invoice-dashboard-total-setting-from-date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    });
    $('#invoice-dashboard-total-setting-to-date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    });

    $('#invoice-dashboard-total-setting').change(function () {
      toggleWidgetInvoiceDashboardTotalsDateRange($('#invoice-dashboard-total-setting').val());
    });

    function toggleWidgetInvoiceDashboardTotalsDateRange (val) {
      if (val == 'custom_date_range') {
        $('#div-invoice-dashboard-totals-date-range').show();
      }
      else {
        $('#div-invoice-dashboard-totals-date-range').hide();
      }
    }

    toggleWidgetInvoiceDashboardTotalsDateRange($('#invoice-dashboard-total-setting').val());
  });
</script>

<div class="form-group">
    <label>{{ trans('fi.dashboard_totals_option') }}: </label>
    {!! Form::select('setting[widgetInvoiceSummaryDashboardTotals]', $dashboardTotalOptions, config('fi.widgetInvoiceSummaryDashboardTotals'), ['class' => 'form-control', 'id' => 'invoice-dashboard-total-setting']) !!}
</div>

<div class="row" id="div-invoice-dashboard-totals-date-range">
    <div class="col-md-2">
        <label>{{ trans('fi.from_date') }} (yyyy-mm-dd):</label>
        {!! Form::text('setting[widgetInvoiceSummaryDashboardTotalsFromDate]', config('fi.widgetInvoiceSummaryDashboardTotalsFromDate'), ['class' => 'form-control', 'id' => 'invoice-dashboard-total-setting-from-date']) !!}
    </div>
    <div class="col-md-2">
        <label>{{ trans('fi.to_date') }} (yyyy-mm-dd):</label>
        {!! Form::text('setting[widgetInvoiceSummaryDashboardTotalsToDate]', config('fi.widgetInvoiceSummaryDashboardTotalsToDate'), ['class' => 'form-control', 'id' => 'invoice-dashboard-total-setting-to-date']) !!}
    </div>
</div>