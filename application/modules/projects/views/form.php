<script>
    $(function () {
        $("#client_id").select2({
            placeholder: "<?php echo htmlentities(trans('client')); ?>",
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
    });
</script>

<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('projects_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <label for="project_name"><?php _trans('project_name'); ?></label>
            <input type="text" name="project_name" id="project_name" class="form-control"
                   value="<?php echo $this->mdl_projects->form_value('project_name', true); ?>">
        </div>
        <div class="form-group">
            <label for="client_id"><?php _trans('client'); ?></label>
            <select name="client_id" id="client_id" class="form-control" autofocus="autofocus"></select>
        </div>

    </div>

</form>
