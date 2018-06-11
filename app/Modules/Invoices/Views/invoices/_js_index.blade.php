<script type="text/javascript">
  $(function () {
    $('.invoice_filter_options').change(function () {
      $('form#filter').submit();
    });

    $('#btn-bulk-delete').click(function () {

      var ids = [];

      $('.bulk-record:checked').each(function () {
        ids.push($(this).data('id'));
      });

      if (ids.length > 0) {
        if (!confirm('{!! trans('ip.bulk_delete_record_warning') !!}')) return false;
        $.post("{{ route('invoices.bulk.delete') }}", {
          ids: ids
        }).done(function () {
          window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
        });
      }
    });

    $('.bulk-change-status').click(function () {
      var ids = [];

      $('.bulk-record:checked').each(function () {
        ids.push($(this).data('id'));
      });

      if (ids.length > 0) {
        if (!confirm('{!! trans('ip.bulk_invoice_change_status_warning') !!}')) return false;
        $.post("{{ route('invoices.bulk.status') }}", {
          ids: ids,
          status: $(this).data('status')
        }).done(function () {
          window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
        });
      }
    });
  });
</script>