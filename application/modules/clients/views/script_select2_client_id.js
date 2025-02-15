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
                <?php echo config_item('csrf_token_name'); ?>: Cookies.get('<?php echo config_item('csrf_cookie_name'); ?>')
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    minimumInputLength: 1
});
