<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo lang('add_family'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_families->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
            >

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="family_name" class="control-label">
                    <?php echo lang('family_name'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="family_name" id="family_name" class="form-control"
                       value="<?php echo $this->mdl_families->form_value('family_name'); ?>">
            </div>
        </div>

    </div>

</form>