<div class="headerbar">
	<h1><?php echo lang('payment_methods'); ?></h1>

	<div class="pull-right">
		<a class="btn btn-primary" href="<?php echo site_url('payment_methods/form'); ?>"><i class="icon-plus icon-white"></i> <?php echo lang('new'); ?></a>
	</div>
	
	<div class="pull-right">
		<?php echo pager(site_url('payment_methods/index'), 'mdl_payment_methods'); ?>
	</div>

</div>

<div class="table-content">

	<?php $this->layout->load_view('layout/alerts'); ?>

	<table class="table table-striped">

		<thead>
			<tr>
				<th><?php echo lang('payment_method'); ?></th>
				<th><?php echo lang('options'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($payment_methods as $payment_method) { ?>
			<tr>
				<td><?php echo $payment_method->payment_method_name; ?></td>
				<td>
					<div class="options btn-group">
						<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <?php echo lang('options'); ?></a>
						<ul class="dropdown-menu">
							<li>
								<a href="<?php echo site_url('payment_methods/form/' . $payment_method->payment_method_id); ?>">
									<i class="icon-pencil"></i> <?php echo lang('edit'); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo site_url('payment_methods/delete/' . $payment_method->payment_method_id); ?>" onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');">
									<i class="icon-trash"></i> <?php echo lang('delete'); ?>
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