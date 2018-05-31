<div class="col-xs-12 col-lg-6 text-right">
    <div class="row mb-1">
        <div class="col-xs-9 col-sm-8">
            <?php _trans('subtotal'); ?>:
        </div>
        <div class="col-xs-3 col-sm-4">
            <?php echo format_currency($item->item_subtotal); ?>
        </div>
    </div>

    <div id="visible_discount_<?=$item->item_id?>" class="<?php if ($item->item_discount == 0) echo "invisible"; ?>">
        <div class="row mb-1">
            <div class="col-xs-9 col-sm-8">
                <?php _trans('discount'); ?>:
            </div>
            <div class="col-xs-3 col-sm-4">
                <?php echo format_currency($item->item_discount); ?>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-xs-9 col-sm-8">
                <?php _trans('subtotal_after_discount'); ?>:
            </div>
            <div class="col-xs-3 col-sm-4">
                <?php echo format_currency($item->item_subtotal_recalc); ?>
            </div>
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-xs-9 col-sm-8">
            <?php _trans('tax'); ?>:
        </div>
        <div class="col-xs-3 col-sm-4">
            <?php echo format_currency($item->item_tax_total); ?>
        </div>
    </div>

    <div class="row mb-1">
        <b>
            <div class="col-xs-9 col-sm-8">
                <?php _trans('total'); ?>:
            </div>
            <div class="col-xs-3 col-sm-4">
                <?php echo format_currency($item->item_total); ?>
            </div>
        </b>
    </div>
</div>
