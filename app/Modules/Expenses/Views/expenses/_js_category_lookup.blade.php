<script>

    $(function () {

        var categories = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.num);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '{{ route('expenses.lookupCategory') }}' + '?query=%QUERY'
        });

        categories.initialize();

        $('.category-lookup').typeahead(null, {
            minLength: 3,
            source: categories.ttAdapter()
        });

    });

</script>
