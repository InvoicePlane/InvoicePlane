<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('add_family'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <input class="hidden" name="is_update" type="hidden"
                    <?php if ($this->mdl_families->form_value('is_update')) {
                        echo 'value="1"';
                    } else {
                        echo 'value="0"';
                    } ?>>

                <div class="form-group">
                    <label for="family_name">
                        <?php _trans('family_name'); ?>
                    </label>
                    <input type="text" name="family_name" id="family_name" class="form-control"
                           value="<?php echo $this->mdl_families->form_value('family_name', true); ?>">
                </div>

            </div>
        </div>

    </div>

</form>
