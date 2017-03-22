<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php echo trans('products_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <fieldset>
                    <legend>
                        <?php if ($this->mdl_products->form_value('product_id')) : ?>
                            #<?php echo $this->mdl_products->form_value('product_id'); ?>&nbsp;
                            <?php echo $this->mdl_products->form_value('product_name'); ?>
                        <?php else : ?>
                            <?php echo trans('new_product'); ?>
                        <?php endif; ?>
                    </legend>

                    <div class="form-group">
                        <label class="control-label" for="family_id">
                            <?php echo trans('family'); ?>
                        </label>

                        <select name="family_id" id="family_id" class="form-control simple-select">
                            <option value="0"><?php echo trans('select_family'); ?></option>
                            <?php foreach ($families as $family) { ?>
                                <option value="<?php echo $family->family_id; ?>"
                                        <?php if ($this->mdl_products->form_value('family_id') == $family->family_id) {
                                        ?>selected="selected"<?php } ?>
                                ><?php echo $family->family_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label" id="product_sku">
                            <?php echo trans('product_sku'); ?>
                        </label>

                        <input type="text" name="product_sku" id="product_sku" class="form-control"
                               value="<?php echo $this->mdl_products->form_value('product_sku'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="product_name">
                            <?php echo trans('product_name'); ?>
                        </label>

                        <input type="text" name="product_name" id="product_name" class="form-control" required
                               value="<?php echo $this->mdl_products->form_value('product_name'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="product_description">
                            <?php echo trans('product_description'); ?>
                        </label>

                        <textarea name="product_description" id="product_description" class="form-control"
                                  rows="3"><?php echo $this->mdl_products->form_value('product_description'); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="product_price">
                            <?php echo trans('product_price'); ?>
                        </label>

                        <div class="input-group has-feedback">
                            <input type="text" name="product_price" id="product_price" class="form-control"
                                   value="<?php echo format_amount($this->mdl_products->form_value('product_price')); ?>">
                            <span class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="unit_id">
                            <?php echo trans('product_unit'); ?>
                        </label>

                        <select name="unit_id" id="unit_id" class="form-control simple-select">
                            <option value="0"><?php echo trans('select_unit'); ?></option>
                            <?php foreach ($units as $unit) { ?>
                                <option value="<?php echo $unit->unit_id; ?>"
                                        <?php if ($this->mdl_products->form_value('unit_id') == $unit->unit_id) {
                                        ?>selected="selected"<?php } ?>
                                ><?php echo $unit->unit_name . '/' . $unit->unit_name_plrl; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="tax_rate_id">
                            <?php echo trans('tax_rate'); ?>
                        </label>

                        <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
                            <option value="0"><?php echo trans('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                        <?php if ($this->mdl_products->form_value('tax_rate_id') == $tax_rate->tax_rate_id) {
                                        ?>selected="selected" <?php } ?>>
                                    <?php echo $tax_rate->tax_rate_name
                                        . ' (' . format_amount($tax_rate->tax_rate_percent) . '%)'; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </fieldset>
            </div>

            <div class="col-xs-12 col-md-6">

                <fieldset>
                    <legend><?php echo trans('extra_information'); ?></legend>

                    <div class="form-group">
                        <label class="control-label" for="provider_name">
                            <?php echo trans('provider_name'); ?>
                        </label>

                        <input type="text" name="provider_name" id="provider_name" class="form-control"
                               value="<?php echo $this->mdl_products->form_value('provider_name'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="purchase_price">
                            <?php echo trans('purchase_price'); ?>
                        </label>

                        <div class="input-group has-feedback">
                            <input type="text" name="purchase_price" id="purchase_price" class="form-control"
                                   value="<?php echo format_amount($this->mdl_products->form_value('purchase_price')); ?>">
                            <span class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></span>
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend><?php echo trans('invoice_sumex'); ?></legend>

                    <div class="form-group">
                        <label class="control-label" for="product_tariff">
                            <?php echo trans('product_tariff'); ?>
                        </label>

                        <input type="text" name="product_tariff" id="product_tariff" class="form-control"
                               value="<?php echo $this->mdl_products->form_value('product_tariff'); ?>">
                    </div>

                </fieldset>

            </div>
        </div>

    </div>

</form>
