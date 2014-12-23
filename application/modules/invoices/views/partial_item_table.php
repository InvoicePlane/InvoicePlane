<div class="table-responsive">
    <table id="item_table" class="items table table-striped table-condensed table-bordered">
        <thead>
        <tr>
            <th><?php echo lang('item'); ?></th>
            <th class="td-description"><?php echo lang('description'); ?></th>
            <th><?php echo lang('quantity'); ?></th>
            <th><?php echo lang('price'); ?></th>
            <th><?php echo lang('tax_rate'); ?></th>
            <th><?php echo lang('subtotal'); ?></th>
            <th><?php echo lang('tax'); ?></th>
            <th><?php echo lang('total'); ?></th>
            <th class="td-icon"></th>
        </tr>
        </thead>
        <tbody>

        <tr id="new_item" style="display: none;">
            <td>
                <input type="hidden" name="invoice_id"
                       value="<?php echo $invoice_id; ?>">
                <input type="hidden" name="item_id" value="" class="form-control">
                <input type="text" name="item_name" class="lookup-item-name form-control"
                       data-typeahead=""><br>
                <label>
                    <input type="checkbox" name="save_item_as_lookup" tabindex="999">
                    <?php echo lang('save_item_as_lookup'); ?>
                </label>
            </td>

            <td>
                <textarea name="item_description" class="form-control"></textarea>
            </td>

            <td>
                <input type="text" class="input-xs form-control"
                       name="item_quantity"  value=""></td>

            <td>
                <input type="text" class="input-xs form-control"
                       name="item_price" value=""></td>

            <td>
                <select name="item_tax_rate_id" class="input-xs form-control">
                    <option value="0"><?php echo lang('none'); ?></option>
                    <?php foreach ($tax_rates as $tax_rate) { ?>
                        <option value="<?php echo $tax_rate->tax_rate_id; ?>"<?php if ($tax_rate->tax_rate_id == $this->mdl_settings->setting('default_item_tax_rate')) { ?>selected="selected"<?php } ?>><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td><span name="subtotal"></span></td>
            <td><span name="item_tax_total"></span></td>
            <td><span name="item_total"></span></td>
            <td></td>
        </tr>

        <?php foreach ($items as $item) { ?>
            <tr class="item">
                <td>
                    <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>">
                    <input type="text" name="item_name" class="form-control"
                           value="<?php echo $item->item_name; ?>">
                </td>

                <td>
                    <textarea name="item_description" class="form-control"><?php echo $item->item_description; ?></textarea>
                </td>

                <td>
                    <input type="text" name="item_quantity" class="input-sm form-control"
                           value="<?php echo format_amount($item->item_quantity); ?>">
                </td>

                <td>
                    <input type="text" name="item_price" class="input-sm form-control"
                           value="<?php echo format_amount($item->item_price); ?>">
                </td>

                <td>
                    <select name="item_tax_rate_id" name="item_tax_rate_id" class="form-control input-sm">
                        <option value="0"><?php echo lang('none'); ?></option>
                        <?php foreach ($tax_rates as $tax_rate) { ?>
                            <option value="<?php echo $tax_rate->tax_rate_id; ?>" <?php if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
                        <?php } ?>
                    </select>
                </td>

                <td>
                <span name="subtotal">
                    <?php echo format_currency($item->item_subtotal); ?>
                </span>
                </td>
                <td>
                <span name="item_tax_total">
                    <?php echo format_currency($item->item_tax_total); ?>
                </span>
                </td>
                <td>
                <span name="item_total">
                    <?php echo format_currency($item->item_total); ?>
                </span>
                </td>
                <td class="td-icon">
                    <a href="<?php echo site_url('invoices/delete_item/' . $invoice->invoice_id . '/' . $item->item_id); ?>" title="<?php echo lang('delete'); ?>">
                        <i class="fa fa-trash-o no-margin text-danger"></i>
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
            <th><?php echo lang('invoice_tax'); ?></th>
            <th><?php echo lang('total'); ?></th>
            <th><?php echo lang('paid'); ?></th>
            <th><?php echo lang('balance'); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
            <td><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
            <td>
                <?php if ($invoice_tax_rates) { foreach ($invoice_tax_rates as $invoice_tax_rate) { ?>
                    <strong><?php echo anchor('invoices/delete_invoice_tax/' . $invoice->invoice_id . '/' . $invoice_tax_rate->invoice_tax_rate_id, lang('remove')) . ' ' . $invoice_tax_rate->invoice_tax_rate_name . ' ' . $invoice_tax_rate->invoice_tax_rate_percent; ?>%:</strong>
                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?><br>
                <?php } } else { echo format_currency('0'); }?>
            </td>
            <td><?php echo format_currency($invoice->invoice_total); ?></td>
            <td><?php echo format_currency($invoice->invoice_paid); ?></td>
            <td><strong><?php echo format_currency($invoice->invoice_balance); ?></strong></td>
        </tr>
        </tbody>
    </table>
</div>