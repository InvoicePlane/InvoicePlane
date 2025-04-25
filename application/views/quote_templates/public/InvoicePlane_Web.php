<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>
        <?php echo get_setting('custom_title', 'InvoicePlane', true); ?>
        - <?php _trans('quote'); ?> <?php echo $quote->quote_number; ?>
    </title>

    <link rel="icon" href="<?php _core_asset('img/favicon.png'); ?>" type="image/png">
    <link rel="stylesheet" href="<?php _theme_asset('css/style.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php _core_asset('css/custom.css'); ?>" type="text/css">
</head>
<body>

    <div class="container">

        <div id="content">

            <div class="webpreview-header">

                <h2><?php _trans('quote'); ?>&nbsp;<?php echo $quote->quote_number; ?></h2>

                <div class="btn-group">
<?php if (isset($_SESSION['user_id'], $_SESSION['user_type'])) { ?>
                    <a href="<?php echo site_url($_SESSION['user_type'] > 1 ? 'guest' : ''); ?>"
                       class="btn btn-default" title="<?php _trans('dashboard'); ?>">
                        <i class="fa fa-dashboard"></i> <?php _trans('dashboard'); ?>
                    </a>
<?php } ?>
<?php if (in_array($quote->quote_status_id, [2, 3])) { ?>
                    <a href="<?php echo site_url('guest/view/approve_quote/' . $quote_url_key); ?>"
                       class="btn btn-success">
                        <i class="fa fa-check"></i><?php _trans('approve_this_quote'); ?>
                    </a>
                    <a href="<?php echo site_url('guest/view/reject_quote/' . $quote_url_key); ?>"
                       class="btn btn-danger">
                        <i class="fa fa-times-circle"></i><?php _trans('reject_this_quote'); ?>
                    </a>
<?php } ?>
                    <a href="<?php echo site_url('guest/view/generate_quote_pdf/' . $quote_url_key); ?>"
                       class="btn btn-primary">
                        <i class="fa fa-print"></i> <?php _trans('download_pdf'); ?>
                    </a>
                </div>

            </div>

            <hr>

<?php
if ($flash_message) {
?>
            <div class="alert alert-info">
                <?php echo $flash_message; ?>
            </div>
<?php
} else {
    echo '<br>';
}
?>

            <div class="quote">

<?php
if ($logo = invoice_logo()) {
    echo $logo . '<br><br>';
}
?>

                <div class="row">
                    <div class="col-xs-12 col-md-6 col-lg-5">

                        <h4><?php _htmlsc($quote->user_name); ?></h4>
                        <p><?php
                           if ($quote->user_vat_id) {
                                echo lang('vat_id_short') . ': ' . $quote->user_vat_id . '<br>';
                           }
                           if ($quote->user_tax_code) {
                               echo lang('tax_code_short') . ': ' . $quote->user_tax_code . '<br>';
                           }
                           if ($quote->user_address_1) {
                               echo htmlsc($quote->user_address_1) . '<br>';
                           }
                           if ($quote->user_address_2) {
                               echo htmlsc($quote->user_address_2) . '<br>';
                           }
                           if ($quote->user_city) {
                               echo htmlsc($quote->user_city) . ' ';
                           }
                           if ($quote->user_state) {
                               echo htmlsc($quote->user_state) . ' ';
                           }
                           if ($quote->user_zip) {
                               echo htmlsc($quote->user_zip) . '<br>';
                           }
                           if ($quote->user_phone) {
                                _trans('phone_abbr'); ?>: <?php echo htmlsc($quote->user_phone); ?>
                                <br>
                           <?php }
                           if ($quote->user_fax) {
                                _trans('fax_abbr'); ?>: <?php echo htmlsc($quote->user_fax);
                           }
