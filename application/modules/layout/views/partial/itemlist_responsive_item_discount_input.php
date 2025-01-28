<?php
// Called in [quotes|invoices]/partial_itemlist_responsive.php
$invoice_disabled = isset($invoice) && $invoice->is_read_only == 1 ? ' disabled="disabled"' : '';
$item_id = isset($item->item_id) ? $item->item_id : '';
$item_value = isset($item->item_discount_amount) ? format_amount($item->item_discount_amount) : '';
?>
                                <div class="input-group">
                                    <label for="item_discount_amount_<?php echo $item_id; ?>" class="input-group-addon ig-addon-aligned"><?php _trans('discount'); ?></label>
                                    <input type="text" name="item_discount_amount" id="item_discount_amount_<?php echo $item_id; ?>" class="input-sm form-control"
                                           value="<?php echo $item_value; ?>"
                                           data-toggle="tooltip" data-placement="bottom"
                                           title="<?php echo trans('item_discount'); ?>"<?php echo $invoice_disabled; ?>>
                                    <div class="input-group-addon"><?php echo get_setting('currency_symbol') . ' ' . trans('per_item'); ?></div>
                                </div>
