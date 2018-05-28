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
                    customRangeLabel: "{!! trans('fi.custom') !!}",
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
                },
                ranges: {
                    '{{ trans('fi.today') }}': [moment(), moment()],
                    '{{ trans('fi.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '{{ trans('fi.last_7_days') }}': [moment().subtract(6, 'days'), moment()],
                    '{{ trans('fi.last_30_days') }}': [moment().subtract(29, 'days'), moment()],
                    '{{ trans('fi.this_month') }}': [moment().startOf('month'), moment().endOf('month')],
                    '{{ trans('fi.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    '{{ trans('fi.this_year') }}': [moment().startOf('year'), moment().endOf('year')],
                    '{{ trans('fi.last_year') }}': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                    '{{ trans('fi.this_quarter') }}': [moment().startOf('quarter'), moment().endOf('quarter')],
                    '{{ trans('fi.last_quarter') }}': [moment().subtract(1, 'quarter').startOf('quarter'), moment().subtract(1, 'quarter').endOf('quarter')],
                    '{{ trans('fi.first_quarter') }}': [moment().startOf('quarter').quarter(1), moment().endOf('quarter').quarter(1)],
                    '{{ trans('fi.second_quarter') }}': [moment().startOf('quarter').quarter(2), moment().endOf('quarter').quarter(2)],
                    '{{ trans('fi.third_quarter') }}': [moment().startOf('quarter').quarter(3), moment().endOf('quarter').quarter(3)],
                    '{{ trans('fi.fourth_quarter') }}': [moment().startOf('quarter').quarter(4), moment().endOf('quarter').quarter(4)]
                }
            },
            function (start, end) {
                daterangepicker_update_fields(start, end);
            });

        function daterangepicker_update_fields(start, end) {
            $('#from_date').val(start.format('YYYY-MM-DD'));
            $('#to_date').val(end.format('YYYY-MM-DD'));
        }

        daterangepicker_update_fields(startDate, endDate);
    });
</script>
