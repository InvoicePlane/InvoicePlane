<?php
// Called in [quotes|invoices]/partial_itemlist_responsive.php
$item_value = isset($item->item_discount) ? format_currency($item->item_discount) : '';
?>
                                <div class="row mb-1">
                                    <div class="col-xs-9 col-sm-8">
                                        <?php _trans('item'); ?> <?php _trans('discount'); ?>:
                                    </div>
                                    <div class="col-xs-3 col-sm-4">
                                        <?php echo $item_value; ?>
                                    </div>
                                </div>
<?php if ( ! $legacy_calculation) : ?>
                                <div class="row mb-1">
                                    <div class="col-xs-9 col-sm-8">
                                        <?php _trans('invoice'); ?> <?php _trans('discount'); ?>:
                                    </div>
                                    <div class="col-xs-3 col-sm-4">
                                        <?php echo format_currency($item->item_subtotal - ($item->item_total - $item->item_tax_total + $item->item_discount)); ?>
                                    </div>
                                </div>
<?php endif ?>