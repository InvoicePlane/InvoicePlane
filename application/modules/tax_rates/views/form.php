<form method="post" class="form-horizontal">

	<div class="headerbar">
		<h1><?php echo lang('tax_rate_form'); ?></h1>
		<?php $this->layout->load_view('layout/header_buttons'); ?>
	</div>

	<div class="content">

		<?php $this->layout->load_view('layout/alerts'); ?>

			<div class="control-group">
				<label class="control-label"><?php echo lang('tax_rate_name'); ?>: </label>
				<div class="controls">
					<input type="text" name="tax_rate_name" id="tax_rate_name" value="<?php echo $this->mdl_tax_rates->form_value('tax_rate_name'); ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('tax_rate_percent'); ?>: </label>
				<div class="controls">
					<input type="text" name="tax_rate_percent" id="tax_rate_percent" value="<?php echo $this->mdl_tax_rates->form_value('tax_rate_percent'); ?>">
				</div>
			</div>

	</div>

</form>