<div id="headerbar">

    <h1 class="headerbar-title"><?php echo trans('invoices'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('guest/invoices/status/' . $this->uri->segment(4)), 'mdl_invoices'); ?>
    </div>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm index-options">
            <a href="<?php echo site_url('guest/invoices/status/open'); ?>"
               class="btn <?php echo $status == 'open' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('open'); ?>
            </a>
            <a href="<?php echo site_url('guest/invoices/status/paid'); ?>"
               class="btn  <?php echo $status == 'paid' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('paid'); ?>
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
                    <th><?php echo trans('invoice'); ?></th>
                    <th><?php echo trans('created'); ?></th>
                    <th><?php echo trans('due_date'); ?></th>
                    <th><?php echo trans('client_name'); ?></th>
                    <th><?php echo trans('amount'); ?></th>
                    <th><?php echo trans('balance'); ?></th>
                    <th><?php echo trans('options'); ?></th>
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
                            <?php echo format_client($invoice); ?>
                        </td>
                        <td>
                            <?php echo format_currency($invoice->invoice_total); ?>
                        </td>
                        <td>
                            <?php echo format_currency($invoice->invoice_balance); ?>
                        </td>
                        <td>
                            <a href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>"
                               class="btn btn-default btn-sm">
                                <i class="fa fa-eye"></i>
                                <?php echo trans('view'); ?>
                            </a>

                            <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
                               class="btn btn-default btn-sm">
                                <i class="fa fa-print"></i>
                                <?php echo trans('pdf'); ?>
                            </a>

                            <?php if ($invoice->invoice_status_id != 4) { ?>
                                <a href="<?php echo site_url('guest/payment_information/form/' . $invoice->invoice_url_key); ?>"
                                   class="btn btn-success btn-sm">
                                    <i class="fa fa-credit-card"></i>
                                    <?php echo trans('pay_now'); ?>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</div>
