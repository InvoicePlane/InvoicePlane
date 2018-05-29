<script type="text/javascript">
  $(function () {
    $('#btn-change-company-profile').click(function () {
      $('#modal-placeholder').load('{{ route('companyProfiles.ajax.modalLookup') }}', {
        id: {{ $invoice->id }},
        update_company_profile_route: '{{ route('invoiceEdit.updateCompanyProfile') }}',
        refresh_from_route: '{{ route('invoiceEdit.refreshFrom') }}'
      });
    });
  });
</script>