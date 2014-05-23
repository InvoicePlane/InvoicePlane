<div class="install-step">

	<h1>FusionInvoice</h1>

	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-horizontal">
        
        <input type="hidden" name="user_type" value="1">

		<legend><?php echo lang('setup_create_user'); ?></legend>
		
		<?php echo $this->layout->load_view('layout/alerts'); ?>

		<p><?php echo lang('setup_create_user_message'); ?></p>

		<div class="control-group">
			<label class="control-label"><?php echo lang('email_address'); ?>: </label>
			<div class="controls">
				<input type="email" name="user_email" id="user_email" value="<?php echo $this->mdl_users->form_value('user_email'); ?>"><br>
				<span class="help-block"><?php echo lang('setup_user_email_info'); ?></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('name'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_name" id="user_name" value="<?php echo $this->mdl_users->form_value('user_name'); ?>"><br>
				<span class="help-block"><?php echo lang('setup_user_name_info'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo lang('password'); ?>: </label>
			<div class="controls">
				<input type="password" name="user_password" id="user_password"><br>
				<span class="help-block"><?php echo lang('setup_user_password_info'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo lang('verify_password'); ?>: </label>
			<div class="controls">
				<input type="password" name="user_passwordv" id="user_passwordv"><br>
				<span class="help-block"><?php echo lang('setup_user_password_verify_info'); ?></span>
			</div>
		</div>
		
		<legend><?php echo lang('address'); ?></legend>
		<p><?php echo lang('setup_user_address_info'); ?></p>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('street_address'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_address_1" id="user_address_1" value="<?php echo $this->mdl_users->form_value('user_address_1'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('street_address_2'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_address_2" id="user_address_2" value="<?php echo $this->mdl_users->form_value('user_address_2'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('city'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_city" id="user_city" value="<?php echo $this->mdl_users->form_value('user_city'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('state'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_state" id="user_state" value="<?php echo $this->mdl_users->form_value('user_state'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('zip_code'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_zip" id="user_zip" value="<?php echo $this->mdl_users->form_value('user_zip'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('country'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_country" id="user_country" value="<?php echo $this->mdl_users->form_value('user_country'); ?>">
			</div>
		</div>
		
		<legend><?php echo lang('setup_other_contact'); ?></legend>
		
		<p><?php echo lang('setup_user_contact_info'); ?></p>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('phone'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_phone" id="user_phone" value="<?php echo $this->mdl_users->form_value('user_phone'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('fax'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_fax" id="user_fax" value="<?php echo $this->mdl_users->form_value('user_fax'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('mobile'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_mobile" id="user_mobile" value="<?php echo $this->mdl_users->form_value('user_mobile'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('web'); ?>: </label>
			<div class="controls">
				<input type="text" name="user_web" id="user_web" value="<?php echo $this->mdl_users->form_value('user_web'); ?>">
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary" name="btn_continue" value="<?php echo lang('continue'); ?>">
			</div>
		</div>
		
	</form>

</div>