<script>
    $(function () {
        $('#btn-change-company_profile').click(function () {
            $('#modal-placeholder').load('{{ route('companyProfiles.ajax.modalLookup') }}', {
                id: {{ $quote->id }},
                update_company_profile_route: '{{ route('quoteEdit.updateCompanyProfile') }}',
                refresh_from_route: '{{ route('quoteEdit.refreshFrom') }}'
            });
        });
    });
</script>
