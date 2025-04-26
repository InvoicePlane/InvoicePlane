<?php
$global_discount = $invoice->invoice_discount_percent > 0 ? format_amount($invoice->invoice_discount_percent) . '%' : format_currency($invoice->invoice_discount_amount);
if ($invoice_tax_rates) {
    $global_taxes = [];
    foreach ($invoice_tax_rates as $invoice_tax_rate) {
        $global_taxes[] = $invoice_tax_rate->invoice_tax_rate_name . ' (' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%): '
                          . format_currency($invoice_tax_rate->invoice_tax_rate_amount);
    }
    $global_taxes = implode('<br>', $global_taxes);
}
?>
<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('invoice'); ?> #<?php echo $invoice->invoice_number; ?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
<?php
if ($invoice->invoice_balance == 0 || $invoice->invoice_status_id >= 4) {
?>
            <button class="btn btn-success disabled">
                <i class="fa fa-check"></i> <?php echo trans('paid') ?>
            </button>
<?php
} elseif ($enable_online_payments) {
?>
            <a href="<?php echo site_url('guest/payment_information/form/' . $invoice->invoice_url_key); ?>"
               class="btn btn-primary">
                <i class="fa fa-credit-card"></i>
                <?php _trans('pay_now'); ?>
            </a>
<?php
}
?>
            <a href="<?php echo site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
               class="btn btn-default" id="btn_generate_pdf" target="_blank">
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

                        <h3><?php _htmlsc(format_client($invoice)); ?></h3>

                        <div class="client-address">
                            <?php $this->layout->load_view('clients/partial_client_address', ['client' => $invoice]); ?>
                        </div>
<?php
if ($invoice->client_phone) {
?>
                        <br><span><strong><?php _trans('phone'); ?>:</strong> <?php _htmlsc($invoice->client_phone); ?></span>
<?php
}
if ($invoice->client_email) {
?>
                        <br><span><strong><?php _trans('email'); ?>:</strong> <?php _htmlsc($invoice->client_email); ?></span>
<?php
}
?>
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
                        <tr class="<?php echo ($invoice->invoice_status_id != 4 && $invoice->invoice_date_due < date('Y-m-d')) ? 'font-overdue' : ''; ?>">
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
foreach ($items as $i => $item) {
?>
                        <tbody class="item">
                        <tr>
                            <td rowspan="2" style="width:20px;" class="text-center"><?php echo 1 + $i; ?></td>
                            <td><?php _htmlsc($item->item_name); ?></td>
                            <td>
                                <span class="pull-left"><?php _trans('quantity'); ?></span>
                                <span class="pull-right amount"><?php echo format_quantity($item->item_quantity) . ' ' . htmlsc($item->item_product_unit); ?></span>
                            </td>
                            <td>
                                <span class="pull-left"><?php _trans('price'); ?></span>
                                <span class="pull-right amount"><?php echo format_currency($item->item_price); ?></span>
                            </td>
                            <td>
                                <span class="pull-left"><?php _trans('subtotal'); ?></span>
                                <span class="pull-right amount"><?php echo format_currency($item->item_subtotal); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                            <td>
                                <span class="pull-left"><?php _trans('discount'); ?></span>
                                <span class="pull-right amount">
                                    <span data-toggle="tooltip" data-placement="bottom" title="<?php _trans('item_discount'); ?>">
                                        <?php echo format_currency($item->item_discount); ?>
                                    </span>
<?php
    // New Discount calculation - since v1.6.3
    $item_global_discount = $legacy_calculation ? 0 : $item->item_subtotal - ($item->item_total - $item->item_tax_total + $item->item_discount);
    if ($item_global_discount) {
?>
                                    <span data-toggle="tooltip" data-placement="bottom" title="<?php _trans('global_discount'); ?>">
                                        + <?php echo format_currency($item_global_discount); ?>
                                    </span>
                                    <span data-toggle="tooltip" data-placement="bottom" title="<?php _trans('discount'); ?> (<?php _trans('subtotal'); ?>)">
                                        = <?php echo format_currency($item_global_discount + $item->item_discount); ?>
                                    </span>
<?php
    }
?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?php _trans('tax'); ?></span>
                                <span class="pull-right amount"><?php
                                    echo $item->item_tax_rate_percent ? $item->item_tax_rate_name . ' (' . format_amount($item->item_tax_rate_percent) . '%): ' : '';
                                    echo format_currency($item->item_tax_total);
                                ?></span>
                            </td>
                            <td>
                                <span class="pull-left"><?php _trans('total'); ?></span>
                                <span class="pull-right amount"><?php echo format_currency($item->item_total); ?></span>
                            </td>
                        </tr>
                        </tbody>
<?php
} // End foreach items
?>
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
<?php if (! $legacy_calculation) { ?>
                        <th class="text-right"><?php _trans('global_discount'); ?></th>
<?php } ?>
                        <th class="text-right"><?php _trans('subtotal'); ?></th>
                        <th class="text-right"><?php _trans('item_tax'); ?></th>
<?php if ($invoice_tax_rates) { ?>
                        <th class="text-right"><?php _trans('invoice_tax'); ?></th>
<?php } ?>
<?php if ($legacy_calculation) { ?>
                        <th class="text-right"><?php _trans('global_discount'); ?></th>
<?php } ?>
                        <th class="text-right"><?php _trans('total'); ?></th>
                        <th class="text-right"><?php _trans('paid'); ?></th>
                        <th class="text-right"><?php _trans('balance'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
<?php if (! $legacy_calculation) { ?>
                        <td class="amount"><?php echo $global_discount; ?></td>
<?php } ?>
                        <td class="amount"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
                        <td class="amount"><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
<?php if ($invoice_tax_rates) { ?>
                        <td class="amount"><?php echo $global_taxes; ?></td>
<?php } ?>
<?php if ($legacy_calculation) { ?>
                        <td class="amount"><?php echo $global_discount; ?></td>
<?php } ?>
                        <td class="amount"><b><?php echo format_currency($invoice->invoice_total); ?></b></td>
                        <td class="amount"><?php echo format_currency($invoice->invoice_paid); ?></td>
                        <td class="amount"><?php echo format_currency($invoice->invoice_balance); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-xs-12 col-md-6">

                <?php _dropzone_html(); ?>

            </div>
<?php
if ($invoice->invoice_terms) {
?>
            <div class="col-xs-12 col-md-6">
                <strong><?php _trans('invoice_terms'); ?></strong><br/>
                <?php echo nl2br(htmlsc($invoice->invoice_terms)); ?>
            </div>
<?php
}
?>
        </div>

    </form>

</div>
<?php
_dropzone_script($invoice->invoice_url_key, $invoice->client_id, 'guest/get', false);
