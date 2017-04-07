<div class="table-responsive">
    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php _trans('payment_date'); ?></th>
            <th><?php _trans('invoice_date'); ?></th>
            <th><?php _trans('invoice'); ?></th>
            <th><?php _trans('client'); ?></th>
            <th><?php _trans('amount'); ?></th>
            <th><?php _trans('payment_method'); ?></th>
            <th><?php _trans('note'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($payments as $payment) { ?>
            <tr>
                <td><?php echo date_from_mysql($payment->payment_date); ?></td>
                <td><?php echo date_from_mysql($payment->invoice_date_created); ?></td>
                <td><?php echo anchor('invoices/view/' . $payment->invoice_id, $payment->invoice_number); ?></td>
                <td>
                    <a href="<?php echo site_url('clients/view/' . $payment->client_id); ?>"
                       title="<?php _trans('view_client'); ?>">
                        <?php _htmlsc(format_client($payment)); ?>
                    </a>
                </td>
                <td><?php echo format_currency($payment->payment_amount); ?></td>
                <td><?php _htmlsc($payment->payment_method_name); ?></td>
                <td><?php _htmlsc($payment->payment_note); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('payments/form/' . $payment->payment_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i>
                                    <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('payments/delete/' . $payment->payment_id); ?>"
                                   onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i>
                                    <?php _trans('delete'); ?>
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
