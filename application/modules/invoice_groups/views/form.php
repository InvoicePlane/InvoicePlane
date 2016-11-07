<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo trans('invoice_group_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label" for="invoice_group_name"><?php echo trans('name'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <input type="text" name="invoice_group_name" id="invoice_group_name" class="form-control"
                       value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_name'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label"
                       for="invoice_group_identifier_format"><?php echo trans('identifier_format'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <input type="text" class="form-control taggable"
                       name="invoice_group_identifier_format" id="invoice_group_identifier_format"
                       value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_identifier_format'); ?>"
                       placeholder="INV-{{{id}}}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label" for="invoice_group_next_id"><?php echo trans('next_id'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <input type="text" name="invoice_group_next_id" id="invoice_group_next_id" class="form-control"
                       value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_next_id'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                <label class="control-label" for="invoice_group_left_pad"><?php echo trans('left_pad'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-8 col-lg-8">
                <input type="text" name="invoice_group_left_pad" id="invoice_group_left_pad" class="form-control"
                       value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_left_pad'); ?>">
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-xs-12">
                <h4><?php echo trans('identifier_format_template_tags'); ?></h4>

                <p><?php echo trans('identifier_format_template_tags_instructions'); ?></p>
            </div>

            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                <a href="#" class="text-tag" data-tag="{{{id}}}"><?php echo trans('id'); ?></a><br>
                <a href="#" class="text-tag" data-tag="{{{year}}}"><?php echo trans('current_year'); ?></a><br>
                <a href="#" class="text-tag" data-tag="{{{month}}}"><?php echo trans('current_month'); ?></a><br>
                <a href="#" class="text-tag" data-tag="{{{day}}}"><?php echo trans('current_day'); ?></a><br>
            </div>
        </div>
    </div>
</form>
