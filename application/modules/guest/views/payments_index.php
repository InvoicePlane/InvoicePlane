<div id="headerbar">
    <h1><?php echo trans('payments'); ?></h1>

    <div class="pull-right">
        <?php echo pager(site_url('guest/payments/index'), 'mdl_payments'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <div class="table-responsive">
            <table class="table table-striped">

                <thead>
                <tr>
                    <th><?php echo trans('date'); ?></th>
                    <th><?php echo trans('invoice'); ?></th>
                    <th><?php echo trans('amount'); ?></th>
                    <th><?php echo trans('payment_method'); ?></th>
                    <th><?php echo trans('note'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($payments as $payment) { ?>
                    <tr>
                        <td><?php echo date_from_mysql($payment->payment_date); ?></td>
                        <td><?php echo $payment->invoice_number; ?></td>
                        <td><?php echo format_currency($payment->payment_amount); ?></td>
                        <td><?php echo $payment->payment_method_name; ?></td>
                        <td><?php echo $payment->payment_note; ?></td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</div>