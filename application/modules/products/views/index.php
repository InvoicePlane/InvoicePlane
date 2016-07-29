<div id="headerbar">
    <h1><?php echo trans('products'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('products/form'); ?>"><i
                class="fa fa-plus"></i> <?php echo trans('new'); ?></a>
    </div>

    <div class="pull-right">
        <?php echo pager(site_url('products/index'), 'mdl_products'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo trans('family'); ?></th>
                <th><?php echo trans('product_sku'); ?></th>
                <th><?php echo trans('product_name'); ?></th>
                <th><?php echo trans('product_description'); ?></th>
                <th><?php echo trans('product_price'); ?></th>
                <th><?php echo trans('tax_rate'); ?></th>
                <th><?php echo trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($products as $product) { ?>
                <tr>
                    <td><?php echo $product->family_name; ?></td>
                    <td><?php echo $product->product_sku; ?></td>
                    <td><?php echo $product->product_name; ?></td>
                    <td><?php echo nl2br($product->product_description); ?></td>
                    <td><?php echo format_currency($product->product_price); ?></td>
                    <td><?php echo ($product->tax_rate_id) ? $product->tax_rate_name : trans('none'); ?></td>
                    <td>
                        <a href="<?php echo site_url('products/form/' . $product->product_id); ?>"
                           title="<?php echo trans('edit'); ?>"><i class="fa fa-edit fa-margin"></i></a>
                        <a href="<?php echo site_url('products/delete/' . $product->product_id); ?>"
                           title="<?php echo trans('delete'); ?>"
                           onclick="return confirm('<?php echo trans('delete_record_warning'); ?>');"><i
                                class="fa fa-trash-o fa-margin"></i></a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>
</div>