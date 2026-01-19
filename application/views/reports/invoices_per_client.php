<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <title><?php echo get_setting('custom_title', 'InvoicePlane', true); ?> - <?php _trans('invoices_per_client'); ?></title>
    <link rel="stylesheet" href="<?php _theme_asset('css/reports.css'); ?>" type="text/css">
</head>
<body>

    <h3 class="report_title"><?php _trans('invoices_per_client'); ?><br><small><?php echo $from_date . ' - ' . $to_date ?></small></h3>

    <table>
<?php
$client_id = '';
foreach ($results as $result) {
    if ($client_id != $result->client_id) {
        $client_id = $result->client_id;
?>
        <tr>
            <th><?php _htmlsc(format_client($result)); ?></th>
            <th></th>
            <th></th>
        </tr>
<?php
    }
?>
        <tr>
            <td><?php echo date_from_mysql($result->invoice_date_created, true); ?></td>
            <td><?php echo $result->invoice_number; ?></td>
            <td class="amount"><?php echo format_currency($result->invoice_total); ?></td>
        </tr>
<?php
} // End foreach
?>
    </table>

</body>
</html>
