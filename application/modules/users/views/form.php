<script type="text/javascript">
    $(function()
    {
        show_fields();
        
        $('#user_type').change(function()
        {
            show_fields();
        });

        function show_fields()
        {
            $('#administrator_fields').hide();
            $('#guest_fields').hide();

            user_type = $('#user_type').val();

            if (user_type == 1)
            {
                $('#administrator_fields').show();
            }
            else if (user_type == 2)
            {
                $('#guest_fields').show();
            }
        }
    });
</script>

<?php if (isset($modal_user_client)) { echo $modal_user_client; } ?>

<form method="post" class="form-horizontal">

    <div class="headerbar">
        <h1><?php echo lang('user_form'); ?></h1>
        <?php echo $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div class="content">

        <?php echo $this->layout->load_view('layout/alerts'); ?>

        <div id="userInfo">

            <fieldset>
                <legend><?php echo lang('account_information'); ?></legend>

                <div class="control-group">
                    <label class="control-label"><?php echo lang('name'); ?>: </label>
                    <div class="controls">
                        <input type="text" name="user_name" id="user_name" value="<?php echo $this->mdl_users->form_value('user_name'); ?>">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"><?php echo lang('email_address'); ?>: </label>
                    <div class="controls">
                        <input type="text" name="user_email" id="user_email" value="<?php echo $this->mdl_users->form_value('user_email'); ?>">
                    </div>
                </div>

                <?php if (!$id) { ?>
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
                <?php } else { ?>
                <div class="control-group">
                    <label><?php echo lang('change_password'); ?>: </label>
                    <div class="controls">
                    <?php echo anchor('users/change_password/' . $id, lang('change_password')); ?>
                    </div>
                </div>
                <?php } ?>

                <div class="control-group">
                    <label class="control-label"><?php echo lang('user_type'); ?></label>
                    <div class="controls">
                        <select name="user_type" id="user_type">
                            <option value=""></option>
                            <?php foreach ($user_types as $key => $type) { ?>
                            <option value="<?php echo $key; ?>" <?php if ($this->mdl_users->form_value('user_type') == $key) { ?>selected="selected"<?php } ?>><?php echo $type; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

            </fieldset>

            <div id="administrator_fields">
                <fieldset>
                    <legend><?php echo lang('address'); ?></legend>

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
                </fieldset>

                <fieldset>

                    <legend><?php echo lang('contact_information'); ?></legend>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('phone_number'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="user_phone" id="user_phone" value="<?php echo $this->mdl_users->form_value('user_phone'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('fax_number'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="user_fax" id="user_fax" value="<?php echo $this->mdl_users->form_value('user_fax'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('mobile_number'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="user_mobile" id="user_mobile" value="<?php echo $this->mdl_users->form_value('user_mobile'); ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo lang('web_address'); ?>: </label>
                        <div class="controls">
                            <input type="text" name="user_web" id="user_web" value="<?php echo $this->mdl_users->form_value('user_web'); ?>">
                        </div>
                    </div>

                </fieldset>
                
                <fieldset>

                    <legend><?php echo lang('custom_fields'); ?></legend>

                    <?php foreach ($custom_fields as $custom_field) { ?>
                    <div class="control-group">
                        <label class="control-label"><?php echo $custom_field->custom_field_label; ?>: </label>
                        <div class="controls">
                            <input type="text" name="custom[<?php echo $custom_field->custom_field_column; ?>]" id="<?php echo $custom_field->custom_field_column; ?>" value="<?php echo form_prep($this->mdl_users->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                        </div>
                    </div>
                    <?php } ?>
                </fieldset>

            </div>
            
            <div id="guest_fields">
                
				<div id="open_invoices" class="widget">

                    <div class="widget-title">
                        <h5 style="float: left;"><?php echo lang('client_access'); ?></h5>
                        <div class="pull-right">
                            <a href="#add-user-client" class="btn" style="margin-right: 5px;" data-toggle="modal"><i class="icon-plus-sign"></i> <?php echo lang('add_client'); ?></a>
                        </div>
                    </div>
                    
                    <div id="div_user_client_table">
                    <?php echo $user_client_table; ?>
                    </div>

				</div>
                                
            </div>

        </div>

    </div>

</form>