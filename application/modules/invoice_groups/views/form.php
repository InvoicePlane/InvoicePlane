<form method="post" class="form-horizontal">

	<div class="headerbar">
		<h1><?php echo lang('invoice_group_form'); ?></h1>
		<?php $this->layout->load_view('layout/header_buttons'); ?>
	</div>

	<div class="content">

		<?php $this->layout->load_view('layout/alerts'); ?>

			<div class="control-group">
				<label class="control-label"><?php echo lang('name'); ?>: </label>
				<div class="controls">
					<input type="text" name="invoice_group_name" id="invoice_group_name" value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_name'); ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('prefix'); ?>: </label>
				<div class="controls">
					<input type="text" name="invoice_group_prefix" id="invoice_group_prefix" value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_prefix'); ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('next_id'); ?>: </label>
				<div class="controls">
					<input type="text" name="invoice_group_next_id" id="invoice_group_next_id" value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_next_id'); ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('left_pad'); ?>: </label>
				<div class="controls">
					<input type="text" name="invoice_group_left_pad" id="invoice_group_left_pad" value="<?php echo $this->mdl_invoice_groups->form_value('invoice_group_left_pad'); ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('year_prefix'); ?>: </label>
				<div class="controls">
					<select name="invoice_group_prefix_year" id="invoice_group_prefix_year">
						<option value="0" <?php if ($this->mdl_invoice_groups->form_value('invoice_group_prefix_year') == 0) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
						<option value="1" <?php if ($this->mdl_invoice_groups->form_value('invoice_group_prefix_year') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('month_prefix'); ?>: </label>
				<div class="controls">
					<select name="invoice_group_prefix_month" id="invoice_group_prefix_month">
						<option value="0" <?php if ($this->mdl_invoice_groups->form_value('invoice_group_prefix_month') == 0) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
						<option value="1" <?php if ($this->mdl_invoice_groups->form_value('invoice_group_prefix_month') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
					</select>
				</div>
			</div>

	</div>
	
</form>