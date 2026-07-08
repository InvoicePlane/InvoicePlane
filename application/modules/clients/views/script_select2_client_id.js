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
                [csrf_token_name]: csrf_token_value // dynamic property https://stackoverflow.com/a/35579786
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

// Toggle on/off permissive search on clients names
$('#toggle_permissive_search_clients').click(function () {
    if ($('input#input_permissive_search_clients').val() == ('1')) {
        $.get("<?php echo site_url('clients/ajax/save_preference_permissive_search_clients'); ?>", {
            permissive_search_clients: '0'
        });
        $('input#input_permissive_search_clients').val('0');
        $('span#toggle_permissive_search_clients i').removeClass('fa-toggle-on');
        $('span#toggle_permissive_search_clients i').addClass('fa-toggle-off');
    } else {
        $.get("<?php echo site_url('clients/ajax/save_preference_permissive_search_clients'); ?>", {
            permissive_search_clients: '1'
        });
        $('input#input_permissive_search_clients').val('1');
        $('span#toggle_permissive_search_clients i').removeClass('fa-toggle-off');
        $('span#toggle_permissive_search_clients i').addClass('fa-toggle-on');
    }
});
