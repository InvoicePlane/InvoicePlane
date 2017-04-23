<div id="headerbar">

    <h1 class="headerbar-title"><?php _trans('invoices'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('guest/invoices/status/' . $this->uri->segment(4)), 'mdl_invoices'); ?>
    </div>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm index-options">
            <a href="<?php echo site_url('guest/invoices/status/open'); ?>"
               class="btn <?php echo $status == 'open' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('open'); ?>
            </a>
            <a href="<?php echo site_url('guest/invoices/status/paid'); ?>"
               class="btn  <?php echo $status == 'paid' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('paid'); ?>
            </a>
        </div>
    </div>

</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <div class="table-responsive">
            <table class="table table-striped">

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
                <?php foreach ($invoices as $invoice) { ?>
                    <tr>
                        <td>
                            <a href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>">
                                <?php echo $invoice->invoice_number; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo date_from_mysql($invoice->invoice_date_created); ?>
                        </td>
                        <td>
                            <?php echo date_from_mysql($invoice->invoice_date_due); ?>
                        </td>
                        <td>
                            <?php _htmlsc(format_client($invoice)); ?>
                        </td>
                        <td>
                            <?php echo format_currency($invoice->invoice_total); ?>
                        </td>
                        <td>
                            <?php echo format_currency($invoice->invoice_balance); ?>
                        </td>
                        <td>
                            <div class="options btn-group btn-group-sm">
                                <?php if ($invoice->invoice_status_id != 4 && get_setting('enable_online_payments')) : ?>
                                    <a href="<?php echo site_url('guest/payment_handler/make_payment/' . $invoice->invoice_url_key); ?>"
                                       class="btn btn-primary">
                                        <i class="fa fa-credit-card"></i>
                                        <?php _trans('pay_now'); ?>
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>"
                                   class="btn btn-default">
                                    <i class="fa fa-eye"></i>
                                    <?php _trans('view'); ?>
                                </a>
                                <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
                                   class="btn btn-default">
                                    <i class="fa fa-print"></i>
                                    <?php _trans('pdf'); ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</div>
