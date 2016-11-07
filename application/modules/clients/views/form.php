<script type="text/javascript">
    $(function () {
        $('#client_name').focus();
        $("#client_country").select2({
            placeholder: "<?php echo trans('country'); ?>",
            allowClear: true
        });
    });
</script>

<form method="post">

    <div id="headerbar">
        <h1><?php echo trans('client_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_clients->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
        >

        <fieldset>
            <legend><?php echo trans('personal_information'); ?></legend>
            <div class="input-group col-xs-6">
              <span class="input-group-addon">
                <?php echo trans('active_client'); ?>:
                <input id="client_active" name="client_active" type="checkbox" value="1"
                    <?php if ($this->mdl_clients->form_value('client_active') == 1
                        or !is_numeric($this->mdl_clients->form_value('client_active'))
                    ) {
                        echo 'checked="checked"';
                    } ?>
                >
              </span>
                <input id="client_name" name="client_name" type="text" class="form-control"
                       placeholder="<?php echo trans('client_name'); ?>"
                       value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_name')); ?>">
            </div>
        </fieldset>

        <div class="row">

            <div class="col-xs-12 col-sm-6">
                <fieldset>
                    <legend><?php echo trans('address'); ?></legend>

                    <div class="form-group">
                        <label><?php echo trans('street_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_address_1" id="client_address_1" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_address_1')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('street_address_2'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_address_2" id="client_address_2" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_address_2')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('city'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_city" id="client_city" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_city')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('state'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_state" id="client_state" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_state')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('zip_code'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_zip" id="client_zip" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_zip')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('country'); ?>: </label>

                        <div class="controls">
                            <select name="client_country" id="client_country" class="form-control">
                                <option></option>
                                <?php foreach ($countries as $cldr => $country) { ?>
                                    <option value="<?php echo $cldr; ?>"
                                        <?php if ($selected_country == $cldr) {
                                            echo 'selected="selected"';
                                        } ?>
                                    ><?php echo $country ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-6">
                <fieldset>

                    <legend><?php echo trans('contact_information'); ?></legend>

                    <div class="form-group">
                        <label><?php echo trans('phone_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_phone" id="client_phone" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_phone')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('fax_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_fax" id="client_fax" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_fax')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('mobile_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_mobile" id="client_mobile" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_mobile')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('email_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_email" id="client_email" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_email')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('web_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_web" id="client_web" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_web')); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-6">
                <fieldset>

                    <legend><?php echo trans('tax_information'); ?></legend>

                    <div class="form-group">
                        <label><?php echo trans('vat_id'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_vat_id')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('tax_code'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_tax_code" id="client_tax_code" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_tax_code')); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>

        </div>

        <?php if ($custom_fields) { ?>
            <div class="row">
                <div class="col-xs-12">
                    <fieldset>
                        <legend><?php echo trans('custom_fields'); ?></legend>
                        <?php foreach ($custom_fields as $custom_field) { ?>
                            <div class="form-group">
                                <label><?php echo $custom_field->custom_field_label; ?>: </label>

                                <div class="controls">
                                    <input type="text" class="form-control"
                                           name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                           id="<?php echo $custom_field->custom_field_column; ?>"
                                           value="<?php echo form_prep($this->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                                </div>
                            </div>
                        <?php } ?>
                    </fieldset>
                </div>
            </div>
        <?php } ?>
    </div>
</form>
