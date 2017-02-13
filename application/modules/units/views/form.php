<form method="post" class="form-horizontal">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1><?php echo trans('add_unit'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_units->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
        >

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="unit_name" class="control-label">
                    <?php echo trans('unit_name'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="unit_name" id="unit_name" class="form-control"
                       value="<?php echo $this->mdl_units->form_value('unit_name'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="unit_name_plrl" class="control-label">
                    <?php echo trans('unit_name_plrl'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="unit_name_plrl" id="unit_name_plrl" class="form-control"
                       value="<?php echo $this->mdl_units->form_value('unit_name_plrl'); ?>">
            </div>
        </div>

    </div>

</form>
