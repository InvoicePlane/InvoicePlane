<script type="text/javascript">
    $(function () {
        show_fields();

        $('#user_type').change(function () {
            show_fields();
        });

        function show_fields() {
            $('#administrator_fields').hide();
            $('#guest_fields').hide();

            user_type = $('#user_type').val();

            if (user_type == 1) {
                $('#administrator_fields').show();
            }
            else if (user_type == 2) {
                $('#guest_fields').show();
            }
        }

        $("#user_country").select2({
            placeholder: "<?php echo trans('country'); ?>",
            allowClear: true
        });
    });
</script>

<?php if (isset($modal_user_client)) {
    echo $modal_user_client;
} ?>

<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo trans('user_form'); ?></h1>
        <?php echo $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php echo $this->layout->load_view('layout/alerts'); ?>

        <div id="userInfo">

            <fieldset>
                <legend><?php echo trans('account_information'); ?></legend>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                        <label><?php echo trans('name'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="user_name" id="user_name" class="form-control"
                               value="<?php echo $this->mdl_users->form_value('user_name'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                        <label class="control-label">
                            <?php echo trans('company'); ?>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="user_company" id="user_company" class="form-control"
                               value="<?php echo $this->mdl_users->form_value('user_company'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                        <label class="control-label">
                            <?php echo trans('email_address'); ?>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="user_email" id="user_email" class="form-control"
                               value="<?php echo $this->mdl_users->form_value('user_email'); ?>">
                    </div>
                </div>

                <?php if (!$id) { ?>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('password'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="password" name="user_password" id="user_password" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('verify_password'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="password" name="user_passwordv" id="user_passwordv" class="form-control">
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label>
                                <?php echo trans('change_password'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo anchor('users/change_password/' . $id, trans('change_password')); ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                        <label class="control-label">
                            <?php echo trans('user_type'); ?>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <select name="user_type" id="user_type" class="form-control">
                            <option value=""></option>
                            <?php foreach ($user_types as $key => $type) { ?>
                                <option value="<?php echo $key; ?>"
                                        <?php if ($this->mdl_users->form_value('user_type') == $key) { ?>selected="selected"<?php } ?>><?php echo $type; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

            </fieldset>

            <div id="administrator_fields">
                <fieldset>
                    <legend><?php echo trans('address'); ?></legend>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('street_address'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_address_1" id="user_address_1" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_address_1'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('street_address_2'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_address_2" id="user_address_2" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_address_2'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('city'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_city" id="user_city" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_city'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('state'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_state" id="user_state" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_state'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('zip_code'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_zip" id="user_zip" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_zip'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('country'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <select name="user_country" id="user_country" class="form-control">
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

                <fieldset>

                    <legend><?php echo trans('tax_information'); ?></legend>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('vat_id'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_vat_id" id="user_vat_id" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_vat_id'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('tax_code'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_tax_code" id="user_tax_code" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_tax_code'); ?>">
                        </div>
                    </div>

                </fieldset>

                <fieldset>

                    <legend><?php echo trans('contact_information'); ?></legend>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('phone_number'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_phone" id="user_phone" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_phone'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('fax_number'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_fax" id="user_fax" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_fax'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('mobile_number'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_mobile" id="user_mobile" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_mobile'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?php echo trans('web_address'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="user_web" id="user_web" class="form-control"
                                   value="<?php echo $this->mdl_users->form_value('user_web'); ?>">
                        </div>
                    </div>

                </fieldset>

                <fieldset>

                    <legend><?php echo trans('custom_fields'); ?></legend>

                    <?php foreach ($custom_fields as $custom_field) { ?>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label">
                                    <?php echo $custom_field->custom_field_label; ?>
                                </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" class="form-control"
                                       name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                       id="<?php echo $custom_field->custom_field_column; ?>"
                                       value="<?php echo form_prep($this->mdl_users->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                            </div>
                        </div>
                    <?php } ?>
                </fieldset>

            </div>

            <div id="guest_fields">

                <div id="open_invoices" class="widget">

                    <div class="widget-title">
                        <h5 style="float: left;"><?php echo trans('client_access'); ?></h5>

                        <div class="pull-right">
                            <a href="#add-user-client" class="btn btn-default" data-toggle="modal">
                                <i class="fa fa-plus"></i>
                                <?php echo trans('add_client'); ?>
                            </a>
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