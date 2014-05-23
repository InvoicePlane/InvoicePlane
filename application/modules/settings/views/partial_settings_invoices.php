<div class="tab-info">

	<div class="control-group">
		<label class="control-label"><?php echo lang('default_pdf_template'); ?>: </label>
		<div class="controls">
			<select name="settings[pdf_invoice_template]">
				<option value=""></option>
				<?php foreach ($pdf_invoice_templates as $invoice_template) { ?>
				<option value="<?php echo $invoice_template; ?>" <?php if ($this->mdl_settings->setting('pdf_invoice_template') == $invoice_template) { ?>selected="selected"<?php } ?>><?php echo $invoice_template; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
   
	<div class="control-group">
		<label class="control-label"><?php echo lang('pdf_template_paid'); ?>: </label>
		<div class="controls">
			<select name="settings[pdf_invoice_template_paid]">
				<option value=""></option>
				<?php foreach ($pdf_invoice_templates as $invoice_template) { ?>
				<option value="<?php echo $invoice_template; ?>" <?php if ($this->mdl_settings->setting('pdf_invoice_template_paid') == $invoice_template) { ?>selected="selected"<?php } ?>><?php echo $invoice_template; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('pdf_template_overdue'); ?>: </label>
		<div class="controls">
			<select name="settings[pdf_invoice_template_overdue]">
				<option value=""></option>
				<?php foreach ($pdf_invoice_templates as $invoice_template) { ?>
				<option value="<?php echo $invoice_template; ?>" <?php if ($this->mdl_settings->setting('pdf_invoice_template_overdue') == $invoice_template) { ?>selected="selected"<?php } ?>><?php echo $invoice_template; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('default_public_template'); ?>: </label>
		<div class="controls">
			<select name="settings[public_invoice_template]">
				<option value=""></option>
				<?php foreach ($public_invoice_templates as $invoice_template) { ?>
				<option value="<?php echo $invoice_template; ?>" <?php if ($this->mdl_settings->setting('public_invoice_template') == $invoice_template) { ?>selected="selected"<?php } ?>><?php echo $invoice_template; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('default_email_template'); ?>: </label>
		<div class="controls">
			<select name="settings[email_invoice_template]">
                <option value=""></option>
                <?php foreach ($email_templates as $email_template) { ?>
                <option value="<?php echo $email_template->email_template_id; ?>" <?php if ($this->mdl_settings->setting('email_invoice_template') == $email_template->email_template_id) { ?>selected="selected"<?php } ?>><?php echo $email_template->email_template_title; ?></option>
                <?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('email_template_paid'); ?>: </label>
		<div class="controls">
			<select name="settings[email_invoice_template_paid]">
                <option value=""></option>
                <?php foreach ($email_templates as $email_template) { ?>
                <option value="<?php echo $email_template->email_template_id; ?>" <?php if ($this->mdl_settings->setting('email_invoice_template_paid') == $email_template->email_template_id) { ?>selected="selected"<?php } ?>><?php echo $email_template->email_template_title; ?></option>
                <?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('email_template_overdue'); ?>: </label>
		<div class="controls">
			<select name="settings[email_invoice_template_overdue]">
                <option value=""></option>
                <?php foreach ($email_templates as $email_template) { ?>
                <option value="<?php echo $email_template->email_template_id; ?>" <?php if ($this->mdl_settings->setting('email_invoice_template_overdue') == $email_template->email_template_id) { ?>selected="selected"<?php } ?>><?php echo $email_template->email_template_title; ?></option>
                <?php } ?>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('invoices_due_after'); ?>: </label>
		<div class="controls">
			<input type="text" name="settings[invoices_due_after]" class="input-small" value="<?php echo $this->mdl_settings->setting('invoices_due_after'); ?>">
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo lang('default_invoice_group'); ?>: </label>
		<div class="controls">
			<select name="settings[default_invoice_group]">
				<option value=""></option>
				<?php foreach ($invoice_groups as $invoice_group) { ?>
				<option value="<?php echo $invoice_group->invoice_group_id; ?>" <?php if ($this->mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>><?php echo $invoice_group->invoice_group_name; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo lang('default_terms'); ?>: </label>
		<div class="controls">
			<textarea name="settings[default_invoice_terms]" style="width: 400px; height: 150px;"><?php echo $this->mdl_settings->setting('default_invoice_terms'); ?></textarea>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('invoice_logo'); ?>: </label>
		<div class="controls">
            <?php if ($this->mdl_settings->setting('invoice_logo')) { ?>
            <img src="<?php echo base_url(); ?>uploads/<?php echo $this->mdl_settings->setting('invoice_logo'); ?>"><br>
            <?php echo anchor('settings/remove_logo/invoice', 'Remove Logo'); ?><br>
            <?php } ?>
			<input type="file" name="invoice_logo" size="40" />
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('automatic_email_on_recur'); ?>: </label>
		<div class="controls">
			<select name="settings[automatic_email_on_recur]">
                <option value="0" <?php if (!$this->mdl_settings->setting('automatic_email_on_recur')) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
                <option value="1" <?php if ($this->mdl_settings->setting('automatic_email_on_recur')) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
			</select>
		</div>
	</div>
    
	<div class="control-group">
		<label class="control-label"><?php echo lang('mark_invoices_sent_pdf'); ?>: </label>
		<div class="controls">
			<select name="settings[mark_invoices_sent_pdf]">
                <option value="0" <?php if (!$this->mdl_settings->setting('mark_invoices_sent_pdf')) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
                <option value="1" <?php if ($this->mdl_settings->setting('mark_invoices_sent_pdf')) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
			</select>
		</div>
	</div>

</div>