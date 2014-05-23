<form method="post" class="form-horizontal">

	<div class="headerbar">
		<h1><?php echo lang('custom_field_form'); ?></h1>
		<?php $this->layout->load_view('layout/header_buttons'); ?>
	</div>

	<div class="content">

		<?php $this->layout->load_view('layout/alerts'); ?>

			<div class="control-group">
				<label class="control-label"><?php echo lang('table'); ?>: </label>
				<div class="controls">
                    <select name="custom_field_table" id="custom_field_table">
                        <option value=""></option>
                        <?php foreach ($custom_field_tables as $table => $label) { ?>
                        <option value="<?php echo $table; ?>" <?php if ($this->mdl_custom_fields->form_value('custom_field_table') == $table) { ?>selected="selected"<?php } ?>><?php echo $label; ?></option>
                        <?php } ?>
                    </select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('label'); ?>: </label>
				<div class="controls">
					<input type="text" name="custom_field_label" id="custom_field_label" value="<?php echo $this->mdl_custom_fields->form_value('custom_field_label'); ?>">
				</div>
			</div>

	</div>

</form>