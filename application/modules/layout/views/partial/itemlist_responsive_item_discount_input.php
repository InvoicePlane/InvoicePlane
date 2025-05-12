<?php
// Called in [quotes|invoices]/partial_itemlist_responsive.php (item & new) line
$invoice_disabled = isset($invoice) && $invoice->is_read_only == 1 ? ' disabled="disabled"' : '';
$item_id          = $item->item_id ?? '';
$item_value       = isset($item->item_discount_amount) ? format_amount($item->item_discount_amount) : '';
?>
                                <div class="input-group">
                                    <label for="item_discount_amount_<?php echo $item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('discount'); ?></label>
                                    <input type="text" name="item_discount_amount" id="item_discount_amount_<?php echo $item_id; ?>" class="form-control"
                                           value="<?php echo $item_value; ?>"<?php echo $invoice_disabled; ?>
                                           data-toggle="tooltip" data-placement="bottom" title="<?php _trans('item_discount'); ?>">
                                    <div class="input-group-addon"><?php echo get_setting('currency_symbol') . ' ' . trans('per_item'); ?></div>
                                </div>
