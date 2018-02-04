$(".client-id-select").select2({
    placeholder: "<?php _trans('client'); ?>",
    ajax: {
        url: "<?php echo site_url('clients/ajax/name_query'); ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term,
                permissive_search_clients: $('input#input_permissive_search_clients').val(),
                page: params.page,
                _ip_csrf: Cookies.get('ip_csrf_cookie')
            };
        },
        processResults: function (data) {
            console.log(data);
            return {
                results: data
            };
        },
        cache: true
    },
    initSelection: function (element, callback) {
        $.ajax("<?php echo site_url('clients/ajax/get_latest'); ?>", {
            dataType: 'json'
        }).done(function(data) {
            <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
            callback(data);
        });
    },
    minimumInputLength: 1
});
