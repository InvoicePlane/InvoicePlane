<div id="headerbar">
    <h1 class="headerbar-title"><?php echo $warehouse->warehouse_name; ?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a href="<?php echo site_url('warehouses/form/' . $warehouse->warehouse_id); ?>" class="btn btn-default">
                <i class="fa fa-edit"></i> <?php _trans('edit'); ?>
            </a>
            <a class="btn btn-danger"
               href="<?php echo site_url('warehouses/delete/' . $warehouse->warehouse_id); ?>"
               onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                <i class="fa fa-trash-o"></i> <?php _trans('delete'); ?>
            </a>
        </div>
    </div>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php _trans('products'); ?>
                </div>
                <div class="panel-body no-padding">

                    <div class="table-responsive">
                        <table class="table table-striped no-margin">

                            <thead>
                            <tr>
                                <th><?php _trans('family'); ?></th>
                                <th><?php _trans('product_sku'); ?></th>
                                <th><?php _trans('product_name'); ?></th>
                                <th><?php _trans('product_description'); ?></th>
                                <th><?php _trans('product_price'); ?></th>
                                <th><?php _trans('product_qty'); ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($products as $product) { ?>
                                <tr>
                                    <td>
                                        <?php echo htmlsc($product->family_name); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlsc($product->product_sku); ?>
                                    </td>
                                    <td>
                                        <?php echo anchor('products/form/' . $product->product_id, htmlsc($product->product_name)) ?>
                                    </td>
                                    <td>
                                        <?php echo htmlsc($product->product_description); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlsc($product->product_price); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlsc($product->product_qty); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
