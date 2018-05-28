<script type="text/javascript">
    $(function() {
        $('.quote_filter_options').change(function () {
            $('form#filter').submit();
        });

        $('#btn-bulk-delete').click(function () {
            var ids = [];

            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });

            if (ids.length > 0) {
                if (!confirm('{!! trans('fi.bulk_delete_record_warning') !!}')) return false;
                $.post("{{ route('quotes.bulk.delete') }}", {
                    ids: ids
                }).done(function() {
                    window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
                });
            }
        });

        $('.bulk-change-status').click(function() {
            var ids = [];

            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });

            if (ids.length > 0) {
                if (!confirm('{!! trans('fi.bulk_quote_change_status_warning') !!}')) return false;
                $.post("{{ route('quotes.bulk.status') }}", {
                    ids: ids,
                    status: $(this).data('status')
                }).done(function() {
                    window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
                });
            }
        });
    });
</script>