<?php
// Called in [quotes|invoices]/partial_itemlist_responsive.php (item & new) line
$item_value = isset($item) ? format_currency($item->item_discount) : '';
?>
<hr class="no-margin">
                                <div class="row mb-1">
                                    <div class="col-xs-9 col-sm-8"><?php _trans('item_discount'); ?>:</div>
                                    <div class="col-xs-3 col-sm-4"><?php echo $item_value; ?></div>
                                </div>
<?php
$item_global_discount = $item_value ? $item->item_subtotal - ($item->item_total - $item->item_tax_total + $item->item_discount) : 0;
if (! $legacy_calculation && $item_global_discount)
{
?>
                                <div class="row mb-1">
                                    <div class="col-xs-9 col-sm-8"><?php _trans('global_discount'); ?>:</div>
                                    <div class="col-xs-3 col-sm-4"><?php echo format_currency($item_global_discount); ?></div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-xs-9 col-sm-8"><?php _trans('discount'); ?> (<?php _trans('subtotal'); ?>):</div>
                                    <div class="col-xs-3 col-sm-4"><?php echo format_currency($item_global_discount + $item->item_discount); ?></div>
                                </div>
<?php
}
?>
<hr class="no-margin">