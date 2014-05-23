<div class="headerbar">
	<h1><?php echo lang('invoice_aging'); ?></h1>
</div>

<div class="content">

	<?php $this->layout->load_view('layout/alerts'); ?>

	<div id="report_options" class="widget">

		<div class="widget-title">
			<h5><i class="icon-print"></i> <?php echo lang('report_options'); ?></h5>
		</div>

		<div class="padded">
			
			<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-horizontal">

				<div class="control-group">
					<div class="controls">
						<input type="submit" class="btn" name="btn_submit" value="<?php echo lang('run_report'); ?>">	
					</div>
				</div>

			</form>

		</div>

	</div>

</div>

</form>