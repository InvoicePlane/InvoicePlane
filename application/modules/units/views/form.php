<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('add_unit'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <input class="hidden" name="is_update" type="hidden"
                    <?php if ($this->mdl_units->form_value('is_update')) {
                        echo 'value="1"';
                    } else {
                        echo 'value="0"';
                    } ?>
                >

                <div class="form-group">
                    <label for="unit_name">
                        <?php _trans('unit_name'); ?>
                    </label>
                    <input type="text" name="unit_name" id="unit_name" class="form-control"
                           value="<?php echo $this->mdl_units->form_value('unit_name', true); ?>">
                </div>

                <div class="form-group">
                    <label for="unit_name_plrl">
                        <?php _trans('unit_name_plrl'); ?>
                    </label>
                    <input type="text" name="unit_name_plrl" id="unit_name_plrl" class="form-control"
                           value="<?php echo $this->mdl_units->form_value('unit_name_plrl', true); ?>">
                </div>

            </div>
        </div>

    </div>

</form>
