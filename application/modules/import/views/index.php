<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('import_data'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('import/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('import/index'), 'mdl_import'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php _trans('id'); ?></th>
                <th><?php _trans('date'); ?></th>
                <th><?php _trans('clients'); ?></th>
                <th><?php _trans('invoices'); ?></th>
                <th><?php _trans('invoice_items'); ?></th>
                <th><?php _trans('payments'); ?></th>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($imports as $import) { ?>
                <tr>
                    <td><?php echo $import->import_id; ?></td>
                    <td><?php echo $import->import_date; ?></td>
                    <td><?php echo $import->num_clients; ?></td>
                    <td><?php echo $import->num_invoices; ?></td>
                    <td><?php echo $import->num_invoice_items; ?></td>
                    <td><?php echo $import->num_payments; ?></td>
                    <td>
                        <div class="options btn-group btn-group-sm">
                            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('import/delete/' . $import->import_id); ?>"
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
