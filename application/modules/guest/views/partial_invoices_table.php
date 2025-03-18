        <div class="table-responsive">
            <table class="table table-hover table-striped">

                <thead>
                    <tr>
                        <th><?php _trans('invoice'); ?></th>
                        <th><?php _trans('created'); ?></th>
                        <th><?php _trans('due_date'); ?></th>
                        <th><?php _trans('client_name'); ?></th>
                        <th><?php _trans('amount'); ?></th>
                        <th><?php _trans('balance'); ?></th>
                        <th><?php _trans('options'); ?></th>
                    </tr>
                </thead>

                <tbody>
<?php
foreach ($invoices as $invoice)
{
    $css_class = ($invoice->invoice_status_id != 4 && $invoice->invoice_date_due < date('Y-m-d')) ? 'font-overdue' : '';
?>
                    <tr>
                        <td>
                            <a href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>">
                                <?php echo $invoice->invoice_number; ?>
                            </a>
                        </td>
                        <td><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                        <td class="<?php echo $css_class ?>"><?php echo date_from_mysql($invoice->invoice_date_due); ?></td>
                        <td><?php _htmlsc(format_client($invoice)); ?></td>
                        <td><?php echo format_currency($invoice->invoice_total); ?></td>
                        <td><?php echo format_currency($invoice->invoice_balance); ?></td>
                        <td>
                            <div class="options btn-group btn-group-sm">
                                <a class="btn btn-default" href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>">
                                    <i class="fa fa-eye"></i> <?php _trans('view'); ?>
                                </a>
                                <a class="btn btn-default" target="_blank" href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>">
                                    <i class="fa fa-print"></i> <?php _trans('pdf'); ?>
                                </a>
<?php
    // fix 404 when balance = 0.00
    if ($enable_online_payments && $invoice->invoice_balance > 0 && $invoice->invoice_status_id != 4)
    {
?>
                                <a class="btn btn-primary" href="<?php echo site_url('guest/payment_information/form/' . $invoice->invoice_url_key); ?>">
                                    <i class="fa fa-credit-card"></i> <?php _trans('pay_now'); ?>
                                </a>
<?php
    }
    elseif ($invoice->invoice_balance == 0)
    {
?>
                                <button class="btn btn-success disabled">
                                    <i class="fa fa-check"></i> <?php _trans('paid') ?>
                                </button>
<?php
    }
?>

                            </div>
                        </td>
                    </tr>
<?php
} // End foreach
?>
                </tbody>

            </table>
        </div>
