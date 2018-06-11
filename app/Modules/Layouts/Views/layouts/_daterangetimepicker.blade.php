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
                    customRangeLabel: "@lang('ip.custom')",
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
