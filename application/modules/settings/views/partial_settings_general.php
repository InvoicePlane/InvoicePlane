<script type="text/javascript">
    $(function()
    {
        $('#btn_generate_cron_key').click(function()
        {
            $.post("<?php echo site_url('settings/ajax/get_cron_key'); ?>", function(data) {
                $('#cron_key').val(data);
            });
        });
    });
</script>

<div class="tab-info">

	<div class="control-group">
		<label class="control-label"><?php echo lang('current_version'); ?>: </label>
		<div class="controls" style="text: bottom;">
			<input type="text" class="input-small" value="<?php echo $current_version; ?>" readonly="readonly">
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php echo lang('language'); ?>: </label>
		<div class="controls">
			<select name="settings[default_language]">
				<?php foreach ($languages as $language) { ?>
				<option value="<?php echo $language; ?>" <?php if ($this->mdl_settings->setting('default_language') == $language) { ?>selected="selected"<?php } ?>><?php echo ucfirst($language); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('date_format'); ?>: </label>
		<div class="controls">
			<select name="settings[date_format]">
				<?php foreach ($date_formats as $date_format) { ?>
				<option value="<?php echo $date_format['setting']; ?>" <?php if ($this->mdl_settings->setting('date_format') == $date_format['setting']) { ?>selected="selected"<?php } ?>><?php echo $current_date->format($date_format['setting']); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php echo lang('currency_symbol'); ?>: </label>
		<div class="controls" style="text: bottom;">
			<input type="text" name="settings[currency_symbol]" class="input-small" value="<?php echo $this->mdl_settings->setting('currency_symbol'); ?>">
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo lang('currency_symbol_placement'); ?>: </label>
		<div class="controls">
			<select name="settings[currency_symbol_placement]">
				<option value="before" <?php if ($this->mdl_settings->setting('currency_symbol_placement') == 'before') { ?>selected="selected"<?php } ?>><?php echo lang('before_amount'); ?></option>
				<option value="after" <?php if ($this->mdl_settings->setting('currency_symbol_placement') == 'after') { ?>selected="selected"<?php } ?>><?php echo lang('after_amount'); ?></option>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('thousands_separator'); ?>: </label>
		<div class="controls" style="text: bottom;">
			<input type="text" name="settings[thousands_separator]" class="input-small" value="<?php echo $this->mdl_settings->setting('thousands_separator'); ?>">
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('decimal_point'); ?>: </label>
		<div class="controls" style="text: bottom;">
			<input type="text" name="settings[decimal_point]" class="input-small" value="<?php echo $this->mdl_settings->setting('decimal_point'); ?>">
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('tax_rate_decimal_places'); ?>: </label>
		<div class="controls">
			<select class="input-small" name="settings[tax_rate_decimal_places]" id="tax_rate_decimal_places">
				<option value="2" <?php if ($this->mdl_settings->setting('tax_rate_decimal_places') == '2') { ?>selected="selected"<?php } ?>>2</option>
				<option value="3" <?php if ($this->mdl_settings->setting('tax_rate_decimal_places') == '3') { ?>selected="selected"<?php } ?>>3</option>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('cron_key'); ?>: </label>
		<div class="controls" style="text: bottom;">
			<input type="text" name="settings[cron_key]" id="cron_key" class="input" value="<?php echo $this->mdl_settings->setting('cron_key'); ?>">
            <input type="button" id="btn_generate_cron_key" value="<?php echo lang('generate'); ?>">
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('login_logo'); ?>: </label>
		<div class="controls">
            <?php if ($this->mdl_settings->setting('login_logo')) { ?>
            <img src="<?php echo base_url(); ?>uploads/<?php echo $this->mdl_settings->setting('login_logo'); ?>"><br>
            <?php echo anchor('settings/remove_logo/login', 'Remove Logo'); ?><br>
            <?php } ?>
			<input type="file" name="login_logo" size="40" />
		</div>
	</div>

</div>