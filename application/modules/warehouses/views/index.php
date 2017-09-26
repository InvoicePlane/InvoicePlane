<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('warehouses'); ?></h1>

    <div class="headerbar-item pull-right">
        <a href="#" class="warehouse-products-entry btn btn-sm btn-primary">
            <i class="fa fa-indent fa-margin"></i> <?php _trans('warehouse_products_entry'); ?>
        </a>
        
        <a href="#" class="warehouse-products-exit btn btn-sm btn-primary">
            <i class="fa fa-outdent fa-margin"></i> <?php _trans('warehouse_products_exit'); ?>
        </a>
        
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('warehouses/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('warehouses/index'), 'mdl_warehouses'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php _trans('warehouse_name'); ?></th>
                <th><?php _trans('warehouse_location'); ?></th>
                <th><?php _trans('warehouse_total_products'); ?></th>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($warehouses as $warehouse) { ?>
                <tr>
                    <td><?php echo anchor('warehouses/view/' . $warehouse->warehouse_id, htmlsc($warehouse->warehouse_name)); ?></td>
                    <td><?php echo htmlsc($warehouse->warehouse_location); ?></td>
                    <td><?php echo count($warehouse->total_products); ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('warehouses/form/' . $warehouse->warehouse_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('warehouses/delete/' . $warehouse->warehouse_id); ?>"
                                       onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>

</div>
