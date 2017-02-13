<script>
    $(function () {
        // Performs the lookup against current clients in the database
        $('#client_name').keypress(function () {
            var self = $(this);

            $.post('<?php echo site_url('clients/ajax/name_query'); ?>', {
                query: self.val()
            }, function (data) {
                self.data('typeahead').source = eval('(' + data + ')');
            });
        });
    });
</script>
