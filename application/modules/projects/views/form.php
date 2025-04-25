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

        <div class="form-group has-feedback">
            <label for="client_id"><?php _trans('client'); ?></label>
            <div class="input-group">
                <span id="toggle_permissive_search_clients" class="input-group-addon" title="<?php _trans('enable_permissive_search_clients'); ?>" style="cursor:pointer;">
                    <i class="fa fa-toggle-<?php echo get_setting('enable_permissive_search_clients') ? 'on' : 'off' ?> fa-fw" ></i>
                </span>
                <select name="client_id" id="client_id" class="client-id-select form-control" autofocus="autofocus">
<?php
$permissive = get_setting('enable_permissive_search_users');
if (! empty($project->client_id))
{
?>
                    <option value="<?php echo $project->client_id; ?>"><?php _htmlsc(format_client($project)); ?></option>
<?php
}
?>
                </select>
            </div>
        </div>

        <input class="hidden" id="input_permissive_search_clients"
               value="<?php echo get_setting('enable_permissive_search_clients'); ?>">
    </div>

</form>
