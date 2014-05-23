<script type="text/javascript">
$(function() {
   $('#client_name').focus(); 
});
</script>

<form method="post" class="form-horizontal">

	<div class="headerbar">
		<h1><?php echo lang('client_form'); ?></h1>
		<?php $this->layout->load_view('layout/header_buttons'); ?>
	</div>

	<div class="content">

		<?php $this->layout->load_view('layout/alerts'); ?>

        <fieldset>
            <legend><?php echo lang('personal_information'); ?></legend>

            <div class="control-group">
                <label class="control-label"><?php echo lang('active_client'); ?>: </label>
                <div class="controls">
                    <input type="checkbox" name="client_active" id="client_active" value="1" <?php if ($this->mdl_clients->form_value('client_active') == 1 or !is_numeric($this->mdl_clients->form_value('client_active'))) { ?>checked="checked"<?php } ?>>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">* <?php echo lang('client_name'); ?>: </label>
                <div class="controls">
                    <input type="text" name="client_name" id="client_name" value="<?php echo $this->mdl_clients->form_value('client_name'); ?>">
                </div>
            </div>

        </fieldset>
        
        <div class="row-fluid">
            
            <div class="span6">
                <fieldset>
                    <legend><?php echo lang('address'); ?></legend>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('street_address'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_address_1" id="client_address_1" value="<?php echo $this->mdl_clients->form_value('client_address_1'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('street_address_2'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_address_2" id="client_address_2" value="<?php echo $this->mdl_clients->form_value('client_address_2'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('city'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_city" id="client_city" value="<?php echo $this->mdl_clients->form_value('client_city'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('state'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_state" id="client_state" value="<?php echo $this->mdl_clients->form_value('client_state'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('zip_code'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_zip" id="client_zip" value="<?php echo $this->mdl_clients->form_value('client_zip'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('country'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_country" id="client_country" value="<?php echo $this->mdl_clients->form_value('client_country'); ?>">
                        </div>
                    </div>
                </fieldset>
            </div>
            
            <div class="span6">
                <fieldset>

                    <legend><?php echo lang('contact_information'); ?></legend>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('phone_number'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_phone" id="client_phone" value="<?php echo $this->mdl_clients->form_value('client_phone'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('fax_number'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_fax" id="client_fax" value="<?php echo $this->mdl_clients->form_value('client_fax'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('mobile_number'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_mobile" id="client_mobile" value="<?php echo $this->mdl_clients->form_value('client_mobile'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('email_address'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_email" id="client_email" value="<?php echo $this->mdl_clients->form_value('client_email'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('web_address'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="client_web" id="client_web" value="<?php echo $this->mdl_clients->form_value('client_web'); ?>">
                        </div>
                    </div>

                </fieldset>                
            </div>
            
        </div>

        <div class="row-fluid">
                
            <div class="span12">
                <fieldset>
                        
                        <legend><?php echo lang('custom_fields'); ?></legend>
                        
                        <?php foreach ($custom_fields as $custom_field) { ?>
                        <div class="control-group">
                            <label class="control-label"><?php echo $custom_field->custom_field_label; ?>: </label>
                            <div class="controls">
                                <input type="text" name="custom[<?php echo $custom_field->custom_field_column; ?>]" id="<?php echo $custom_field->custom_field_column; ?>" value="<?php echo form_prep($this->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                            </div>
                        </div>
                        <?php } ?>
                </fieldset>
            </div>

        </div>

	</div>

</form>