<div class="table-responsive">
    <table class="table table-striped">

        <thead>
        <tr>
        	<th><?php echo lang('expense_id'); ?></th>
            <th><?php echo lang('expense_date'); ?></th>
            <th><?php echo lang('client'); ?></th>
            <th><?php echo lang('amount'); ?></th>
            <th><?php echo lang('tax_paid'); ?></th>
            <th><?php echo lang('tax_rate'); ?></th>
            <th><?php echo lang('payment_method'); ?></th>
            <th><?php echo lang('expense_file_download'); ?></th>
            <th><?php echo lang('note'); ?></th>
            <th><?php echo lang('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($expenses as $expense) { ?>
            <tr>
            	<td><?php echo $expense->expense_id; ?>
                <td><?php echo date_from_mysql($expense->expense_date); ?></td>
                <td>
                    <a href="<?php echo site_url('clients/view/' . $expense->client_id); ?>"
                       title="<?php echo lang('view_client'); ?>">
                        <?php echo $expense->client_name; ?>
                    </a>
                </td>
                <td><?php echo format_currency($expense->expense_amount); ?></td>
                <td><?php echo format_currency($expense->expense_amount * ($expense->tax_rate_percent / 100)); ?></td>
                <td><?php echo $expense->tax_rate_name; ?></td>
                <td><?php echo $expense->payment_method_name; ?></td>
                <td>
					<?php if($expense->expense_file_name)
						  { 
						  	echo "<a href='".site_url('expenses/getfile/' . $expense->expense_id)."'>Download</a>"; 
						  }
						  else
						  {
							  echo "";
						  }
					?>
                </td>
                <td><?php echo $expense->expense_note; ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php echo lang('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('expenses/form/' . $expense->expense_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i>
                                    <?php echo lang('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('expenses/remove/' . $expense->expense_id); ?>"
                                   onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i>
                                    <?php echo lang('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>

    </table>
</div>