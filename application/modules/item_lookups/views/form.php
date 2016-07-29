<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo trans('item_lookup_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="item_name" class="control-label">
                    <?php echo trans('item_name'); ?>:
                </label>
            </div>

            <div class="col-xs-12 col-sm-6">
                <input type="text" name="item_name" id="item_name" class="form-control"
                       value="<?php echo $this->mdl_item_lookups->form_value('item_name'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="item_description" class="control-label">
                    <?php echo trans('description'); ?>:
                </label>
            </div>

            <div class="col-xs-12 col-sm-6">
                <input type="text" name="item_description" id="item_description" class="form-control"
                       value="<?php echo $this->mdl_item_lookups->form_value('item_description'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="item_price" class="control-label">
                    <?php echo trans('price'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="item_price" id="item_price" class="form-control"
                       value="<?php echo $this->mdl_item_lookups->form_value('item_price'); ?>">
            </div>
        </div>

    </div>

</form>