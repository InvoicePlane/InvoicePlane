<script type="text/javascript">

    $(function () {
        $('#btn_generate_pdf').click(function () {
            window.location = '<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>';
        });
    });

</script>

<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('invoice'); ?> #<?php echo $invoice->invoice_number; ?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <?php if ($invoice->invoice_status_id == 4) { ?>
                <button class="btn btn-success disabled">
                    <i class="fa fa-check"></i> <?php echo trans('paid') ?>
                </button>
            <?php } ?>
            <?php if ($invoice->invoice_status_id != 4 && get_setting('enable_online_payments') == 'on') : ?>
                <a href="<?php echo site_url('guest/payment_information/form/' . $invoice->invoice_url_key); ?>"
                   class="btn btn-primary">
                    <i class="fa fa-credit-card"></i>
                    <?php _trans('pay_now'); ?>
                </a>
            <?php endif; ?>
            <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
               class="btn btn-default" id="btn_generate_pdf"
               data-invoice-id="<?php echo $invoice_id; ?>"
               data-invoice-balance="<?php echo $invoice->invoice_balance; ?>">
                <i class="fa fa-print"></i> <?php _trans('download_pdf'); ?>
            </a>
        </div>
    </div>

</div>

<div id="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <form id="invoice_form" class="form-horizontal">

        <div class="invoice">

            <div class="row">

                <div class="col-xs-12 col-md-9 clearfix">
                    <div class="pull-left">

                        <h3><?php echo format_client($invoice); ?></h3>

                        <div class="client-address">
                            <?php $this->layout->load_view('clients/partial_client_address', array('client' => $invoice)); ?>
                        </div>
                        <br><br>

                        <?php if ($invoice->client_phone) { ?>
                            <span>
                            <strong><?php _trans('phone'); ?>:</strong>
                                <?php _htmlsc($invoice->client_phone); ?>
                        </span><br>
                        <?php } ?>

                        <?php if ($invoice->client_email) { ?>
                            <span>
                            <strong><?php _trans('email'); ?>:</strong>
                                <?php _htmlsc($invoice->client_email); ?>
                        </span>
                        <?php } ?>

                    </div>
                </div>

                <div class="col-xs-12 col-md-3">

                    <table class="table table-bordered">
                        <tr>
                            <td><?php _trans('invoice'); ?> #</td>
                            <td><?php echo $invoice->invoice_number; ?></td>
                        </tr>
                        <tr>
                            <td><?php _trans('date'); ?></td>
                            <td><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                        </tr>
                        <tr>
                            <td><?php _trans('due_date'); ?></td>
                            <td><?php echo date_from_mysql($invoice->invoice_date_due); ?></td>
                        </tr>
                    </table>

                </div>
            </div>

            <br/>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th><?php _trans('item'); ?> / <?php echo lang('description'); ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <?php
                    $i = 1;
                    foreach ($items as $item) { ?>
                        <tbody class="item">
                        <tr>
                            <td rowspan="2" style="max-width: 20px;" class="text-center">
                                <?php echo $i;
                                $i++; ?>
                            </td>
                            <td><?php _htmlsc($item->item_name); ?></td>
                            <td>
                                <span class="pull-left"><?php _trans('quantity'); ?></span>
                                <span class="pull-right amount"><?php echo $item->item_quantity; ?></span>
                            </td>
                            <td>
                                <span class="pull-left"><?php _trans('item_discount'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_discount); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?php _trans('subtotal'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_subtotal); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                            <td>
                                <span class="pull-left"><?php _trans('price'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_price); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?php _trans('tax'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_tax_total); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?php _trans('total'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_total); ?>
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    <?php } ?>
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-right"><?php _trans('subtotal'); ?></th>
                        <th class="text-right"><?php _trans('item_tax'); ?></th>
                        <th class="text-right"><?php _trans('invoice_tax'); ?></th>
                        <th class="text-right"><?php _trans('discount'); ?></th>
                        <th class="text-right"><?php _trans('total'); ?></th>
                        <th class="text-right"><?php _trans('paid'); ?></th>
                        <th class="text-right"><?php _trans('balance'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="amount"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
                        <td class="amount"><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
                        <td class="amount">
                            <?php if ($invoice_tax_rates) {
                                foreach ($invoice_tax_rates as $invoice_tax_rate) { ?>
                                    <?php echo $invoice_tax_rate->invoice_tax_rate_name . ' ' . $invoice_tax_rate->invoice_tax_rate_percent; ?>%:
                                    <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?><br>
                                <?php }
                            } else {
                                echo format_currency('0');
                            } ?>
                        </td>
                        <td class="amount"><?php
                            if ($invoice->invoice_discount_amount == floatval(0)) {
                                echo $invoice->invoice_discount_percent . '%';
                            } else {
                                echo format_currency($invoice->invoice_discount_amount);
                            }
                            ?>
                        </td>
                        <td class="amount"><?php echo format_currency($invoice->invoice_total); ?></td>
                        <td class="amount"><?php echo format_currency($invoice->invoice_paid); ?></td>
                        <td class="amount"><?php echo format_currency($invoice->invoice_balance); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <?php if ($invoice->invoice_terms): ?>
                <p>
                    <strong><?php _trans('invoice_terms'); ?></strong><br/>
                    <?php echo nl2br(htmlsc($invoice->invoice_terms)); ?>
                </p>
            <?php endif; ?>

        </div>

    </form>

</div>
