<div class="table-responsive">
    <table id="item_table" class="items table table-striped table-condensed table-bordered">
        <thead>
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
        <tbody>

        <tr id="new_row" style="display: none;">
            <td><i class="fa fa-arrows cursor-move"></i></td>
            <td class="td-text">
                <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
                <input type="hidden" name="item_id" value="">
                <input type="text" name="item_name"
                       class="form-control lookup-item-name" data-typeahead="">
                <br/>
                <!-- // temporarily disabled
                <label>
                    <input type="checkbox" name="save_item_as_lookup" tabindex="999">
                    <?php echo lang('save_item_as_lookup'); ?>
                </label>
                -->
            </td>
            <td class="td-textarea">
                <textarea name="item_description" class="form-control"></textarea>
            </td>
            <td class="td-amount amount">
                <input type="text" class="input-sm form-control"
                       name="item_quantity" value="">
            </td>
            <td class="td-amount amount">
                <input type="text" class="input-sm form-control"
                       name="item_price" value="">
            </td>
            <td class="td-amount amount">
                <select name="item_tax_rate_id" class="input-sm form-control">
                    <option value="0"><?php echo lang('none'); ?></option>
                    <?php foreach ($tax_rates as $tax_rate) { ?>
                        <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                <?php if ($tax_rate->tax_rate_id == $this->mdl_settings->setting('default_item_tax_rate')) { ?>selected="selected"<?php } ?>>
                            <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
            <td class="td-amount amount"><span name="subtotal"></span></td>
            <td class="td-amount amount"><span name="item_tax_total"></span></td>
            <td class="td-amount amount"><span name="item_total"></span></td>
            <td class="td-icon"></td>
        </tr>

        <?php foreach ($items as $item) { ?>
            <tr class="item">
                <td><i class="fa fa-arrows cursor-move"></i></td>
                <td class="td-text">
                    <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>">
                    <input type="text" name="item_name" class="input-sm form-control"
                           value="<?php echo html_escape($item->item_name); ?>">
                </td>
                <td class="td-textarea">
                    <textarea name="item_description"
                              class="input-sm form-control"><?php echo $item->item_description; ?></textarea>
                </td>
                <td class="td-amount amount">
                    <input type="text" name="item_quantity" class="input-sm form-control"
                           value="<?php echo format_amount($item->item_quantity); ?>">
                </td>
                <td class="td-amount amount">
                    <input type="text" name="item_price" class="input-sm form-control"
                           value="<?php echo format_amount($item->item_price); ?>">
                </td>
                <td class="td-amount amount">
                    <select name="item_tax_rate_id" name="item_tax_rate_id"
                            class="form-control input-sm">
                        <option value="0"><?php echo lang('none'); ?></option>
                        <?php foreach ($tax_rates as $tax_rate) { ?>
                            <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                    <?php if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>>
                                <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
                <td class="td-amount amount">
                <span name="subtotal">
                    <?php echo format_currency($item->item_subtotal); ?>
                </span>
                </td>
                <td class="td-amount amount">
                <span name="item_tax_total">
                    <?php echo format_currency($item->item_tax_total); ?>
                </span>
                </td>
                <td class="td-amount amount">
                <span name="item_total">
                    <?php echo format_currency($item->item_total); ?>
                </span>
                </td>
                <td class="td-icon">
                    <a href="<?php echo site_url('quotes/delete_item/' . $quote->quote_id . '/' . $item->item_id); ?>"
                       title="<?php echo lang('delete'); ?>">
                        <i class="fa fa-trash-o text-danger"></i>
                    </a>
                </td>
            </tr>
        <?php } ?>

        </tbody>

    </table>
</div>

<div class="table-responsive">
    <table class="table table-striped table-condensed table-bordered">
        <thead>
        <tr>
            <th><?php echo lang('subtotal'); ?></th>
            <th><?php echo lang('item_tax'); ?></th>
            <th><?php echo lang('quote_tax'); ?></th>
            <th><?php echo lang('total'); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><span class="amount"><?php echo format_currency($quote->quote_item_subtotal); ?></span></td>
            <td><span class="amount"><?php echo format_currency($quote->quote_item_tax_total); ?></span></td>
            <td>
                <?php if ($quote_tax_rates) {
                    foreach ($quote_tax_rates as $quote_tax_rate) { ?>
                        <?php echo anchor('quotes/delete_quote_tax/' . $quote->quote_id . '/' . $quote_tax_rate->quote_tax_rate_id, '<i class="fa fa-trash-o"></i>');
                        echo ' ' . $quote_tax_rate->quote_tax_rate_name . ' ' . $quote_tax_rate->quote_tax_rate_percent; ?>%:
                        <span
                            class="amount"><?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?></span>
                    <?php }
                } else {
                    echo format_currency('0');
                } ?>
            </td>
            <td>
                <strong class="amount">
                    <?php echo format_currency($quote->quote_total); ?>
                </strong>
            </td>
        </tr>
        </tbody>
    </table>
</div>