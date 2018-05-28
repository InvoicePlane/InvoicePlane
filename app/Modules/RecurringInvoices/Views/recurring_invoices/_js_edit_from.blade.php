<script type="text/javascript">
    $(function () {
        $('#btn-change-company-profile').click(function () {
            $('#modal-placeholder').load('{{ route('companyProfiles.ajax.modalLookup') }}', {
                id: {{ $recurringInvoice->id }},
                update_company_profile_route: '{{ route('recurringInvoiceEdit.updateCompanyProfile') }}',
                refresh_from_route: '{{ route('recurringInvoiceEdit.refreshFrom') }}'
            });
        });
    });
</script>