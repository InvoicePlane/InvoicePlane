<form method="post" class="form-horizontal">

	<div class="headerbar">
		<h1><?php echo lang('change_password'); ?></h1>
		<?php echo $this->layout->load_view('layout/header_buttons'); ?>
	</div>

	<div class="content">

		<?php $this->layout->load_view('layout/alerts'); ?>

			<fieldset>
				<legend><?php echo lang('change_password'); ?></legend>

				<div class="control-group">
					<label class="control-label"><?php echo lang('password'); ?>: </label>
					<div class="controls">
						<input type="password" name="user_password" id="user_password">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?php echo lang('verify_password'); ?>: </label>
					<div class="controls">
						<input type="password" name="user_passwordv" id="user_passwordv">
					</div>
				</div>

			</fieldset>

	</div>
	
</form>