<script>
    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $(function () {
        $('#filter').keyup(function () {
            delay(function () {
                $.post('<?php echo site_url('filter/ajax/' . $filter_method); ?>',
                    {
                        filter_query: $('#filter').val()
                    }, function (data) {
                        <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        $('#filter_results').html(data);
                    });
            }, 1000);
        });
    });
</script>