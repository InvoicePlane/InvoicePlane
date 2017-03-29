<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <tr>
            <th>&nbsp;</th>
            <th><?php echo trans('product_sku'); ?></th>
            <th><?php echo trans('family_name'); ?></th>
            <th><?php echo trans('product_name'); ?></th>
            <th><?php echo trans('product_description'); ?></th>
            <th class="text-right"><?php echo trans('product_price'); ?></th>
        </tr>
        <?php foreach ($products as $product) { ?>
            <tr class="product">
                <td class="text-left">
                    <input type="checkbox" name="product_ids[]"
                           value="<?php echo $product->product_id; ?>">
                </td>
                <td nowrap class="text-left">
                    <b><?php echo $product->product_sku; ?></b>
                </td>
                <td>
                    <b><?php echo $product->family_name; ?></b>
                </td>
                <td>
                    <b><?php echo $product->product_name; ?></b>
                </td>
                <td>
                    <?php echo nl2br($product->product_description); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($product->product_price); ?>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>
