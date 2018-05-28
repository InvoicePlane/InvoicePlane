<script type="text/javascript">

    $(function () {

        // Set up the item lookups data source
        var itemLookups = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.num);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '{{ route('itemLookups.ajax.itemLookup') }}' + '?query=%QUERY'
        });

        // Initialize the item lookups data source
        itemLookups.initialize();

        // Define the typeahead settings
        var settings = {
            displayKey: 'name',
            minLength: 3,
            source: itemLookups.ttAdapter()
        };

        // Make all existing items typeaheads
        $('.item-lookup').typeahead(null, settings);

        // All existing items should populate proper fields
        typeaheadTrigger();

        // Clones a new item row
        function cloneItemRow() {
            var row = $('#new-item').clone().appendTo('#item-table');
            row.removeAttr('id').addClass('item').show();
            row.find('input[name="name"]').addClass('item-lookup').typeahead(null, settings);
            typeaheadTrigger();
            $('textarea').autosize();
        }

        // Sets up .item-lookup to populate proper fields when item is selected
        function typeaheadTrigger() {
            $('.item-lookup').on('typeahead:selected typeahead:autocompleted', function (obj, item, name) {
                var row = $(this).closest('tr');
                row.find('textarea[name="description"]').val(item.description);
                row.find('input[name="quantity"]').val('1');
                row.find('input[name="price"]').val(item.price);
                row.find('select[name="tax_rate_id"]').val(item.tax_rate_id);
                row.find('select[name="tax_rate_2_id"]').val(item.tax_rate_2_id);
            });
        }

        $(document).on('click', '#btn-add-item', function () {
            cloneItemRow();
        });

        // Add a new item row if no items currently exist
        @if (!$itemCount)
        cloneItemRow();
        @endif


    });

</script>