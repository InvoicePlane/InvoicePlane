<div id="headerbar">
    <h1><?php echo lang('import_data'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('import/form'); ?>">
            <i class="fa fa-plus"></i> <?php echo lang('new'); ?>
        </a>
    </div>

    <div class="pull-right">
        <?php echo pager(site_url('import/index'), 'mdl_import'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo lang('id'); ?></th>
                <th><?php echo lang('date'); ?></th>
                <th><?php echo lang('clients'); ?></th>
                <th><?php echo lang('invoices'); ?></th>
                <th><?php echo lang('invoice_items'); ?></th>
                <th><?php echo lang('payments'); ?></th>
                <th><?php echo lang('options'); ?></th>
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
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><i
                                    class="fa fa-cog"></i> <?php echo lang('options'); ?></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('import/delete/' . $import->import_id); ?>"
                                       onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
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