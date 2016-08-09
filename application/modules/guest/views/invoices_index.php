<div id="headerbar">

    <h1><?php echo trans('invoices'); ?></h1>

    <div class="pull-right">
        <?php echo pager(site_url('guest/invoices/status/' . $this->uri->segment(4)), 'mdl_invoices'); ?>
    </div>

    <div class="pull-right">
        <ul class="nav nav-pills index-options">
            <li <?php if ($status == 'open') { ?>class="active"<?php } ?>>
                <a href="<?php echo site_url('guest/invoices/status/open'); ?>">
                    <?php echo trans('open'); ?>
                </a>
            </li>
            <li <?php if ($status == 'paid') { ?>class="active"<?php } ?>>
                <a href="<?php echo site_url('guest/invoices/status/paid'); ?>">
                    <?php echo trans('paid'); ?>
                </a>
            </li>
        </ul>
    </div>

</div>

<div id="content" class="table-content">

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
                            <?php echo $invoice->client_name; ?>
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
                                <i class="glyphicon glyphicon-eye-open"></i>
                                <?php echo trans('view'); ?>
                            </a>

                            <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
                               class="btn btn-default btn-sm">
                                <i class="icon ion-printer"></i>
                                <?php echo trans('pdf'); ?>
                            </a>

                            <?php if ($this->mdl_settings->setting('merchant_enabled') == 1 and $invoice->invoice_balance > 0) { ?>
                            <a href="<?php echo site_url('guest/payment_handler/make_payment/' . $invoice->invoice_url_key); ?>"
                               class="btn btn-success btn-sm">
                                <i class="glyphicon glyphicon-ok"></i>
                                <?php echo trans('pay_now'); ?>
                                </a><?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</div>