        <div class="table-responsive">
            <table class="table table-hover table-striped">

                <thead>
                <tr>
                    <th><?php _trans('id'); ?></th>
                    <th><?php _trans('invoice'); ?></th>
                    <th><?php _trans('transaction_successful'); ?></th>
                    <th><?php _trans('payment_date'); ?></th>
                    <th><?php _trans('payment_provider'); ?></th>
                    <th><?php _trans('provider_response'); ?></th>
                    <th><?php _trans('transaction_reference'); ?></th>
                </tr>
                </thead>

                <tbody>
<?php
foreach ($payment_logs as $log) {
?>
                    <tr>
                        <td><?php echo $log->merchant_response_id; ?></td>
                        <td>
                            <a href="<?php echo site_url('invoices/view/' . $log->invoice_id); ?>"
                               title="<?php _trans('invoice'); ?>">
                                <?php echo($log->invoice_number ? $log->invoice_number : $log->invoice_id); ?>
                            </a>
                        </td>
                        <td>
                            <?php
                            echo $log->merchant_response_successful
                                ? '<i class="fa fa-check text-success"></i>'
                                : '<i class="fa fa-ban text-danger"></i>';
                            ?>
                        </td>
                        <td><?php echo date_from_mysql($log->merchant_response_date); ?></td>
                        <td><?php echo $log->merchant_response_driver; ?></td>
                        <td class="small text-<?php echo $log->merchant_response_successful ? 'success' : 'danger'; ?>">
                            <?php echo $log->merchant_response; ?>
                        </td>
                        <td><?php echo $log->merchant_response_reference; ?></td>
                    </tr>
<?php
} // End foreach
?>
                </tbody>

            </table>
        </div>
