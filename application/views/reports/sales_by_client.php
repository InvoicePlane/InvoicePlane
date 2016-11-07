<html>
<head>
    <title><?php echo trans('sales_by_client'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/reports.css" type="text/css">
</head>
<body>

<h3 class="report_title"><?php echo trans('sales_by_client'); ?></h3>

<table>
    <tr>
        <th><?php echo trans('client'); ?></th>
        <th class="amount"><?php echo trans('invoice_count'); ?></th>
        <th class="amount"><?php echo trans('sales'); ?></th>
        <th class="amount"><?php echo trans('sales_with_tax'); ?></th>
    </tr>
    <?php foreach ($results as $result) { ?>
        <tr>
            <td><?php echo $result->client_name; ?></td>
            <td class="amount"><?php echo $result->invoice_count; ?></td>
            <td class="amount"><?php echo format_currency($result->sales); ?></td>
            <td class="amount"><?php echo format_currency($result->sales_with_tax); ?></td>
        </tr>
    <?php } ?>
</table>
</body>
</html>