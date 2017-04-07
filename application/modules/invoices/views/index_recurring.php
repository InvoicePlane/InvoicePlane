<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('recurring_invoices'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('invoices/recurring/index'), 'mdl_invoices_recurring'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
        <div class="table-responsive">
            <table class="table table-striped">

                <thead>
                <tr>
                    <th><?php _trans('status'); ?></th>
                    <th><?php _trans('base_invoice'); ?></th>
                    <th><?php _trans('client'); ?></th>
                    <th><?php _trans('start_date'); ?></th>
                    <th><?php _trans('end_date'); ?></th>
                    <th><?php _trans('every'); ?></th>
                    <th><?php _trans('next_date'); ?></th>
                    <th><?php _trans('options'); ?></th>
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
                            <?php _trans($invoice->recur_status); ?>
                        </span>
                        </td>
                        <td>
                            <a href="<?php echo site_url('invoices/view/' . $invoice->invoice_id); ?>">
                                <?php echo $invoice->invoice_number; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo anchor('clients/view/' . $invoice->client_id, htmlsc($invoice->client_name)); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($invoice->recur_start_date); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($invoice->recur_end_date); ?>
                        </td>
                        <td>
                            <?php _trans($recur_frequencies[$invoice->recur_frequency]); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($invoice->recur_next_date); ?></td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                   href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo site_url('invoices/recurring/stop/' . $invoice->invoice_recurring_id); ?>">
                                            <i class="fa fa-ban fa-margin"></i> <?php _trans('stop'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('invoices/recurring/delete/' . $invoice->invoice_recurring_id); ?>"
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

</div>
