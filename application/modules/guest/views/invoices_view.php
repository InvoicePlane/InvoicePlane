<script type="text/javascript">

    $(function () {
        $('#btn_generate_pdf').click(function () {
            window.location = '<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>';
        });
    });

</script>

<div id="headerbar">
    <h1><?php echo trans('invoice'); ?> #<?php echo $invoice->invoice_number; ?></h1>

    <div class="pull-right">
        <?php if ($invoice->invoice_status_id == 4) { ?>
            <span class="btn btn-success btn-sm disabled">
                <i class="fa fa-check"></i>
                <?php echo trans('paid') ?>
            </span>
        <?php } ?>
        <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
           class="btn btn-default btn-sm" id="btn_generate_pdf"
           data-invoice-id="<?php echo $invoice_id; ?>"
           data-invoice-balance="<?php echo $invoice->invoice_balance; ?>">
            <i class="fa fa-print"></i> <?php echo trans('download_pdf'); ?>
        </a>
    </div>

</div>

<?php echo $this->layout->load_view('layout/alerts'); ?>

<div id="content">

    <form id="invoice_form" class="form-horizontal">

        <div class="invoice">

            <div class="row">

                <div class="col-xs-12 col-md-9">
                    <div class="pull-left">

                        <h3><?php echo $invoice->client_name; ?></h3>

					<span>
						<?php echo ($invoice->client_address_1) ? $invoice->client_address_1 . '<br>' : ''; ?>
                        <?php echo ($invoice->client_address_2) ? $invoice->client_address_2 . '<br>' : ''; ?>
                        <?php echo ($invoice->client_city) ? $invoice->client_city : ''; ?>
                        <?php echo ($invoice->client_state) ? $invoice->client_state : ''; ?>
                        <?php echo ($invoice->client_zip) ? $invoice->client_zip : ''; ?>
                        <?php echo ($invoice->client_country) ? '<br>' . $invoice->client_country : ''; ?>
					</span>
                        <br><br>

                        <?php if ($invoice->client_phone) { ?>
                            <span>
                            <strong><?php echo trans('phone'); ?>:</strong>
                                <?php echo $invoice->client_phone; ?>
                        </span><br>
                        <?php } ?>

                        <?php if ($invoice->client_email) { ?>
                            <span>
                            <strong><?php echo trans('email'); ?>:</strong>
                                <?php echo $invoice->client_email; ?>
                        </span>
                        <?php } ?>

                    </div>
                </div>

                <div class="col-xs-12 col-md-3">
                    <div class="panel panel-default panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><?php echo trans('invoice'); ?> #</td>
                                <td><?php echo $invoice->invoice_number; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo trans('date'); ?></td>
                                <td><?php echo date_from_mysql($invoice->invoice_date_created); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo trans('due_date'); ?></td>
                                <td><?php echo date_from_mysql($invoice->invoice_date_due); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <br/>
            <div class="table-responsive">
                <table id="item_table" class="items table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th><?php echo trans('item'); ?> / <?php echo lang('description'); ?></th>
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
                            <td><?php echo $item->item_name; ?></td>
                            <td>
                                <span class="pull-left"><?php echo trans('quantity'); ?></span>
                                <span class="pull-right amount"><?php echo $item->item_quantity; ?></span>
                            </td>
                            <td>
                                <span class="pull-left"><?php echo trans('item_discount'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_discount); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?php echo trans('subtotal'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_subtotal); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><?php echo nl2br($item->item_description); ?></td>
                            <td>
                                <span class="pull-left"><?php echo trans('price'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_price); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?php echo trans('tax'); ?></span>
                                <span class="pull-right amount">
                                    <?php echo format_currency($item->item_tax_total); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?php echo trans('total'); ?></span>
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
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th class="text-right"><?php echo trans('subtotal'); ?></th>
                        <th class="text-right"><?php echo trans('item_tax'); ?></th>
                        <th class="text-right"><?php echo trans('invoice_tax'); ?></th>
                        <th class="text-right"><?php echo trans('discount'); ?></th>
                        <th class="text-right"><?php echo trans('total'); ?></th>
                        <th class="text-right"><?php echo trans('paid'); ?></th>
                        <th class="text-right"><?php echo trans('balance'); ?></th>
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
                    <strong><?php echo trans('invoice_terms'); ?></strong><br/>
                    <?php echo nl2br($invoice->invoice_terms); ?>
                </p>
            <?php endif; ?>

        </div>

    </form>

</div>