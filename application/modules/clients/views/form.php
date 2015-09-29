<script>
    $(function () {
        $('#client_name').focus();
        $("#client_country").select2({allowClear: true});
    });
</script>

<form method="post">

    <div id="headerbar">
        <h1><?php echo lang('client_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
               value="<?php if ($this->mdl_clients->form_value('is_update')) : ?>1<?php else : ?>0<?php endif; ?>">

        <fieldset>
            <legend><?php echo lang('personal_information'); ?></legend>
            <div class="input-group col-xs-6">
              <span class="input-group-addon">
                <?php echo lang('active_client'); ?>: 
                <input id="client_active" name="client_active" type="checkbox" value="1"
                    <?php if ($this->mdl_clients->form_value('client_active') == 1
                        or !is_numeric($this->mdl_clients->form_value('client_active'))
                    ) : ?>
                        checked="checked"
                    <?php endif; ?>>
              </span>
                <input id="client_name" name="client_name" type="text" class="form-control"
                       placeholder="<?php echo lang('client_name'); ?>"
                       value="<?php echo $this->mdl_clients->form_value('client_name'); ?>">
            </div>
        </fieldset>

        <div class="row">

            <div class="col-xs-12 col-sm-6">
                <fieldset>
                    <legend><?php echo lang('address'); ?></legend>

                    <div class="form-group">
                        <label><?php echo lang('street_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_address_1" id="client_address_1" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_address_1'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('street_address_2'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_address_2" id="client_address_2" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_address_2'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('city'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_city" id="client_city" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_city'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('state'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_state" id="client_state" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_state'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('zip_code'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_zip" id="client_zip" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_zip'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('country'); ?>: </label>

                        <div class="controls">
                            <select name="client_country" id="client_country" class="form-control">
                                <option></option>
                                <?php foreach ($countries as $cldr => $country) : ?>
                                    <option value="<?php echo $cldr; ?>"
                                            <?php if ($selected_country == $cldr) : ?>selected="selected"<?php endif; ?>><?php echo $country ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-6">
                <fieldset>

                    <legend><?php echo lang('contact_information'); ?></legend>

                    <div class="form-group">
                        <label><?php echo lang('phone_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_phone" id="client_phone" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_phone'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('fax_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_fax" id="client_fax" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_fax'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('mobile_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_mobile" id="client_mobile" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_mobile'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('email_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_email" id="client_email" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_email'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('web_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_web" id="client_web" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_web'); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-6">
                <fieldset>

                    <legend><?php echo lang('tax_information'); ?></legend>

                    <div class="form-group">
                        <label><?php echo lang('vat_id'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_vat_id'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('tax_code'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_tax_code" id="client_tax_code" class="form-control"
                                   value="<?php echo $this->mdl_clients->form_value('client_tax_code'); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>

        </div>

        <?php if ($custom_fields) : ?>
            <div class="row">
                <div class="col-xs-12">
                    <fieldset>
                        <legend><?php echo lang('custom_fields'); ?></legend>
                        <?php foreach ($custom_fields as $custom_field) : ?>
                            <div class="form-group">
                                <label><?php echo $custom_field->custom_field_label; ?>: </label>

                                <div class="controls">
                                    <?php switch ($custom_field->custom_field_type) : ?>
<?php case 'ip_fieldtype_input': ?>
                                            <input type="text" class="form-control"
                                                   name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                                   id="<?php echo $custom_field->custom_field_column; ?>"
                                                   value="<?php echo form_prep($this->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                                            <?php break; ?>

                                        <?php case 'ip_fieldtype_textarea': ?>
                                            <textarea name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                                      id="<?php echo $custom_field->custom_field_column; ?>"
                                                      class="form-control"><?php echo form_prep($this->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']')); ?></textarea>
                                            <?php break; ?>

                                        <?php endswitch; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
            </div>
        <?php endif; ?>
    </div>
</form>
