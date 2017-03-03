<form method="post" class="form-horizontal">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php echo trans('custom_values_new'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>
        <?php $alpha = str_replace("-", "_", strtolower($field->custom_field_type)); ?>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('field'); ?>: </label>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <input type="text" class="form-control"
                       value="<?php echo htmlentities($field->custom_field_label); ?>" disabled="disabled"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('type'); ?>: </label>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <input type="text" class="form-control"
                       value="<?php echo htmlentities(trans($alpha)); ?>" disabled="disabled"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('value'); ?>: </label>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <input type="text" class="form-control" name="custom_values_value" id="custom_values_value" value=""/>
            </div>
        </div>

    </div>

</form>
