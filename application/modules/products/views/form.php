<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo lang('products_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="row">
            <div class="col-xs-12 col-sm-7">
                <fieldset>
                    <legend>
                        <?php if ($this->mdl_products->form_value('product_id')) : ?>
                            #<?php echo $this->mdl_products->form_value('product_id'); ?>&nbsp;
                            <?php echo $this->mdl_products->form_value('product_name'); ?>
                        <?php else : ?>
                            <?php echo lang('new_product'); ?>
                        <?php endif; ?>
                    </legend>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo lang('family'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <select name="family_id" id="family_id" class="form-control">
                                <option value="0"><?php echo lang('select_family'); ?></option>
                                <?php foreach ($families as $family) { ?>
                                    <option value="<?php echo $family->family_id; ?>"
                                            <?php if ($this->mdl_products->form_value('family_id') == $family->family_id) { ?>selected="selected"<?php } ?>><?php echo $family->family_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo lang('product_sku'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <input type="text" name="product_sku" id="product_sku" class="form-control"
                                   value="<?php echo $this->mdl_products->form_value('product_sku'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo lang('product_name'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <input type="text" name="product_name" id="product_name" class="form-control"
                                   value="<?php echo $this->mdl_products->form_value('product_name'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo lang('product_description'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <textarea name="product_description" id="product_description" class="form-control"
                                      rows="3"><?php echo $this->mdl_products->form_value('product_description'); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo lang('product_price'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <input type="text" name="product_price" id="product_price" class="form-control"
                                   value="<?php echo format_amount($this->mdl_products->form_value('product_price'), $this->mdl_settings->setting('item_price_decimal_places')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo lang('tax_rate'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <select name="tax_rate_id" id="tax_rate_id" class="form-control">
                                <option value="0"><?php echo lang('none'); ?></option>
                                <?php foreach ($tax_rates as $tax_rate) { ?>
                                    <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                        <?php if ($this->mdl_products->form_value('tax_rate_id') == $tax_rate->tax_rate_id) { ?> selected="selected" <?php } ?>
                                        >
                                        <?php echo $tax_rate->tax_rate_name
                                            . ' (' . format_amount($tax_rate->tax_rate_percent) . '%)'; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-5">
                <fieldset>
                    <legend><?php echo lang('extra_information'); ?></legend>

                    <!--
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo lang('provider_name'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <input type="text" name="provider_name" id="provider_name" class="form-control"
                                   value="<?php echo $this->mdl_products->form_value('provider_name'); ?>">
                        </div>
                    </div>
    -->
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                            <label class="control-label"><?php echo lang('purchase_price'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-8">
                            <input type="text" name="purchase_price" id="purchase_price" class="form-control"
                                   value="<?php echo format_amount($this->mdl_products->form_value('purchase_price'), $this->mdl_settings->setting('item_price_decimal_places')); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>

    </div>

</form>