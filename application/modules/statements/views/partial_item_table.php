<?php

    $balance = (empty($opening_balance) ? 0 : $opening_balance);

?>
<div class="table-responsive">

	<table class="table table-striped">

		<thead>
			<tr>
				<th></th>
				<th><?php _trans('invoice_date'); ?></th>
				<th><?php _trans('transaction_type'); ?></th>
				<th><?php _trans('reference'); ?></th>
				<th><?php _trans('invoice_total'); ?></th>
				<th><?php _trans('balance'); ?></th>
				<th></th>
			</tr>
		</thead>

		<tbody>

    <?php if (!empty($opening_balance)) {  ?>

      <tr>
				<td></td>
				<td>
               <?php echo date('Y-m-d',$statement_start_date);$statement_start_date; ?>
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
               <?php echo format_currency($opening_balance); ?>
          </td>
				<td></td>
			</tr>


    <?php  } ?>


    <?php
    $linecounter = 0;
    foreach ($statement_transactions as $transaction) {

        if ($transaction['transaction_type'] == Statements::TRANSACTION_TYPE_INVOICE) {
            $balance += $transaction['transaction_amount'];
        } else {
            $balance -= $transaction['transaction_amount'];
        }

        ?>

    	<tr>
				<td></td>
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
				<td></td>
			</tr>
           <?php $linecounter++; ?>
    <?php } ?>
            </tbody>

	</table>
	<hr class="style-four" />
</div>

<br>


<div class="row">

	<!--     <div class="col-xs-12 visible-xs visible-sm"><br></div> -->

	<div
		class="col-xs-12 col-md-6 col-md-offset-6 col-lg-6 col-lg-offset-6">
		<table class="table table-bordered text-right">
			<tr>
				<td style="width: 40%;"><strong><?php _trans('total_balance'); ?><strong></td>
				<td style="width: 60%;" class="amount"><strong><?php echo format_currency($client_total_balance); ?><strong></td>
			</tr>
		</table>
	</div>

</div>
