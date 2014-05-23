<div class="headerbar">
	<h1><?php echo lang('tax_rates'); ?></h1>

	<div class="pull-right">
		<a class="btn btn-primary" href="<?php echo site_url('tax_rates/form'); ?>"><i class="icon-plus icon-white"></i> <?php echo lang('new'); ?></a>
	</div>
	
	<div class="pull-right">
		<?php echo pager(site_url('tax_rates/index'), 'mdl_tax_rates'); ?>
	</div>

</div>

<div class="table-content">

	<?php echo $this->layout->load_view('layout/alerts'); ?>

	<table class="table table-striped">

		<thead>
			<tr>
				<th><?php echo lang('tax_rate_name'); ?></th>
				<th><?php echo lang('tax_rate_percent'); ?></th>
				<th><?php echo lang('options'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($tax_rates as $tax_rate) { ?>
			<tr>
				<td><?php echo $tax_rate->tax_rate_name; ?></td>
				<td><?php echo $tax_rate->tax_rate_percent; ?>%</td>
				<td>
					<div class="options btn-group">
						<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-cog"></i> <?php echo lang('options'); ?></a>
						<ul class="dropdown-menu">
							<li>
								<a href="<?php echo site_url('tax_rates/form/' . $tax_rate->tax_rate_id); ?>">
									<i class="icon-pencil"></i> <?php echo lang('edit'); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo site_url('tax_rates/delete/' . $tax_rate->tax_rate_id); ?>" onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');">
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