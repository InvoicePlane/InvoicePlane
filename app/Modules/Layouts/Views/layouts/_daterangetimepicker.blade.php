<script src='{{ asset('assets/plugins/daterangepicker/moment.js') }}'></script>
<script src='{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}'></script>
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>

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
                    customRangeLabel: "{{ trans('fi.custom') }}",
                    daysOfWeek: [
                        "{{ trans('fi.day_short_sunday') }}",
                        "{{ trans('fi.day_short_monday') }}",
                        "{{ trans('fi.day_short_tuesday') }}",
                        "{{ trans('fi.day_short_wednesday') }}",
                        "{{ trans('fi.day_short_thursday') }}",
                        "{{ trans('fi.day_short_friday') }}",
                        "{{ trans('fi.day_short_saturday') }}"
                    ],
                    monthNames: [
                        "{{ trans('fi.month_january') }}",
                        "{{ trans('fi.month_february') }}",
                        "{{ trans('fi.month_march') }}",
                        "{{ trans('fi.month_april') }}",
                        "{{ trans('fi.month_may') }}",
                        "{{ trans('fi.month_june') }}",
                        "{{ trans('fi.month_july') }}",
                        "{{ trans('fi.month_august') }}",
                        "{{ trans('fi.month_september') }}",
                        "{{ trans('fi.month_october') }}",
                        "{{ trans('fi.month_november') }}",
                        "{{ trans('fi.month_december') }}"
                    ],
                    firstDay: 1
                }
            },
            function (start, end) {
                daterangepicker_update_fields(start, end);
            });

        function daterangepicker_update_fields(start, end) {
            $('#from_date_time').val(start.format('YYYY-MM-DD H:mm:ss'));
            $('#to_date_time').val(end.format('YYYY-MM-DD H:mm:ss'));
        }

        daterangepicker_update_fields(startDate, endDate);

        $('.open-daterangetimepicker').click(function() {
            $('#date_time_range').click();
        });
    });
</script>