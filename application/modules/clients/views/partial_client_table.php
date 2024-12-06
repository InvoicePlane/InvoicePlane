<?php function do_client_caret($cond, $order) {
  if(!$cond) return;
  if ($order == 'desc') echo ' <i class="fa fa-caret-down"></i> ';
  if ($order == 'asc') echo ' <i class="fa fa-caret-up"></i>';
}

// because of search box
if (!isset($sort)) $sort=''; if(!isset($order)) $order='';
?>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th><?php _trans('active'); ?></th>
            <th> <a href="?sort=name&order=<?= ($sort === 'name' && $order === 'asc') ? 'desc' : 'asc' ?>">
		<?php _trans('client_name'); ?><?= do_client_caret($sort === 'name', $order) ?></a>
	   </th>

<?php if (ip_xtra()): ?>
<th> <a href="?sort=id&order=<?= ($sort === 'id' && $order === 'asc') ? 'desc' : 'asc' ?>">
  Kd-Nr.<?= do_client_caret($sort === 'id', $order) ?></a></th>
<?php endif; ?>

<?php if (ip_atac()): ?>
<th> <a href="?sort=id&order=<?= ($sort === 'id' && $order === 'asc') ? 'desc' : 'asc' ?>">
  Kd-Id.<?= do_client_caret($sort === 'id', $order) ?></a></th>
<th>Hosting</th>
<th>LS-Mandat</th>
<th>AV</th>
<?php endif; ?>

            <th><?php _trans('email_address'); ?></th>
            <th><?php _trans('phone_number'); ?></th>

            <th class="amount">
		<a href="?sort=amount&order=<?= ($sort === 'amount' && $order === 'asc') ? 'desc' : 'asc' ?>">
		<?php _trans('balance'); ?><?= do_client_caret($sort === 'amount', $order) ?></a> 
	</th>

            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $client) : ?>
            <tr>
		<td>
			<?php echo ($client->client_active) ? '<span class="label active">' . trans('yes') . '</span>' : '<span class="label inactive">' . trans('no') . '</span>'; ?>
		</td>
                <td>
			<?php echo anchor('clients/view/' . $client->client_id, htmlsc(format_client($client))); ?>
		</td>
<?php if (ip_atac() || ip_xtra()): ?>
	<td><?php if (isset($client->client_extended_customer_no)) echo $client->client_extended_customer_no; ?></td>
<?php endif; ?>

<?php if (ip_atac()): ?>
	<td><?php if (isset($client->client_extended_contract)) echo $client->client_extended_contract; ?></td>
	<td><?php if (isset($client->client_extended_direct_debit)) echo $client->client_extended_direct_debit; ?></td>
	<td><?php if (isset($client->client_extended_flags)) echo $client->client_extended_flags; ?></td>
<?php endif; ?>

                <td><?php _htmlsc($client->client_email); ?></td>
                <td><?php _htmlsc($client->client_phone ? $client->client_phone : ($client->client_mobile ? $client->client_mobile : '')); ?></td>
                <td class="amount"><?php echo format_currency($client->client_invoice_balance); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('clients/view/' . $client->client_id); ?>">
                                    <i class="fa fa-eye fa-margin"></i> <?php _trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/form/' . $client->client_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-quote"
                                   data-client-id="<?php echo $client->client_id; ?>">
                                    <i class="fa fa-file fa-margin"></i> <?php _trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-invoice"
                                   data-client-id="<?php echo $client->client_id; ?>">
                                    <i class="fa fa-file-text fa-margin"></i> <?php _trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <form action="<?php echo site_url('clients/delete/' . $client->client_id); ?>"
                                      method="POST">
                                    <?php _csrf_field(); ?>
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('<?php _trans('delete_client_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
