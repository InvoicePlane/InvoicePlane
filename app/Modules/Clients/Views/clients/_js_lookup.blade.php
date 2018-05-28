<script type="text/javascript">

    $(function () {

        var clients = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.num);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '{{ route('clients.ajax.lookup') }}' + '?query=%QUERY'
        });

        clients.initialize();

        $('.client-lookup').typeahead(null, {
            minLength: 3,
            source: clients.ttAdapter()
        });

    });

</script>