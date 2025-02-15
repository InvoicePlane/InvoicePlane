<?php
    $default_client_name = $this->mdl_projects->form_value('client_name', true);
    $default_client_surname = $this->mdl_projects->form_value('client_surname', true);
?>

<script>
    $(function () {
        <?php $this->layout->load_view('clients/script_select2_client_id.js'); ?>
    });
</script>

<form method="post">

    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('projects_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">
        <?php $this->layout->load_view('layout/alerts'); ?>
        <div class="form-group">
            <label for="project_name"><?php _trans('project_name'); ?></label>
            <input type="text" name="project_name" id="project_name" class="form-control"
                   value="<?php echo $this->mdl_projects->form_value('project_name', true); ?>" required>
        </div>
        <div class="form-group">
            <label for="client_id"><?php _trans('client'); ?></label>
            <select name="client_id" id="client_id" class="client-id-select form-control" autofocus="autofocus">
                <?php if(null !== $default_client_name): ?>
                    <option value="<?php echo $default_client_name; ?>">
                        <?php echo $default_client_name . ' ' . $default_client_surname; ?>
                    </option>
                <?php endif; ?>
            </select>
        </div>

    </div>

</form>
