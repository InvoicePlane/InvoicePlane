<script src='{{ asset('assets/dist/daterangepicker/daterangepicker.js') }}'></script>
<link href="{{ asset('assets/dist/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>

<script type="text/javascript">
  $(function () {
    var startDate = moment().startOf('day');
    var endDate = moment().startOf('day');

    $('#date_time_range').daterangepicker({
        timePicker: true,
        timePickerIncrement: 15,
        autoApply: true,
        startDate: startDate,
        endDate: endDate,
                @if (config('fi.use24HourTimeFormat'))
                timePicker24Hour: true,
                @endif
                locale: {
                    @if (config('fi.use24HourTimeFormat'))
                    format: "{{ strtoupper(config('fi.datepickerFormat')) }} H:mm",
                    @else
                    format: "{{ strtoupper(config('fi.datepickerFormat')) }} h:mm A",
                    @endif
                    customRangeLabel: "{{ trans('ip.custom') }}",
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
                }
      },
      function (start, end) {
        daterangepicker_update_fields(start, end);
      });

    function daterangepicker_update_fields (start, end) {
      $('#from_date_time').val(start.format('YYYY-MM-DD H:mm:ss'));
      $('#to_date_time').val(end.format('YYYY-MM-DD H:mm:ss'));
    }

    daterangepicker_update_fields(startDate, endDate);

    $('.open-daterangetimepicker').click(function () {
      $('#date_time_range').click();
    });
  });
</script>
