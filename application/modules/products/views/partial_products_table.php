    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th><?php _trans('family'); ?></th>
                <th><?php _trans('product_sku'); ?></th>
                <th><?php _trans('product_name'); ?></th>
                <th><?php _trans('product_description'); ?></th>
                <th class="amount last"><?php _trans('product_price'); ?></th>
                <th><?php _trans('product_unit'); ?></th>
                <th><?php _trans('tax_rate'); ?></th>
<?php
$sumex_active = get_setting('sumex') == '1';
if ($sumex_active) {
?>
                <th><?php _trans('product_tariff'); ?></th>
<?php
}
?>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
<?php
foreach ($products as $product) {
?>
                <tr>
                    <td><a href="<?php echo site_url('families/form/' . $product->family_id); ?>"><i class="fa fa-edit"></i> <?php _htmlsc($product->family_name); ?></a></td>
                    <td><?php _htmlsc($product->product_sku); ?></td>
                    <td><a href="<?php echo site_url('products/form/' . $product->product_id); ?>"><i class="fa fa-edit"></i> <?php _htmlsc($product->product_name); ?></a></td>
                    <td><?php echo nl2br(htmlsc($product->product_description)); ?></td>
                    <td class="amount last"><?php echo format_currency($product->product_price); ?></td>
                    <td><?php _htmlsc($product->unit_name); ?></td>
                    <td><?php echo ($product->tax_rate_id) ? htmlsc($product->tax_rate_name) : trans('none'); ?></td>
<?php
    if ($sumex_active) {
?>
                    <td><?php _htmlsc($product->product_tariff); ?></td>
<?php
    } // endif
?>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('products/form/' . $product->product_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <form action="<?php echo site_url('products/delete/' . $product->product_id); ?>"
                                          method="POST">
                                        <?php _csrf_field(); ?>
                                        <button type="submit" class="dropdown-button"
                                                onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
<?php
} // End foreach
?>
            </tbody>

        </table>
    </div>
