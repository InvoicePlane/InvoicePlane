<!DOCTYPE html>
<html lang="<?php echo trans('cldr'); ?>">
<head>
    <title><?php echo trans('invoices_per_client'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/reports.css" type="text/css">
</head>
<body>

<h3 class="report_title">
    <?php echo trans('invoices_per_client'); ?><br/>
    <small><?php echo $from_date . ' - ' . $to_date ?></small>
</h3>

<pre>
<?php
    // var_dump($results);
    // die();
    $clientname = '';
?>
</pre>

<table>
    <?php foreach ($results as $result) {  ?>
            <?php if ($clientname != $result->client_name) {
                $clientname = $result->client_name; ?>
                <tr>
                    <th><?php _htmlsc($result->client_name); ?></th>
                    <th></th>
                    <th></th>
                </tr>
            <?php } ?>
            <tr>
                <td><?php echo date_from_mysql($result->invoice_date_created, true); ?></td>
                <td><?php echo $result->invoice_number; ?></td>
                <td class="amount"><?php echo format_currency($result->invoice_total); ?></td>
            </tr>
    <?php } ?>
</table>

</body>
</html>
