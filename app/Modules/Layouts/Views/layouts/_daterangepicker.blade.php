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
            "{{ trans('ip.day_short_sunday') }}",
            "{{ trans('ip.day_short_monday') }}",
            "{{ trans('ip.day_short_tuesday') }}",
            "{{ trans('ip.day_short_wednesday') }}",
            "{{ trans('ip.day_short_thursday') }}",
            "{{ trans('ip.day_short_friday') }}",
            "{{ trans('ip.day_short_saturday') }}"
          ],
          monthNames: [
            "{{ trans('ip.month_january') }}",
            "{{ trans('ip.month_february') }}",
            "{{ trans('ip.month_march') }}",
            "{{ trans('ip.month_april') }}",
            "{{ trans('ip.month_may') }}",
            "{{ trans('ip.month_june') }}",
            "{{ trans('ip.month_july') }}",
            "{{ trans('ip.month_august') }}",
            "{{ trans('ip.month_september') }}",
            "{{ trans('ip.month_october') }}",
            "{{ trans('ip.month_november') }}",
            "{{ trans('ip.month_december') }}"
          ],
          firstDay: 1
        },
        ranges: {
          '{{ trans('ip.today') }}': [moment(), moment()],
          '{{ trans('ip.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          '{{ trans('ip.last_7_days') }}': [moment().subtract(6, 'days'), moment()],
          '{{ trans('ip.last_30_days') }}': [moment().subtract(29, 'days'), moment()],
          '{{ trans('ip.this_month') }}': [moment().startOf('month'), moment().endOf('month')],
          '{{ trans('ip.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          '{{ trans('ip.this_year') }}': [moment().startOf('year'), moment().endOf('year')],
          '{{ trans('ip.last_year') }}': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
          '{{ trans('ip.this_quarter') }}': [moment().startOf('quarter'), moment().endOf('quarter')],
          '{{ trans('ip.last_quarter') }}': [moment().subtract(1, 'quarter').startOf('quarter'), moment().subtract(1, 'quarter').endOf('quarter')],
          '{{ trans('ip.first_quarter') }}': [moment().startOf('quarter').quarter(1), moment().endOf('quarter').quarter(1)],
          '{{ trans('ip.second_quarter') }}': [moment().startOf('quarter').quarter(2), moment().endOf('quarter').quarter(2)],
          '{{ trans('ip.third_quarter') }}': [moment().startOf('quarter').quarter(3), moment().endOf('quarter').quarter(3)],
          '{{ trans('ip.fourth_quarter') }}': [moment().startOf('quarter').quarter(4), moment().endOf('quarter').quarter(4)]
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
