<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo trans('tax_rate_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label class="control-label">
                    <?php echo trans('tax_rate_name'); ?>
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="tax_rate_name" id="tax_rate_name" class="form-control"
                       value="<?php echo $this->mdl_tax_rates->form_value('tax_rate_name'); ?>">
            </div>
        </div>

        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label class="control-label">
                    <?php echo trans('tax_rate_percent'); ?>
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="tax_rate_percent" id="tax_rate_percent" class="form-control"
                       value="<?php echo format_amount($this->mdl_tax_rates->form_value('tax_rate_percent')); ?>">
                <span class="form-control-feedback">%</span>
            </div>
        </div>

    </div>

</form>
