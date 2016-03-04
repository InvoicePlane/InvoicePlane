<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo lang('payment_method_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_payment_methods->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
            >

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_name" class="control-label">
                    <?php echo lang('payment_method'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="payment_method_name" id="payment_method_name" class="form-control"
                       value="<?php echo $this->mdl_payment_methods->form_value('payment_method_name'); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label><?php echo lang('receipt_numbering_group'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="receipt_group_id" id="receipt_group_id" class="form-control">
                    <option value=""></option>
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                                <?php if ($this->mdl_payment_methods->form_value('receipt_group_id') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>><?php echo $invoice_group->invoice_group_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

    </div>

</form>