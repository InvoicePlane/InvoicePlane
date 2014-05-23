<div class="install-step">

	<h1>FusionInvoice</h1>

	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="form-horizontal">

		<legend><?php echo lang('setup_database_details'); ?></legend>

		<?php if (!$database['success']) { ?>
		
		<?php if ($database['message'] and $_POST) { ?>
		<p><span class="label label-important"><?php echo lang('failure'); ?></span> <?php echo $database['message']; ?></p>
		<?php } ?>

		<p><?php echo lang('setup_database_message'); ?></p>

			<div class="control-group">
				<label class="control-label"><?php echo lang('hostname'); ?>: </label>
				<div class="controls">
					<input type="text" name="db_hostname" id="db_hostname" value="<?php echo $this->input->post('db_hostname'); ?>"><br>
					<span class="help-block"><?php echo lang('setup_db_hostname_info'); ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('username'); ?>: </label>
				<div class="controls">
					<input type="text" name="db_username" id="db_username" value="<?php echo $this->input->post('db_username'); ?>"><br>
					<span class="help-block"><?php echo lang('setup_db_username_info'); ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('password'); ?>: </label>
				<div class="controls">
					<input type="password" name="db_password" id="db_password" value="<?php echo $this->input->post('db_password'); ?>"><br>
					<span class="help-block"><?php echo lang('setup_db_password_info'); ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('database'); ?>: </label>
				<div class="controls">
					<input type="text" name="db_database" id="db_database" value="<?php echo $this->input->post('db_database'); ?>"><br>
					<span class="help-block"><?php echo lang('setup_db_database_info'); ?></span>
				</div>
			</div>
		<?php } ?>

		<?php if ($errors) { ?>
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary" name="btn_try_again" value="<?php echo lang('try_again'); ?>">
			</div>
		</div>
		<?php } else { ?>
		<p><span class="label label-success"><?php echo lang('success'); ?></span> <?php echo lang('setup_database_configured_message'); ?></p>
		<input type="submit" class="btn btn-primary" name="btn_continue" value="<?php echo lang('continue'); ?>">
		<?php } ?>

	</form>

</div>