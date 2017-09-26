<form method="post">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('warehouses_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <label for="warehouse_name"><?php _trans('warehouse_name'); ?></label>
            <input type="text" name="warehouse_name" id="warehouse_name" class="form-control"
                   value="<?php echo $this->mdl_warehouses->form_value('warehouse_name', true); ?>">
        </div>
        <div class="form-group">
            <label for="warehouse_location"><?php _trans('warehouse_location'); ?></label>
            <input type="text" name="warehouse_location" id="warehouse_location" class="form-control"
                   value="<?php echo $this->mdl_warehouses->form_value('warehouse_location', true); ?>">
        </div>

    </div>

</form>
