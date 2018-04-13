<div class="table-responsive">
            <table class="table table-striped">

                <thead>
                <tr>
                    <th><?php _trans('date'); ?></th>
                    <th><?php _trans('invoice'); ?></th>
                    <th><?php _trans('amount'); ?></th>
                    <th><?php _trans('payment_method'); ?></th>
                    <th><?php _trans('note'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($payments as $payment) { ?>
                    <tr>
                        <td><?php echo date_from_mysql($payment->payment_date); ?></td>
                        <td>
                            <a href="<?php echo site_url('guest/invoices/view/' . $payment->invoice_id); ?>">
                                <?php echo $payment->invoice_number; ?>
                            </a>
                        </td>
                        <td><?php echo format_currency($payment->payment_amount); ?></td>
                        <td><?php echo $payment->payment_method_name; ?></td>
                        <td><?php _htmlsc($payment->payment_note); ?></td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>