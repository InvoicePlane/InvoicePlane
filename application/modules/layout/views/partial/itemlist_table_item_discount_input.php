<?php
// Called in [quotes|invoices]/partial_itemlist_table.php (item & new) line
$invoice_disabled = isset($invoice) && $invoice->is_read_only == 1 ? ' disabled="disabled"' : '';
$item_value       = isset($item->item_discount_amount) ? format_amount($item->item_discount_amount) : '';
?>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon"><?php _trans('discount'); ?></span>
                    <input type="text" name="item_discount_amount" class="form-control amount"
                           value="<?php echo $item_value; ?>"<?php echo $invoice_disabled; ?>
                           data-toggle="tooltip" data-placement="bottom" title="<?php _trans('item_discount'); ?>">
                    <span class="input-group-addon"><?php echo get_setting('currency_symbol') . ' ' . trans('per_item'); ?></span>
                </div>
            </td>
