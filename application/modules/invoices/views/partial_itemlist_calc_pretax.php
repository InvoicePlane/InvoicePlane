<div class="col-xs-12 col-lg-6 text-right">
    <div class="row mb-1">
        <div class="col-xs-9 col-sm-8">
            <?php _trans('subtotal'); ?>:
        </div>
        <div id="subtotal_<?=$item->item_id?>" class="col-xs-3 col-sm-4">
            <?php echo format_currency($item->item_subtotal); ?>
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-xs-9 col-sm-8">
            <?php _trans('tax'); ?>:
        </div>
        <div id="tax_total_<?=$item->item_id?>" class="col-xs-3 col-sm-4">
            <?php echo format_currency($item->item_tax_total); ?>
        </div>
    </div>

    <div id="visible_discount_<?=$item->item_id?>" class="<?php if ($item->item_discount == 0) echo "invisible"; ?>">
        <div class="row mb-1">
            <div class="col-xs-9 col-sm-8">
                <?php _trans('subtotal_before_discount'); ?>:
            </div>
            <div id="subtotal_recalc_<?=$item->item_id?>" class="col-xs-3 col-sm-4">
                <?php echo format_currency($item->item_subtotal_recalc); ?>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-xs-9 col-sm-8">
                <?php _trans('discount'); ?>:
            </div>
            <div id="discount_total_<?=$item->item_id?>" class="col-xs-3 col-sm-4">
                <?php echo format_currency($item->item_discount); ?>
            </div>
        </div>
    </div>

    <div class="row mb-1">
        <b>
            <div class="col-xs-9 col-sm-8">
                <?php _trans('total'); ?>:
            </div>
            <div id="total_<?=$item->item_id?>" class="col-xs-3 col-sm-4">
                <?php echo format_currency($item->item_total); ?>
            </div>
        </b>
    </div>
</div>

<script>
    var tax_rates = new Array();
    <?php foreach ($tax_rates as $tax_rate) echo 'tax_rates["' .  $tax_rate->tax_rate_id . '"] = ' . $tax_rate->tax_rate_percent . ";\n"; ?>

    function toNum(n) {
        'use strict';
        n = n.replace(/\<?=get_setting('thousands_separator')?>/g, '').replace('<?=get_setting('decimal_point')?>', '.');
        return n;
    }

    function toStr(n) {
        'use strict';
        n = n.toFixed(2).toString().replace(/\./g, '<?=get_setting('decimal_point')?>') + "<?=get_setting('currency_symbol')?>";
        return n;
    }

    function calcItem(id)
    {
        var quantity = toNum(document.getElementById("quantity_" + id).value);
        var price = toNum(document.getElementById("price_" + id).value);
        var pricetype = document.getElementById("price_type_" + id).value;
        var discount = toNum(document.getElementById("discount_" + id).value);

        var tid = document.getElementById("tax_rate_" + id).value;
        if (typeof tax_rates[tid] !== 'undefined' && tax_rates[tid] !== null) {
            var taxrate = tax_rates[tid] / 100
        } else {
            var taxrate = 0;
        }

        if (pricetype == "1") {
            var subtotal = price * quantity / (1 + taxrate);
            var discount_total = quantity * discount / (1 + taxrate);
        } else {
            var subtotal = price * quantity;
            var discount_total = quantity * discount;
        }

        var tax_total = subtotal * taxrate;
        var subtotal_recalc = subtotal + tax_total;
        var total = subtotal_recalc - discount;

        document.getElementById("subtotal_" + id).innerHTML = toStr(subtotal);
        document.getElementById("discount_total_" + id).innerHTML = toStr(discount_total);
        document.getElementById("subtotal_recalc_" + id).innerHTML = toStr(subtotal_recalc);
        document.getElementById("tax_total_" + id).innerHTML = toStr(tax_total);
        document.getElementById("total_" + id).innerHTML = toStr(total);

        if (discount_total > 0) {
            document.getElementById("visible_discount_" + id).className = ('visible');
        } else {
            document.getElementById("visible_discount_" + id).className = ('invisible');
        }
    }
</script>
