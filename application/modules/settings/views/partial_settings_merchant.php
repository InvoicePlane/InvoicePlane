<div class="tab-info">

	<div class="control-group">
		<label class="control-label"><?php echo lang('merchant_enable'); ?>: </label>
		<div class="controls">
			<select name="settings[merchant_enabled]">
                <option value="0"><?php echo lang('no'); ?></option>
				<option value="1" <?php if ($this->mdl_settings->setting('merchant_enabled') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('merchant_driver'); ?>: </label>
		<div class="controls">
			<select name="settings[merchant_driver]">
                <option value=""></option>
                <?php foreach ($merchant_drivers as $merchant_driver) { ?>
                <option value="<?php echo $merchant_driver; ?>" <?php if ($this->mdl_settings->setting('merchant_driver') == $merchant_driver) { ?>selected="selected"<?php } ?>><?php echo ucwords(str_replace('_', ' ', $merchant_driver)); ?></option>
                <?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('merchant_test_mode'); ?>: </label>
		<div class="controls">
			<select name="settings[merchant_test_mode]">
                <option value="0"><?php echo lang('no'); ?></option>
				<option value="1" <?php if ($this->mdl_settings->setting('merchant_test_mode') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php echo lang('username'); ?>: </label>
		<div class="controls">
			<input type="text" name="settings[merchant_username]" class="input" value="<?php echo $this->mdl_settings->setting('merchant_username'); ?>">
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('password'); ?>: </label>
		<div class="controls">
			<input type="password" name="settings[merchant_password]" class="input">
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('merchant_signature'); ?>: </label>
		<div class="controls">
			<input type="text" name="settings[merchant_signature]" class="input" value="<?php echo $this->mdl_settings->setting('merchant_signature'); ?>">
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('merchant_currency_code'); ?>: </label>
		<div class="controls">
			<select name="settings[merchant_currency_code]">
                <option value=""></option>
                <?php foreach ($merchant_currency_codes as $val => $key) { ?>
                <option value="<?php echo $val; ?>" <?php if ($this->mdl_settings->setting('merchant_currency_code') == $val) { ?>selected="selected"<?php } ?>><?php echo $val; ?></option>
                <?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('online_payment_method'); ?>: </label>
		<div class="controls">
			<select name="settings[online_payment_method]">
                <option value=""></option>
                <?php foreach ($payment_methods as $payment_method) { ?>
                <option value="<?php echo $payment_method->payment_method_id; ?>" <?php if ($this->mdl_settings->setting('online_payment_method') == $payment_method->payment_method_id) { ?>selected="selected"<?php } ?>><?php echo $payment_method->payment_method_name; ?></option>
                <?php } ?>
			</select>
		</div>
	</div>

</div>