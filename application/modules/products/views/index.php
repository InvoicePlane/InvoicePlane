<div id="headerbar">
    <h1><?php echo lang('products'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('products/form'); ?>"><i
                class="fa fa-plus"></i> <?php echo lang('new'); ?></a>
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
                <th><?php echo lang('family'); ?></th>
                <th><?php echo lang('product_sku'); ?></th>
                <th><?php echo lang('product_name'); ?></th>
                <th><?php echo lang('product_description'); ?></th>
                <th><?php echo lang('product_price'); ?></th>
                <th><?php echo lang('tax_rate'); ?></th>
                <th><?php echo lang('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($products as $product) { ?>
                <tr>
                    <td><?php echo $product->family_name; ?></td>
                    <td><?php echo $product->product_sku; ?></td>
                    <td><?php echo $product->product_name; ?></td>
                    <td><?php echo nl2br($product->product_description); ?></td>
                    <td><?php echo format_currency($product->product_price, $this->mdl_settings->setting('item_price_decimal_places')); ?></td>
                    <td><?php echo ($product->tax_rate_id) ? $product->tax_rate_name : lang('none'); ?></td>
                    <td>
                        <a href="<?php echo site_url('products/form/' . $product->product_id); ?>"
                           title="<?php echo lang('edit'); ?>"><i class="fa fa-edit fa-margin"></i></a>
                        <a href="<?php echo site_url('products/delete/' . $product->product_id); ?>"
                           title="<?php echo lang('delete'); ?>"
                           onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');"><i
                                class="fa fa-trash-o fa-margin"></i></a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>
</div>