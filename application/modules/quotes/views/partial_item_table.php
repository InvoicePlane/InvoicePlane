<div class="table-responsive">
    <table id="item_table" class="items table table-condensed table-bordered">
        <thead style="display: none">
        <tr>
            <th></th>
            <th><?php echo lang('item'); ?></th>
            <th><?php echo lang('description'); ?></th>
            <th><?php echo lang('quantity'); ?></th>
            <th><?php echo lang('price'); ?></th>
            <th><?php echo lang('tax_rate'); ?></th>
            <th><?php echo lang('subtotal'); ?></th>
            <th><?php echo lang('tax'); ?></th>
            <th><?php echo lang('total'); ?></th>
            <th></th>
        </tr>
        </thead>

        <tbody id="blank-row" style="display: none;">
        <tr>
            <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
            <td class="td-text">
                <input type="hidden" name="items[][quote_id]" value="<?php echo $quote_id; ?>"
                       class="quote_id form-control"
                       disabled="disabled">
                <input type="hidden" name="items[][item_id]" value="" class="item_id form-control" disabled="disabled">
                <input type="hidden" name="items[][item_product_id]" value="" class="item_product_id form-control"
                       disabled="disabled">

                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('item'); ?></span>
                    <input type="text" name="items[][item_name]" class="item_name input-sm form-control" value=""
                           disabled="disabled">
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('quantity'); ?></span>
                    <input type="text" name="items[][item_quantity]" class="item_quantity input-sm form-control amount"
                           value=""
                           disabled="disabled">
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('price'); ?></span>
                    <input type="text" name="items[][item_price]" class="item_price input-sm form-control amount"
                           value=""
                           disabled="disabled">
                </div>
            </td>
            <td class="td-amount ">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('item_discount'); ?></span>
                    <input type="text" name="items[][item_discount_amount]"
                           class="item_discount_amount input-sm form-control amount"
                           value="" data-toggle="tooltip" data-placement="bottom" disabled="disabled"
                           title="<?php echo $this->mdl_settings->setting('currency_symbol') . ' ' . lang('per_item'); ?>">
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('tax_rate'); ?></span>
                    <select name="items[][item_tax_rate_id]" name="item_tax_rate_id" disabled="disabled"
                            class="item_tax_rate_id form-control input-sm">
                        <option value="0"><?php echo lang('none'); ?></option>
                        <?php foreach ($tax_rates as $tax_rate) { ?>
                            <option value="<?php echo $tax_rate->tax_rate_id; ?>">
                                <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </td>
            <td class="td-icon text-right td-vert-middle"></td>
        </tr>
        <tr>
            <td class="td-textarea">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('description'); ?></span>
                    <textarea name="items[][item_description]" class="item_description input-sm form-control"
                              disabled="disabled"></textarea>
                </div>
            </td>
            <td colspan="2" class="td-amount td-vert-middle">
                <span><?php echo lang('subtotal'); ?></span><br/>
                <span name="subtotal" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle">
                <span><?php echo lang('discount'); ?></span><br/>
                <span name="item_discount_total" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle">
                <span><?php echo lang('tax'); ?></span><br/>
                <span name="item_tax_total" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle">
                <span><?php echo lang('total'); ?></span><br/>
                <span name="item_total" class="amount"></span>
            </td>
        </tr>
        </tbody>

        <?php
        $items = old('items', $items);
        foreach ($items as $i => $item) {
            // Convert $item array to object if needed
            if (is_array($item)) {
                $item = json_decode(json_encode($item));
            }
            ?>
            <tbody class="item">
            <tr>
                <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
                <td class="td-text">
                    <input type="hidden" name="items[<?php echo $i; ?>][item_id]"
                           value="<?php echo old('items[' . $i . '][item_id]', $item->item_id); ?>">
                    <input type="hidden" name="items[<?php echo $i; ?>][item_product_id]"
                           value="<?php echo old('items[' . $i . '][item_product_id]', $item->item_product_id); ?>">

                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('item'); ?></span>
                        <input type="text" name="items[<?php echo $i; ?>][item_name]" class="input-sm form-control"
                               value="<?php echo html_escape(old('items[' . $i . '][item_name]', $item->item_name)); ?>">
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('quantity'); ?></span>
                        <input type="text" name="items[<?php echo $i; ?>][item_quantity]"
                               class="input-sm form-control amount"
                               value="<?php echo format_amount(old('items[' . $i . '][item_quantity]', $item->item_quantity)); ?>">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('price'); ?></span>
                        <input type="text" name="items[<?php echo $i; ?>][item_price]"
                               class="input-sm form-control amount"
                               value="<?php echo format_amount(old('items[' . $i . '][item_price]', $item->item_price)); ?>">
                    </div>
                </td>
                <td class="td-amount ">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('item_discount'); ?></span>
                        <input type="text" name="items[<?php echo $i; ?>][item_discount_amount]"
                               class="input-sm form-control amount"
                               value="<?php echo format_amount(old('items[' . $i . '][item_discount_amount]', $item->item_discount_amount)); ?>"
                               data-toggle="tooltip" data-placement="bottom"
                               title="<?php echo $this->mdl_settings->setting('currency_symbol') . ' ' . lang('per_item'); ?>">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('tax_rate'); ?></span>
                        <select name="items[<?php echo $i; ?>][item_tax_rate_id]" name="item_tax_rate_id"
                                class="form-control input-sm">
                            <option value="0"><?php echo lang('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                    <?php if ($item->item_tax_rate_id == old('items[' . $i . '][item_tax_rate_id]', $tax_rate->tax_rate_id)) { ?>
                                        selected="selected"
                                    <?php } ?>>
                                    <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td class="td-icon text-right td-vert-middle">
                    <a href="<?php echo site_url('quotes/delete_item/' . $quote->quote_id . '/' . $item->item_id); ?>"
                       title="<?php echo lang('delete'); ?>">
                        <i class="fa fa-trash-o text-danger"></i>
                    </a>
                </td>
            </tr>
            <tr>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('description'); ?></span>
                        <textarea name="items[<?php echo $i; ?>][item_description]" class="input-sm form-control"
                        ><?php echo old('items[' . $i . '][item_description]', $item->item_description); ?></textarea>
                    </div>
                </td>

                <td colspan="2" class="td-amount td-vert-middle">
                    <span><?php echo lang('subtotal'); ?></span><br/>
                    <span name="subtotal" class="amount">
                        <?php echo format_currency(($item->item_subtotal ? $item->item_subtotal : 0)); ?>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('discount'); ?></span><br/>
                    <span name="item_discount_total" class="amount">
                        <?php echo format_currency(($item->item_discount ? $item->item_discount : 0)); ?>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('tax'); ?></span><br/>
                    <span name="item_tax_total" class="amount">
                        <?php echo format_currency(($item->item_tax_total ? $item->item_tax_total : 0)); ?>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('total'); ?></span><br/>
                    <span name="item_total" class="amount">
                        <?php echo format_currency(($item->item_total ? $item->item_total : 0)); ?>
                    </span>
                </td>
            </tr>
            </tbody>
        <?php } ?>

    </table>
</div>

<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="btn-group">
            <button type="button" class="btn-add-new-row btn btn-sm btn-default">
                <i class="fa fa-plus"></i>
                <?php echo lang('add_new_row'); ?>
            </button>
            <button type="button" class="load-modal-by-url btn btn-sm btn-default"
                    data-modal-url="<?php echo site_url('products/ajax/modal_product_lookups'); ?>">
                <i class="fa fa-database"></i>
                <?php echo lang('add_product'); ?>
            </button>
        </div>
        <br/><br/>
    </div>

    <div class="col-xs-12 col-md-6 col-md-offset-2 col-lg-4 col-lg-offset-4">
        <table class="table table-condensed text-right">
            <tr>
                <td style="width: 40%;">
                    <?php echo lang('subtotal'); ?>
                </td>
                <td style="width: 60%;" class="amount">
                    <?php echo format_currency(($quote->quote_item_subtotal ? $quote->quote_item_subtotal : 0)); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo lang('item_tax'); ?></td>
                <td class="amount"><?php echo format_currency(($quote->quote_item_tax_total ? $quote->quote_item_tax_total : 0)); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('quote_tax'); ?></td>
                <td>
                    <?php if ($quote_tax_rates) {
                        foreach ($quote_tax_rates as $quote_tax_rate) { ?>
                            <div class="inline text-muted">
                                <?php
                                $anchor_url = 'quotes/delete_quote_tax/' . $quote->quote_id . '/' . $quote_tax_rate->quote_tax_rate_id;
                                echo anchor($anchor_url, '<i class="fa fa-trash-o"></i>');
                                echo ' ' . $quote_tax_rate->quote_tax_rate_name . ' ' . $quote_tax_rate->quote_tax_rate_percent; ?>
                                &percnt;
                            </div>
                            &nbsp;
                            <div class="inline amount">
                                <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                            </div>
                        <?php }
                    } else {
                        echo format_currency('0');
                    } ?>
                </td>
            </tr>
            <tr>
                <td class="td-vert-middle"><?php echo lang('discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_amount" name="quote_discount_amount"
                                   class="discount-option form-control input-sm amount"
                                   value="<?php
                                   if (old('items[' . $i . '][quote_discount_amount]', $quote->quote_discount_amount)) {
                                       echo old('items[' . $i . '][quote_discount_amount]', $quote->quote_discount_amount);
                                   } ?>">

                            <div
                                class="input-group-addon"><?php echo $this->mdl_settings->setting('currency_symbol'); ?></div>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_percent" name="quote_discount_percent"
                                   value="<?php
                                   if (old('items[' . $i . '][quote_discount_percent]', $quote->quote_discount_percent)) {
                                       echo old('items[' . $i . '][quote_discount_percent]', $quote->quote_discount_percent);
                                   } ?>"
                                   class="discount-option form-control input-sm amount">

                            <div class="input-group-addon">&percnt;</div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><b><?php echo lang('total'); ?></b></td>
                <td class="amount"><b><?php echo format_currency(($quote->quote_total ? $quote->quote_total : 0)); ?></b></td>
            </tr>
        </table>
    </div>
</div>