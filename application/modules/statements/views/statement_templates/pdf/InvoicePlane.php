<?php

    $balance = (empty($statement_balance) ? 0 : $statement_balance)

?>
<!DOCTYPE html>
<html lang="<?php _trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php _trans('statement') . $statement->getStatement_number(); ?></title>
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/core/css/custom-pdf.css">
</head>
<body>
<header class="clearfix">

    <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>

    <div id="client">
        <div>
            <b><?php _htmlsc($client->client_name); ?></b>
        </div>
        <?php if ($client->client_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . $client->client_vat_id . '</div>';
        }
        if ($client->client_tax_code) {
            echo '<div>' . trans('tax_code_short') . ': ' . $client->client_tax_code . '</div>';
        }
        if ($client->client_address_1) {
            echo '<div>' . htmlsc($client->client_address_1) . '</div>';
        }
        if ($client->client_address_2) {
            echo '<div>' . htmlsc($client->client_address_2) . '</div>';
        }
        if ($client->client_city || $client->client_state || $client->client_zip) {
            echo '<div>';
            if ($client->client_city) {
                echo htmlsc($client->client_city) . ' ';
            }
            if ($client->client_state) {
                echo htmlsc($client->client_state) . ' ';
            }
            if ($client->client_zip) {
                echo htmlsc($client->client_zip);
            }
            echo '</div>';
        }
        if ($client->client_state) {
            echo '<div>' . htmlsc($client->client_state) . '</div>';
        }
        if ($client->client_country) {
            echo '<div>' . get_country_name(trans('cldr'), $client->client_country) . '</div>';
        }

        echo '<br/>';

        if ($client->client_phone) {
            echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($client->client_phone) . '</div>';
        } ?>

    </div>

</header>

<main>

    <div class="invoice-details clearfix">
        <table>
            <tr>
                <td><?php echo trans('statement_date') . ':'; ?></td>
                <td><?php echo date(date_format_setting(),$statement->getStatement_date()); ?></td>
            </tr>
            <tr>
                <td><?php echo trans('statement_start_date') . ':'; ?></td>
                <td><?php echo date(date_format_setting(),$statement->getStatement_start_date()); ?></td>
            </tr>
            <tr>
                <td><?php echo trans('statement_end_date') . ':'; ?></td>
                <td><?php echo date(date_format_setting(),$statement->getStatement_end_date()); ?></td>
            </tr>

        </table>
    </div>

    <h1 class="invoice-title"><?php echo trans('statement') . ' ' . $statement->getStatement_number(); ?></h1>

    <table class="item-table">
        <thead>
        <tr>

                <th class="item-name"><?php _trans('invoice_date'); ?></th>
                <th class="item-name"><?php _trans('transaction_type'); ?></th>
                <th class="item-name"><?php _trans('reference'); ?></th>
                <th class="item-name"><?php _trans('invoice_total'); ?></th>
                <th class="item-total text-right"><?php _trans('balance'); ?></th>

        </tr>
        </thead>
        <tbody>

    <?php
    if (!empty($statement->getOpening_balance())) {
    ?>

      <tr>
         <td>
               <?php //echo $statement->GetStatement_start_date(); ?>
          </td>
         <td>
               <?php echo _trans('opening_balance'); ?>
          </td>
         <td>
               <?php echo ' - '; ?>
          </td>
         <td>
               <?php echo ' - '; ?>
          </td>
         <td>
               <?php echo format_currency($statement->getOpening_balance()); ?>
          </td>
      </tr>


    <?php  } ?>

    <?php
    $linecounter = 0;
    foreach ($statement->GetStatement_transactions() as $transaction) {


        if ($transaction['transaction_type'] == Statements::TRANSACTION_TYPE_INVOICE) {
            $balance += $transaction['transaction_amount'];
        } else {
            $balance -= $transaction['transaction_amount'];
        }
    ?>

    	<tr>
         <td>
               <?php echo $transaction['transaction_date']; ?>
         </td>
         <td>
               <?php echo ($transaction['transaction_type'] == Statements::TRANSACTION_TYPE_INVOICE)? "Invoice" : "Payment"; ?>
         </td>
         <td>
               <?php echo $transaction['invoice_number']; ?>
         </td>
         <td>
               <?php echo format_currency($transaction['transaction_amount']); ?>
          </td>
         <td>
               <?php echo format_currency($balance); ?>
          </td>
      </tr>
           <?php $linecounter++; ?>
    <?php } ?>

        </tbody>
<!--     </table> -->
<!--     <table> -->
        <tbody class="invoice-sums">

        <tr>
        	<td colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-right" colspan="4"><?php _trans('total_balance'); ?></td>
            <td class="text-right"><?php echo format_currency($statement->getStatement_balance()); ?></td>
        </tr>
        <tr>
        	<td colspan="5">&nbsp;</td>
        </tr>
        </tbody>
    </table>

</main>

<footer>
    <?php if (!empty($notes)) : ?>
        <div class="notes">
            <b><?php _trans('notes'); ?></b><br/>
            <?php  echo nl2br(htmlsc($notes)); ?>
        </div>
    <?php endif; ?>
</footer>

</body>
</html>
