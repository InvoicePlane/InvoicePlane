<form method="post" class="form-horizontal">

	<div class="headerbar">
		<h1><?php echo lang('email_template_form'); ?></h1>
		<?php $this->layout->load_view('layout/header_buttons'); ?>
	</div>

	<div class="content">

		<?php $this->layout->load_view('layout/alerts'); ?>

			<div class="control-group">
				<label class="control-label"><?php echo lang('title'); ?>: </label>
				<div class="controls">
					<input type="text" name="email_template_title" id="email_template_title" value="<?php echo $this->mdl_email_templates->form_value('email_template_title'); ?>" class="span8">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo lang('body'); ?>: </label>
				<div class="controls">
					<textarea name="email_template_body" id="email_template_body" style="height: 200px;" class="span8"><?php echo $this->mdl_email_templates->form_value('email_template_body'); ?></textarea>
				</div>
			</div>

			<div class="row show-grid">
			    <h4 class="span8 offset2"><?php echo lang('email_template_tags'); ?></h4><br><br>
			</div>
			<div class="row show-grid">

			    <div class="span2 offset2">
			        <strong><?php echo lang('client'); ?></strong><br>
			        <a href="#" class="text-tag" data-tag="{{{client_name}}}"><?php echo lang('client_name'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{client_address_1}}}"><?php echo lang('client'); ?> <?php echo lang('address'); ?> 1</a><br>
			        <a href="#" class="text-tag" data-tag="{{{client_address_2}}}"><?php echo lang('client'); ?> <?php echo lang('address'); ?> 2</a><br>
			        <a href="#" class="text-tag" data-tag="{{{client_city}}}"><?php echo lang('client'); ?> <?php echo lang('city'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{client_state}}}"><?php echo lang('client'); ?> <?php echo lang('state'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{client_zip}}}"><?php echo lang('client'); ?> <?php echo lang('zip_code'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{client_country}}}"><?php echo lang('client'); ?> <?php echo lang('country'); ?></a><br>
                    <?php foreach ($custom_fields['fi_client_custom'] as $custom) { ?>
                    <a href="#" class="text-tag" data-tag="{{{<?php echo $custom->custom_field_column; ?>}}}"><?php echo $custom->custom_field_label; ?></a><br>
                    <?php } ?>
			    </div>

			    <div class="span2">
			        <strong><?php echo lang('user'); ?></strong><br>
			        <a href="#" class="text-tag" data-tag="{{{user_name}}}"><?php echo lang('user'); ?> <?php echo lang('name'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_company}}}"><?php echo lang('user'); ?> <?php echo lang('company'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_address_1}}}"><?php echo lang('user'); ?> <?php echo lang('address'); ?> 1</a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_address_2}}}"><?php echo lang('user'); ?> <?php echo lang('address'); ?> 2</a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_city}}}"><?php echo lang('user'); ?> <?php echo lang('city'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_state}}}"><?php echo lang('user'); ?> <?php echo lang('state'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_zip}}}"><?php echo lang('user'); ?> <?php echo lang('zip_code'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_country}}}"><?php echo lang('user'); ?> <?php echo lang('country'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_phone}}}"><?php echo lang('user'); ?> <?php echo lang('phone'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_fax}}}"><?php echo lang('user'); ?> <?php echo lang('fax'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_mobile}}}"><?php echo lang('user'); ?> <?php echo lang('mobile'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_email}}}"><?php echo lang('user'); ?> <?php echo lang('email'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{user_web}}}"><?php echo lang('user'); ?> <?php echo lang('web_address'); ?></a><br>
                    <?php foreach ($custom_fields['fi_user_custom'] as $custom) { ?>
                    <a href="#" class="text-tag" data-tag="{{{<?php echo $custom->custom_field_column; ?>}}}"><?php echo $custom->custom_field_label; ?></a><br>
                    <?php } ?>
			    </div>

			    <div class="span2">
			        <strong><?php echo lang('invoices'); ?></strong><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_guest_url}}}"><?php echo lang('invoice'); ?> <?php echo lang('guest_url'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_number}}}"><?php echo lang('invoice'); ?> <?php echo lang('id'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_date_due}}}"><?php echo lang('invoice'); ?> <?php echo lang('due_date'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_date_created}}}"><?php echo lang('invoice'); ?> <?php echo lang('created'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_terms}}}"><?php echo lang('invoice_terms'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_total}}}"><?php echo lang('invoice'); ?> <?php echo lang('total'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_paid}}}"><?php echo lang('invoice'); ?> <?php echo lang('total_paid'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_balance}}}"><?php echo lang('invoice'); ?> <?php echo lang('balance'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{invoice_status}}}"><?php echo lang('invoice'); ?> <?php echo lang('status'); ?></a><br>
                    <?php foreach ($custom_fields['fi_invoice_custom'] as $custom) { ?>
                    <a href="#" class="text-tag" data-tag="{{{<?php echo $custom->custom_field_column; ?>}}}"><?php echo $custom->custom_field_label; ?></a><br>
                    <?php } ?>
			    </div>

			    <div class="span2">
			        <strong><?php echo lang('quotes'); ?></strong><br>
			        <a href="#" class="text-tag" data-tag="{{{quote_total}}}"><?php echo lang('quote'); ?> <?php echo lang('total'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{quote_date_created}}}"><?php echo lang('quote_date'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{quote_date_expires}}}"><?php echo lang('quote'); ?> <?php echo lang('expires'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{quote_number}}}"><?php echo lang('quote'); ?> <?php echo lang('id'); ?></a><br>
			        <a href="#" class="text-tag" data-tag="{{{quote_guest_url}}}"><?php echo lang('quote'); ?> <?php echo lang('guest_url'); ?></a><br>
                    <?php foreach ($custom_fields['fi_quote_custom'] as $custom) { ?>
                    <a href="#" class="text-tag" data-tag="{{{<?php echo $custom->custom_field_column; ?>}}}"><?php echo $custom->custom_field_label; ?></a><br>
                    <?php } ?>
			    </div>
			</div>

	</div>

</form>