<?php
// Called in [quotes|invoices]/partial_itemlist_table.php
$item_value = isset($item->item_discount) ? format_currency($item->item_discount) : '';
?>
                <td class="td-amount td-vert-middle">
                    <span><?php _trans('discount'); ?></span><br/>
                    <span name="item_discount_total" class="amount"><?php echo $item_value; ?></span>
<?php if ( ! $legacy_calculation) : ?>
                    <span name="item_discount_total" class="amount"><?php echo format_currency($item->item_subtotal - ($item->item_total - $item->item_tax_total + $item->item_discount)); ?></span>
<?php endif ?>
                </td>