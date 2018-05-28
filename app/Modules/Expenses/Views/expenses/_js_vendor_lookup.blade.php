<script type="text/javascript">

    $(function () {

        var vendors = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.num);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '{{ route('expenses.lookupVendor') }}' + '?query=%QUERY'
        });

        vendors.initialize();

        $('.vendor-lookup').typeahead(null, {
            minLength: 3,
            source: vendors.ttAdapter()
        });

    });

</script>