<div id="headerbar">
    <h1><?php echo lang('recurring_invoices'); ?></h1>

    <div class="pull-right">
        <?php echo pager(site_url('invoices/recurring/index'), 'mdl_invoices_recurring'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
        <div class="table-responsive">
            <table class="table table-striped">

                <thead>
                <tr>
                    <th><?php echo lang('status'); ?></th>
                    <th><?php echo lang('base_invoice'); ?></th>
                    <th><?php echo lang('client'); ?></th>
                    <th><?php echo lang('start_date'); ?></th>
                    <th><?php echo lang('end_date'); ?></th>
                    <th><?php echo lang('every'); ?></th>
                    <th><?php echo lang('next_date'); ?></th>
                    <th><?php echo lang('options'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($recurring_invoices as $invoice) { ?>
                    <tr>
                        <td>
                        <span class="label
                            <?php if ($invoice->recur_status == 'active') {
                            echo 'label-success';
                        } else {
                            echo 'label-default';
                        } ?>">
                            <?php echo lang($invoice->recur_status); ?>
                        </span>
                        </td>
                        <td>
                            <a href="<?php echo site_url('invoices/view/' . $invoice->invoice_id); ?>">
                                <?php echo $invoice->invoice_number; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo anchor('clients/view/' . $invoice->client_id, $invoice->client_name); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($invoice->recur_start_date); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($invoice->recur_end_date); ?>
                        </td>
                        <td>
                            <?php echo lang($recur_frequencies[$invoice->recur_frequency]); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($invoice->recur_next_date); ?></td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                   href="#">
                                    <i class="fa fa-cog"></i> <?php echo lang('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo site_url('invoices/recurring/stop/' . $invoice->invoice_recurring_id); ?>">
                                            <i class="fa fa-ban fa-margin"></i> <?php echo lang('stop'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('invoices/recurring/delete/' . $invoice->invoice_recurring_id); ?>"
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

</div>