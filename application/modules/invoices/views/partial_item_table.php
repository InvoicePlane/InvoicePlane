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
                <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                <input type="hidden" name="item_id" value="" class="form-control">
                <input type="text" name="item_name" class="lookup-item-name form-control"
                       data-typeahead=""><br>
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
                <input type="text" class="input-xs form-control"
                       name="item_quantity" value=""></td>

            <td class="td-amount amount">
                <input type="text" class="input-xs form-control"
                       name="item_price" value=""></td>

            <td class="td-amount amount">
                <select name="item_tax_rate_id" class="input-xs form-control">
                    <option value="0"><?php echo lang('none'); ?></option>
                    <?php foreach ($tax_rates as $tax_rate) { ?>
                        <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                <?php if ($tax_rate->tax_rate_id == $this->mdl_settings->setting('default_item_tax_rate')) { ?>selected="selected"<?php } ?>><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
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
                    <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>>
                    <input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>>
                    <input type="text" name="item_name" class="form-control"
                           value="<?php echo $item->item_name; ?>"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>>
                </td>

                <td class="td-textarea">
                    <textarea name="item_description" class="form-control"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>
                        ><?php echo $item->item_description; ?></textarea>
                </td>

                <td class="td-amount amount">
                    <input type="text" name="item_quantity" class="input-sm form-control"
                           value="<?php echo format_amount($item->item_quantity); ?>"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>>
                </td>

                <td class="td-amount amount">
                    <input type="text" name="item_price" class="input-sm form-control"
                           value="<?php echo format_amount($item->item_price); ?>"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>>
                </td>

                <td class="td-amount amount">
                    <select name="item_tax_rate_id" name="item_tax_rate_id" class="form-control input-sm"
                        <?php if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        } ?>>
                        <option value="0"><?php echo lang('none'); ?></option>
                        <?php foreach ($tax_rates as $tax_rate) { ?>
                            <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                    <?php if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
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
                    <?php if ($invoice->is_read_only != 1) { ?>
                        <a href="<?php echo site_url('invoices/delete_item/' . $invoice->invoice_id . '/' . $item->item_id); ?>"
                           title="<?php echo lang('delete'); ?>">
                            <i class="fa fa-trash-o no-margin text-danger"></i>
                        </a>
                    <?php } ?>
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
            <td>
                <span class="amount">
                    <?php echo format_currency($invoice->invoice_item_subtotal); ?>
                </span>
            </td>
            <td>
                <span class="amount">
                    <?php echo format_currency($invoice->invoice_item_tax_total); ?>
                </span>
            </td>
            <td>
                <?php
                if ($invoice_tax_rates) {
                    foreach ($invoice_tax_rates as $invoice_tax_rate) {
                        if ($invoice->is_read_only != 1) {
                            echo anchor('invoices/delete_invoice_tax/' . $invoice->invoice_id . '/' . $invoice_tax_rate->invoice_tax_rate_id, '<i class="fa fa-trash-o"></i>');
                        }
                        echo ' ' . $invoice_tax_rate->invoice_tax_rate_name . ' ' . $invoice_tax_rate->invoice_tax_rate_percent; ?>%:
                        <span class="amount">
                            <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                        </span>
                    <?php
                    }
                } else {
                    echo format_currency('0');
                } ?>
            </td>
            <td>
                <span class="amount">
                    <?php echo format_currency($invoice->invoice_total); ?>
                </span>
            </td>
            <td>
                <span class="amount">
                    <?php echo format_currency($invoice->invoice_paid); ?>
                </span>
            </td>
            <td>
                <strong class="amount">
                    <?php echo format_currency($invoice->invoice_balance); ?>
                </strong>
            </td>
        </tr>
        </tbody>
    </table>
</div>