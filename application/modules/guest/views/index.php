<div class="headerbar">
    <h1><?php echo lang('dashboard'); ?></h1>
</div>

<div class="container-fluid">

    <div class="row-fluid">
        
        <?php if ($overdue_invoices) { ?>
        <div class="widget">

            <div class="widget-title">
                <h5><?php echo lang('overdue_invoices'); ?></h5>
            </div>
            
            <table class="table table-striped no-margin">

                <thead>
                    <tr>
                        <th><?php echo lang('invoice'); ?></th>
                        <th><?php echo lang('created'); ?></th>
                        <th><?php echo lang('due_date'); ?></th>
                        <th><?php echo lang('client_name'); ?></th>
                        <th><?php echo lang('amount'); ?></th>
                        <th><?php echo lang('balance'); ?></th>
                        <th><?php echo lang('options'); ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($overdue_invoices as $invoice) { ?>
                    <tr>
                        <td><a href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>"><?php echo $invoice->invoice_number; ?></a></td>
                        <td><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                        <td><span class="font-overdue"><?php echo date_from_mysql($invoice->invoice_date_due); ?></span></td>
                        <td><?php echo $invoice->client_name; ?></td>
                        <td><?php echo format_currency($invoice->invoice_total); ?></td>
                        <td><?php echo format_currency($invoice->invoice_balance); ?></td>
                        <td>
                            <a href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>" class="btn btn-small">
                                <i class="icon-eye-open"></i> <?php echo lang('view'); ?>
                            </a>

                            <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>" class="btn btn-small">
                                <i class="icon-print"></i> <?php echo lang('pdf'); ?>
                            </a>

                            <?php if ($this->mdl_settings->setting('merchant_enabled') == 1 and $invoice->invoice_balance > 0) { ?><a href="<?php echo site_url('guest/payment_handler/make_payment/' . $invoice->invoice_url_key); ?>" class="btn btn-small btn-success"><i class="icon-white icon-ok"></i> <?php echo lang('pay_now'); ?></a><?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>

            </table>

        </div>
        <?php } ?>
            
        <div class="widget">

            <div class="widget-title">
                <h5><?php echo lang('quotes_requiring_approval'); ?></h5>
            </div>

            <?php if ($open_quotes) { ?>
            <table class="table table-striped no-margin">

                <thead>
                    <tr>
                        <th><?php echo lang('quote'); ?></th>
                        <th><?php echo lang('created'); ?></th>
                        <th><?php echo lang('due_date'); ?></th>
                        <th><?php echo lang('client_name'); ?></th>
                        <th><?php echo lang('amount'); ?></th>
                        <th><?php echo lang('options'); ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($open_quotes as $quote) { ?>
                    <tr>
                        <td><a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>" title="<?php echo lang('edit'); ?>"><?php echo $quote->quote_number; ?></a></td>
                        <td><?php echo date_from_mysql($quote->quote_date_created); ?></td>
                        <td><?php echo date_from_mysql($quote->quote_date_expires); ?></td>
                        <td><?php echo $quote->client_name; ?></td>
                        <td><?php echo format_currency($quote->quote_total); ?></td>
                        <td>
                            <a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>" class="btn btn-small">
                                <i class="icon-search"></i> <?php echo lang('view'); ?>
                            </a>
                            <a href="<?php echo site_url('guest/quotes/generate_pdf/' . $quote->quote_id); ?>" class="btn btn-small">
                                <i class="icon-print"></i> <?php echo lang('pdf'); ?>
                            </a>
                            <?php if (in_array($quote->quote_status_id, array(2,3))) { ?>
                            <a href="<?php echo site_url('guest/quotes/approve/' . $quote->quote_id); ?>" class="btn btn-success"><i class="icon-white icon-check"></i> <?php echo lang('approve'); ?></a>
                            <a href="<?php echo site_url('guest/quotes/reject/' . $quote->quote_id); ?>" class="btn btn-danger"><i class="icon-white icon-ban-circle"></i> <?php echo lang('reject'); ?></a>
                            <?php } elseif ($quote->quote_status_id == 4) { ?>
                            <a href="#" class="btn btn-success"><?php echo lang('approved'); ?></a>
                            <?php } elseif ($quote->quote_status_id == 5) { ?>
                            <a href="#" class="btn btn-danger"><?php echo lang('rejected'); ?></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>

            </table>
            <?php } else { ?>
            <div class="padded"><?php echo lang('no_quotes_requiring_approval'); ?></div>
            <?php } ?>

        </div>

        <div class="widget">

            <div class="widget-title">
                <h5><?php echo lang('open_invoices'); ?></h5>
            </div>

            <table class="table table-striped no-margin">

                <thead>
                    <tr>
                        <th><?php echo lang('invoice'); ?></th>
                        <th><?php echo lang('created'); ?></th>
                        <th><?php echo lang('due_date'); ?></th>
                        <th><?php echo lang('client_name'); ?></th>
                        <th><?php echo lang('amount'); ?></th>
                        <th><?php echo lang('balance'); ?></th>
                        <th><?php echo lang('options'); ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($open_invoices as $invoice) { ?>
                    <tr>
                        <td><a href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>"><?php echo $invoice->invoice_number; ?></a></td>
                        <td><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                        <td><span class="<?php if ($invoice->is_overdue) { ?>font-overdue<?php } ?>"><?php echo date_from_mysql($invoice->invoice_date_due); ?></span></td>
                        <td><?php echo $invoice->client_name; ?></td>
                        <td><?php echo format_currency($invoice->invoice_total); ?></td>
                        <td><?php echo format_currency($invoice->invoice_balance); ?></td>
                        <td>
                            <a href="<?php echo site_url('guest/invoices/view/' . $invoice->invoice_id); ?>" class="btn btn-small">
                                <i class="icon-eye-open"></i> <?php echo lang('view'); ?>
                            </a>

                            <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>" class="btn btn-small">
                                <i class="icon-print"></i> <?php echo lang('pdf'); ?>
                            </a>

                            <?php if ($this->mdl_settings->setting('merchant_enabled') == 1 and $invoice->invoice_balance > 0) { ?><a href="<?php echo site_url('guest/payment_handler/make_payment/' . $invoice->invoice_url_key); ?>" class="btn btn-small btn-success"><i class="icon-white icon-ok"></i> <?php echo lang('pay_now'); ?></a><?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>

            </table>

        </div>

    </div>

</div>