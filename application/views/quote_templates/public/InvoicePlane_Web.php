<!doctype html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php
        if ($this->mdl_settings->setting('custom_title') != '') {
            echo $this->mdl_settings->setting('custom_title');
        } else {
            echo 'quotePlane';
        } ?> - <?php echo trans('quote'); ?> <?php echo $quote->quote_number; ?></title>

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom.css">

</head>
<body>

<div class="container">

    <div id="content">

        <div class="webpreview-header">

            <h2><?php echo trans('quote') . ' ' . $quote->quote_number; ?></h2>

            <div class="btn-group">
                <a href="<?php echo site_url('guest/view/generate_quote_pdf/' . $quote_url_key); ?>"
                   class="btn btn-primary">
                    <i
                        class="fa fa-print"></i> <?php echo trans('download_pdf'); ?>
                </a>
                <?php if ($this->mdl_settings->setting('merchant_enabled') == 1 and $quote->quote_balance > 0) { ?>
                <a
                    href="<?php echo site_url('guest/payment_handler/make_payment/' . $quote_url_key); ?>"
                    class="btn btn-success"><i class="fa fa-credit-card"></i> <?php echo trans('pay_now'); ?>
                    </a><?php } ?>
            </div>

        </div>

        <hr>

        <?php if ($flash_message) { ?>
            <div class="alert alert-info">
                <?php echo $flash_message; ?>
            </div>
        <?php } else {
            echo '<br>';
        } ?>

        <div class="quote">

            <?php
            $logo = invoice_logo();
            if ($logo) {
                echo $logo . '<br><br>';
            }
            ?>

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-5">

                    <h4><?php echo $quote->user_name; ?></h4>
                    <p><?php if ($quote->user_vat_id) {
                            echo lang("vat_id_short") . ": " . $quote->user_vat_id . '<br>';
                        } ?>
                        <?php if ($quote->user_tax_code) {
                            echo lang("tax_code_short") . ": " . $quote->user_tax_code . '<br>';
                        } ?>
                        <?php if ($quote->user_address_1) {
                            echo $quote->user_address_1 . '<br>';
                        } ?>
                        <?php if ($quote->user_address_2) {
                            echo $quote->user_address_2 . '<br>';
                        } ?>
                        <?php if ($quote->user_city) {
                            echo $quote->user_city . ' ';
                        } ?>
                        <?php if ($quote->user_state) {
                            echo $quote->user_state . ' ';
                        } ?>
                        <?php if ($quote->user_zip) {
                            echo $quote->user_zip . '<br>';
                        } ?>
                        <?php if ($quote->user_phone) { ?><?php echo trans('phone_abbr'); ?>: <?php echo $quote->user_phone; ?>
                            <br><?php } ?>
                        <?php if ($quote->user_fax) { ?><?php echo trans('fax_abbr'); ?>: <?php echo $quote->user_fax; ?><?php } ?>
                    </p>

                </div>
                <div class="col-lg-2"></div>
                <div class="col-xs-12 col-md-6 col-lg-5 text-right">

                    <h4><?php echo $quote->client_name; ?></h4>
                    <p><?php if ($quote->client_vat_id) {
                            echo lang("vat_id_short") . ": " . $quote->client_vat_id . '<br>';
                        } ?>
                        <?php if ($quote->client_tax_code) {
                            echo lang("tax_code_short") . ": " . $quote->client_tax_code . '<br>';
                        } ?>
                        <?php if ($quote->client_address_1) {
                            echo $quote->client_address_1 . '<br>';
                        } ?>
                        <?php if ($quote->client_address_2) {
                            echo $quote->client_address_2 . '<br>';
                        } ?>
                        <?php if ($quote->client_city) {
                            echo $quote->client_city . ' ';
                        } ?>
                        <?php if ($quote->client_state) {
                            echo $quote->client_state . ' ';
                        } ?>
                        <?php if ($quote->client_zip) {
                            echo $quote->client_zip . '<br>';
                        } ?>
                        <?php if ($quote->client_phone) {
                            echo trans('phone_abbr') . ': ' . $quote->client_phone; ?>
                            <br>
                        <?php } ?>
                    </p>

                    <br>

                    <table class="table table-condensed">
                        <tbody>
                        <tr>
                            <td><?php echo trans('quote_date'); ?></td>
                            <td style="text-align:right;"><?php echo date_from_mysql($quote->quote_date_created); ?></td>
                        </tr>
                        <tr class="<?php echo($is_expired ? 'overdue' : '') ?>">
                            <td><?php echo trans('expires'); ?></td>
                            <td class="text-right">
                                <?php echo date_from_mysql($quote->quote_date_expires); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo trans('total'); ?></td>
                            <td class="text-right"><?php echo format_currency($quote->quote_total); ?></td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <br>

            <div class="quote-items">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th><?php echo trans('item'); ?></th>
                            <th><?php echo trans('description'); ?></th>
                            <th class="text-right"><?php echo trans('qty'); ?></th>
                            <th class="text-right"><?php echo trans('price'); ?></th>
                            <th class="text-right"><?php echo trans('discount'); ?></th>
                            <th class="text-right"><?php echo trans('total'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?php echo $item->item_name; ?></td>
                                <td><?php echo nl2br($item->item_description); ?></td>
                                <td class="amount"><?php echo format_amount($item->item_quantity); ?></td>
                                <td class="amount"><?php echo format_currency($item->item_price); ?></td>
                                <td class="amount"><?php echo format_currency($item->item_discount); ?></td>
                                <td class="amount"><?php echo format_currency($item->item_total); ?></td>
                            </tr>
                        <?php endforeach ?>
                        <tr>
                            <td colspan="4"></td>
                            <td class="text-right"><?php echo trans('subtotal'); ?>:</td>
                            <td class="amount"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
                        </tr>

                        <?php if ($quote->quote_item_tax_total > 0) { ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="text-right"><?php echo trans('item_tax'); ?></td>
                                <td class="amount"><?php echo format_currency($quote->quote_item_tax_total); ?></td>
                            </tr>
                        <?php } ?>

                        <?php foreach ($quote_tax_rates as $quote_tax_rate) : ?>
                            <tr>
                                <td class="no-bottom-border" colspan="4"></td>
                                <td class="text-right">
                                    <?php echo $quote_tax_rate->quote_tax_rate_name . ' ' . format_amount($quote_tax_rate->quote_tax_rate_percent); ?>
                                    %
                                </td>
                                <td class="amount"><?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?></td>
                            </tr>
                        <?php endforeach ?>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="text-right"><?php echo trans('discount'); ?>:</td>
                            <td class="amount">
                                <?php
                                if ($quote->quote_discount_percent > 0) {
                                    echo format_amount($quote->quote_discount_percent) . ' %';
                                } else {
                                    echo format_amount($quote->quote_discount_amount);
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="text-right"><?php echo trans('total'); ?></td>
                            <td class="amount"><?php echo format_currency($quote->quote_total) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row">

                    <?php if ($quote->notes) { ?>
                        <div class="col-xs-12 col-md-6">
                            <h4><?php echo trans('notes'); ?></h4>
                            <p><?php echo nl2br($quote->notes); ?></p>
                        </div>
                    <?php } ?>

                    <?php
                    if (count($attachments) > 0) { ?>
                        <div class="col-xs-12 col-md-6">
                            <h4><?php echo trans('attachments'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <?php foreach ($attachments as $attachment) { ?>
                                        <tr class="attachments">
                                            <td><?php echo $attachment['name']; ?></td>
                                            <td>
                                                <a href="<?php echo $attachment['fullpath']; ?>"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fa fa-download"></i> <?php echo trans('download') ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
