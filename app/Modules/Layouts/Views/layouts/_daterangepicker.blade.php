<script src='{{ asset('assets/dist/daterangepicker/daterangepicker.js') }}'></script>
<link href="{{ asset('assets/dist/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>

<script type="text/javascript">
  $(function () {
    var startDate = moment().startOf('month');
    var endDate = moment().endOf('month');

    $('#date_range').daterangepicker({
        autoApply: true,
        startDate: startDate,
        endDate: endDate,
        locale: {
          format: "{{ strtoupper(config('fi.datepickerFormat')) }}",
          customRangeLabel: "{!! trans('ip.custom') !!}",
          daysOfWeek: [
            "@lang('ip.day_short_sunday')",
            "@lang('ip.day_short_monday')",
            "@lang('ip.day_short_tuesday')",
            "@lang('ip.day_short_wednesday')",
            "@lang('ip.day_short_thursday')",
            "@lang('ip.day_short_friday')",
            "@lang('ip.day_short_saturday')"
          ],
          monthNames: [
            "@lang('ip.month_january')",
            "@lang('ip.month_february')",
            "@lang('ip.month_march')",
            "@lang('ip.month_april')",
            "@lang('ip.month_may')",
            "@lang('ip.month_june')",
            "@lang('ip.month_july')",
            "@lang('ip.month_august')",
            "@lang('ip.month_september')",
            "@lang('ip.month_october')",
            "@lang('ip.month_november')",
            "@lang('ip.month_december')"
          ],
          firstDay: 1
        },
        ranges: {
          '@lang('ip.today')': [moment(), moment()],
          '@lang('ip.yesterday')': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          '{{ trans('ip.last_7_days') }}': [moment().subtract(6, 'days'), moment()],
          '{{ trans('ip.last_30_days') }}': [moment().subtract(29, 'days'), moment()],
          '@lang('ip.this_month')': [moment().startOf('month'), moment().endOf('month')],
          '@lang('ip.last_month')': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          '@lang('ip.this_year')': [moment().startOf('year'), moment().endOf('year')],
          '@lang('ip.last_year')': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
          '@lang('ip.this_quarter')': [moment().startOf('quarter'), moment().endOf('quarter')],
          '@lang('ip.last_quarter')': [moment().subtract(1, 'quarter').startOf('quarter'), moment().subtract(1, 'quarter').endOf('quarter')],
          '@lang('ip.first_quarter')': [moment().startOf('quarter').quarter(1), moment().endOf('quarter').quarter(1)],
          '@lang('ip.second_quarter')': [moment().startOf('quarter').quarter(2), moment().endOf('quarter').quarter(2)],
          '@lang('ip.third_quarter')': [moment().startOf('quarter').quarter(3), moment().endOf('quarter').quarter(3)],
          '@lang('ip.fourth_quarter')': [moment().startOf('quarter').quarter(4), moment().endOf('quarter').quarter(4)]
        }
      },
      function (start, end) {
        daterangepicker_update_fields(start, end);
      });

    function daterangepicker_update_fields (start, end) {
      $('#from_date').val(start.format('YYYY-MM-DD'));
      $('#to_date').val(end.format('YYYY-MM-DD'));
    }

    daterangepicker_update_fields(startDate, endDate);
  });
</script>
