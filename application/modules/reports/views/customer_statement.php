<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('customer_statement'); ?></title>
    <link rel="stylesheet" href="<?php _theme_asset('css/templates.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php _core_asset('css/custom-pdf.css'); ?>" type="text/css">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #333;
        }
        
        /* Layout Tables */
        .layout-table { width: 100%; border-collapse: collapse; }
        .layout-table td { vertical-align: top; }
        
        /* Header Section */
        .header-section { margin-bottom: 30px; border-bottom: 1px solid #ddd; padding-bottom: 20px; }
        .company-details { text-align: right; color: #555; font-size: 9pt; line-height: 1.4; }
        .company-name { font-size: 12pt; font-weight: bold; color: #333; margin-bottom: 5px; }
        
        /* Card Styles */
        .card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .card-title {
            text-transform: uppercase;
            font-size: 8pt;
            color: #6c757d;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .statement-header {
            font-size: 16pt;
            color: #2c3e50;
            text-transform: uppercase;
            text-align: right;
            font-weight: bold;
        }
        
        /* Data Tables inside Cards */
        .summary-table { width: 100%; font-size: 10pt; }
        .summary-table td { padding: 4px 0; }
        .summary-label { color: #666; }
        .summary-value { text-align: right; font-weight: bold; }
        
        /* Transaction Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 9pt;
            color: #444;
        }
        .items-table tr:nth-child(even) { background-color: #fcfcfc; }
        
        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #27ae60; }
        .text-bold { font-weight: bold; }
        .total-row td {
            background-color: #e9ecef;
            font-weight: bold;
            color: #333;
            border-top: 2px solid #2c3e50;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0px;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<table class="layout-table header-section">
    <tr>
        <td style="width: 50%;">
            <?php echo invoice_logo_pdf(); ?>
        </td>
        <td style="width: 50%;" class="company-details">
            <?php if(isset($user) && $user): ?>
                <div class="company-name"><?php _htmlsc($user->user_name); ?></div>
                <?php if ($user->user_address_1) echo htmlsc($user->user_address_1) . '<br>'; ?>
                <?php if ($user->user_address_2) echo htmlsc($user->user_address_2) . '<br>'; ?>
                <?php if ($user->user_city) echo htmlsc($user->user_city) . ' '; ?>
                <?php if ($user->user_state) echo htmlsc($user->user_state) . ' '; ?>
                <?php if ($user->user_zip) echo htmlsc($user->user_zip) . '<br>'; ?>
                <?php if ($user->user_country) echo get_country_name(trans('cldr'), htmlsc($user->user_country)) . '<br>'; ?>
                <?php if ($user->user_vat_id) echo trans('vat_id_short') . ': ' . htmlsc($user->user_vat_id) . '<br>'; ?>
                <?php if ($user->user_email) echo htmlsc($user->user_email); ?>
            <?php endif; ?>
        </td>
    </tr>
</table>

<table class="layout-table" cellspacing="20">
    <tr>
        <td style="width: 45%;">
            <div class="card">
                <div class="card-title"><?php _trans('bill_to'); ?></div>
                <div style="font-size: 11pt; font-weight: bold; margin-bottom: 5px;">
                    <?php _htmlsc(format_client($client)); ?>
                </div>
                <div style="color: #555; font-size: 9pt; line-height: 1.4;">
                    <?php if ($client->client_address_1) echo htmlsc($client->client_address_1) . '<br>'; ?>
                    <?php if ($client->client_address_2) echo htmlsc($client->client_address_2) . '<br>'; ?>
                    <?php 
                        $cityLine = [];
                        if ($client->client_city) $cityLine[] = htmlsc($client->client_city);
                        if ($client->client_state) $cityLine[] = htmlsc($client->client_state);
                        if ($client->client_zip) $cityLine[] = htmlsc($client->client_zip);
                        if (!empty($cityLine)) echo implode(' ', $cityLine) . '<br>';
                    ?>
                    <?php if ($client->client_country) echo get_country_name(trans('cldr'), htmlsc($client->client_country)) . '<br>'; ?>
                    <?php if ($client->client_vat_id) echo trans('vat_id_short') . ': ' . htmlsc($client->client_vat_id); ?>
                </div>
            </div>
        </td>
        
        <td style="width: 45%;">
            <div class="statement-header"><?php _trans('customer_statement'); ?></div>
            <div class="card" style="margin-top: 10px;">
                <table class="summary-table">
                    <tr>
                        <td class="summary-label"><?php _trans('from_date'); ?>:</td>
                        <td class="summary-value"><?php echo $from_date; ?></td>
                    </tr>
                    <tr>
                        <td class="summary-label"><?php _trans('to_date'); ?>:</td>
                        <td class="summary-value"><?php echo $to_date; ?></td>
                    </tr>
                    <tr><td colspan="2" style="border-bottom: 1px solid #ddd; height: 5px;"></td></tr>
                    <tr>
                        <td class="summary-label" style="padding-top: 8px;"><?php _trans('total_balance'); ?>:</td>
                        <td class="summary-value" style="padding-top: 8px; font-size: 12pt; color: #2c3e50;">
                            <?php 
                            $closing_balance = $opening_balance;
                            foreach ($transactions as $t) { $closing_balance += $t['amount']; }
                            echo format_currency($closing_balance); 
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th style="width: 15%;"><?php _trans('date'); ?></th>
            <th style="width: 15%;"><?php _trans('type'); ?></th>
            <th style="width: 35%;"><?php _trans('description'); ?></th>
            <th style="width: 15%;" class="text-right"><?php _trans('amount'); ?></th>
            <th style="width: 20%;" class="text-right"><?php _trans('balance'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr style="background-color: #f2f2f2;">
            <td><?php echo $from_date; ?></td>
            <td><strong><?php _trans('opening_balance'); ?></strong></td>
            <td style="font-style: italic; color: #777;"><?php _trans('balance_brought_forward'); ?></td>
            <td class="text-right">-</td>
            <td class="text-right text-bold"><?php echo format_currency($opening_balance); ?></td>
        </tr>

        <?php
        $running_balance = $opening_balance;
        foreach ($transactions as $transaction) {
            $running_balance += $transaction['amount'];
            
            $type_label = $transaction['type'] == 'invoice' ? trans('invoice') : trans('payment');
            $badge_style = $transaction['type'] == 'invoice' 
                ? 'background:#e3f2fd; color:#0d47a1; padding:2px 6px; border-radius:3px; font-size:8pt;' 
                : 'background:#e8f5e9; color:#1b5e20; padding:2px 6px; border-radius:3px; font-size:8pt;';
            
            $amount_class = $transaction['amount'] < 0 ? 'text-success' : '';
        ?>
            <tr>
                <td><?php echo date_from_mysql($transaction['date']); ?></td>
                <td><span style="<?php echo $badge_style; ?>"><?php echo $type_label; ?></span></td>
                <td><?php echo $transaction['description']; ?></td>
                <td class="text-right <?php echo $amount_class; ?>"><?php echo format_currency($transaction['amount']); ?></td>
                <td class="text-right text-bold"><?php echo format_currency($running_balance); ?></td>
            </tr>
        <?php } ?>
        
        <?php if (empty($transactions)): ?>
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px; color: #777;">
                    <?php _trans('no_transactions_found'); ?>
                </td>
            </tr>
        <?php endif; ?>
        
        <tr class="total-row">
            <td colspan="4" class="text-right"><?php _trans('total_balance'); ?></td>
            <td class="text-right"><?php echo format_currency($running_balance); ?></td>
        </tr>
    </tbody>
</table>

<htmlpagefooter name="statement_footer">
    <div class="footer">
        <?php echo get_setting('custom_title', 'InvoicePlane', true); ?> - <?php _trans('page'); ?> {PAGENO} / {nbpg}
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="statement_footer" value="on" />

</body>
</html>