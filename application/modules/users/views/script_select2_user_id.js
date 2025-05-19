$(".user-id-select").select2({
    placeholder: "<?php _trans('user'); ?>",
    ajax: {
        url: "<?php echo site_url('users/ajax/name_query'); ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term,
                permissive_search_users: $('input#input_permissive_search_users').val(),
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

// Toggle on/off permissive search on users names
$('#toggle_permissive_search_users').click(function () {
    if ($('input#input_permissive_search_users').val() == ('1')) {
        $.get("<?php echo site_url('users/ajax/save_preference_permissive_search_users'); ?>", {
            permissive_search_users: '0'
        });
        $('input#input_permissive_search_users').val('0');
        $('span#toggle_permissive_search_users i').removeClass('fa-toggle-on');
        $('span#toggle_permissive_search_users i').addClass('fa-toggle-off');
    } else {
        $.get("<?php echo site_url('users/ajax/save_preference_permissive_search_users'); ?>", {
            permissive_search_users: '1'
        });
        $('input#input_permissive_search_users').val('1');
        $('span#toggle_permissive_search_users i').removeClass('fa-toggle-off');
        $('span#toggle_permissive_search_users i').addClass('fa-toggle-on');
    }
});
