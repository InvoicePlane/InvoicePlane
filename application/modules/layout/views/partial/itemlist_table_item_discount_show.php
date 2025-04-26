<?php
// Called in [quotes|invoices]/partial_itemlist_table.php (item & new) line
$item_value = isset($item) ? format_currency($item->item_discount) : '';
?>
                <td class="td-amount td-vert-middle">
                    <span><?php _trans('discount'); ?></span><br/>
                    <span data-toggle="tooltip" data-placement="bottom" title="<?php _trans('item_discount'); ?>"
                          class="amount"><?php echo $item_value; ?></span>
<?php
$item_global_discount = $item_value ? $item->item_subtotal - ($item->item_total - $item->item_tax_total + $item->item_discount) : 0;
if (! $legacy_calculation && $item_global_discount) {
?>
                    <span data-toggle="tooltip" data-placement="bottom" title="<?php _trans('global_discount'); ?>"
                          class="amount">+ <?php echo format_currency($item_global_discount); ?></span>
                    <span data-toggle="tooltip" data-placement="bottom" title="<?php _trans('discount'); ?> (<?php _trans('subtotal'); ?>)"
                          class="amount">= <?php echo format_currency($item_global_discount + $item->item_discount); ?></span>
<?php
}
?>
                </td>