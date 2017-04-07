$(".client-id-select").select2({
    placeholder: "<?php _trans('client'); ?>",
    ajax: {
        url: "<?php echo site_url('clients/ajax/name_query'); ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term,
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
    escapeMarkup: function (markup) {
        return markup;
    },
    minimumInputLength: 2
});