?>
                        </p>

                    </div>
                    <div class="col-lg-2"></div>
                    <div class="col-xs-12 col-md-6 col-lg-5 text-right">

                        <h4><?php _htmlsc($quote->client_name); ?></h4>
                        <p><?php
                        if ($quote->client_vat_id) {
                            _trans('vat_id_short');
                            echo ': ' . $quote->client_vat_id . '<br>';
                        }
                        if ($quote->client_tax_code) {
                            _trans('tax_code_short');
                            echo ': ' . $quote->client_tax_code . '<br>';
                        }
                        if ($quote->client_address_1) {
                            echo htmlsc($quote->client_address_1) . '<br>';
                        }
                        if ($quote->client_address_2) {
                            echo htmlsc($quote->client_address_2) . '<br>';
                        }
                        if ($quote->client_city) {
                            echo htmlsc($quote->client_city) . ' ';
                        }
                        if ($quote->client_state) {
                            echo htmlsc($quote->client_state) . ' ';
                        }
                        if ($quote->client_zip) {
                            echo htmlsc($quote->client_zip) . '<br>';
                        }
                        if ($quote->client_phone) {
                            _trans('phone_abbr');
                            echo ': ' . htmlsc($quote->client_phone) . '<br>';
                        }
                        ?></p>

                        <br>

                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td><?php _trans('quote_date'); ?></td>
                                    <td style="text-align:right;"><?php echo date_from_mysql($quote->quote_date_created); ?></td>
                                </tr>
                                <tr class="<?php echo($is_expired ? 'overdue' : '') ?>">
                                    <td><?php _trans('expires'); ?></td>
                                    <td class="amount">
                                        <?php echo date_from_mysql($quote->quote_date_expires); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _trans('total'); ?></td>
                                    <td class="amount"><?php echo format_currency($quote->quote_total); ?></td>
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
                                    <th><?php _trans('item'); ?></th>
                                    <th><?php _trans('description'); ?></th>
                                    <th class="amount"><?php _trans('qty'); ?></th>
                                    <th class="amount"><?php _trans('price'); ?></th>
                                    <th class="amount"><?php _trans('discount'); ?></th>
                                    <th class="amount"><?php _trans('total'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
<?php
foreach ($items as $item) {
?>
                                <tr>
                                    <td><?php _htmlsc($item->item_name); ?></td>
                                    <td><?php echo nl2br(htmlsc($item->item_description)); ?></td>
                                    <td class="amount">
                                        <?php echo format_quantity($item->item_quantity); ?>
                                        <?php if ($item->item_product_unit) : ?>
                                            <br>
                                            <small><?php _htmlsc($item->item_product_unit); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="amount"><?php echo format_currency($item->item_price); ?></td>
                                    <td class="amount"><?php echo format_currency($item->item_discount); ?></td>
                                    <td class="amount"><?php echo format_currency($item->item_subtotal); ?></td>
                                </tr>
<?php
}
?>

<?php
if (! $legacy_calculation) {
?>
                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="amount"><?php _trans('discount'); ?></td>
                                    <td class="amount">
                                        <?php
                                        if ($quote->quote_discount_percent > 0) {
                                            echo format_amount($quote->quote_discount_percent) . '&nbsp;%';
                                        } else {
                                            echo format_currency($quote->quote_discount_amount);
                                        }
                                        ?>
                                    </td>
                                </tr>
<?php
}
?>

                                <tr>
                                    <td colspan="4"></td>
                                    <td class="amount"><?php _trans('subtotal'); ?>:</td>
                                    <td class="amount"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
                                </tr>

<?php
if ($quote->quote_item_tax_total > 0) {
?>
                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="amount"><?php _trans('item_tax'); ?></td>
                                    <td class="amount"><?php echo format_currency($quote->quote_item_tax_total); ?></td>
                                </tr>
<?php
}
?>

<?php
foreach ($quote_tax_rates as $quote_tax_rate) {
?>
                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="amount">
                                        <?php
                                            _htmlsc($quote_tax_rate->quote_tax_rate_name);
                                            echo ' ' . format_amount($quote_tax_rate->quote_tax_rate_percent);
                                        ?>&nbsp;%
                                    </td>
                                    <td class="amount"><?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?></td>
                                </tr>
<?php
}
?>

<?php
if ($legacy_calculation) {
?>
                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="amount"><?php _trans('discount'); ?></td>
                                    <td class="amount">
                                        <?php
                                        if ($quote->quote_discount_percent > 0) {
                                            echo format_amount($quote->quote_discount_percent) . '&nbsp;%';
                                        } else {
                                            echo format_currency($quote->quote_discount_amount);
                                        }
                                        ?>
                                    </td>
                                </tr>
<?php
}
?>

                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="amount"><?php _trans('total'); ?></td>
                                    <td class="amount"><?php echo format_currency($quote->quote_total) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">

<?php
if ($quote->notes) {
?>
                        <div class="col-xs-12 col-md-6">
                            <h4><?php _trans('notes'); ?></h4>
                            <p><?php echo nl2br(htmlsc($quote->notes)); ?></p>
                        </div>
<?php
}
?>

<?php
if (count($attachments) > 0) {
?>
                        <div class="col-xs-12 col-md-6">
                            <h4><?php _trans('attachments'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-condensed">
<?php
    foreach ($attachments as $attachment) {
?>
                                    <tr class="attachments">
                                        <td><?php echo $attachment['name']; ?></td>
                                        <td>
                                            <a href="<?php echo site_url('guest/get/attachment/' . $attachment['fullname']); ?>"
                                               class="btn btn-primary btn-sm">
                                                <i class="fa fa-download"></i> <?php _trans('download') ?>
                                            </a>
                                        </td>
                                    </tr>
<?php
    }
?>
                                </table>
                            </div>
                        </div>
<?php
}
?>

                    </div>

                </div><!-- .quote-items -->
            </div><!-- .quote -->
        </div><!-- #content -->
    </div>

</body>
</html>
