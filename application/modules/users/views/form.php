<?php
$itsCompany   = $this->mdl_users->form_value('user_company') || $this->mdl_users->form_value('user_vat_id');
$qr_code_info = get_setting('qr_code') ? '<span class="pull-right"><i class="fa fa-qrcode"  data-toggle="tooltip" data-placement="bottom" title="' . trans('user_qr_code_hint') . '"></i></span>' : '';
// eInvoicing enabled?
$einvoicingTip = $einvoicing ? ' data-toggle="tooltip" data-placement="bottom" title="e-' . trans('invoicing') . ' (' : ''; // tootip base
$einvoicingReq = $einvoicing ? $einvoicingTip . trans('required_field') . ')"' : '';
$einvoicingB2B = $einvoicing ? $einvoicingTip . 'B2B ' . trans('required_field') . ')"' : '';
$einvoicingOpt = $einvoicing ? $einvoicingTip . trans('optional') . ')"' : '';
?>
<script>
    $(function () {
        show_fields();

        $('#user_type').change(function () {
            show_fields();
        });

        function show_fields() {
            $('#administrator_fields').hide();
            $('#guest_fields').hide(); // Todo this id missing (IMO: It's for old? modal user-client). (Idea* Why not a new user `type` system)

            var user_type = $('#user_type').val();

            if (user_type === '1') {
                $('#administrator_fields').show();
            } else if (user_type === '2') {
                $('#guest_fields').show(); // Todo this id missing. (Idea* For a new user type, like company? Need new module?)
            }
        }

        $('#user_country').select2({
            placeholder: '<?php _trans('country'); ?>',
            allowClear: true
        });

        $('#add-user-client-modal').click(function () {
            <?php $user_id = $id ?? ''; ?>
            $('#modal-placeholder').load("<?php echo site_url('users/ajax/modal_add_user_client/' . $user_id); ?>");
        });
    });
</script>

<form method="post">

    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('user_form'); ?></h1>
        <?php echo $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">
        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <?php echo $this->layout->load_view('layout/alerts'); ?>

                <div id="userInfo">

                    <div class="panel panel-default">
                        <div class="panel-heading"><?php _trans('account_information'); ?></div>

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="user_name"><?php _trans('name'); ?></label>
                                <input type="text" name="user_name" id="user_name" class="form-control"
                                       value="<?php echo $this->mdl_users->form_value('user_name', true); ?>">
                            </div>

                            <div class="form-group"<?php echo  $itsCompany ? $einvoicingB2B : $einvoicingReq; ?>>
                                <label for="user_company"><?php _trans('company'); ?> (<?php _trans($itsCompany ? 'required_field' : 'optional'); ?>)</label><?php echo $qr_code_info; ?>
                                <input type="text" name="user_company" id="user_company" class="form-control"
                                       value="<?php echo $this->mdl_users->form_value('user_company', true); ?>">
                            </div>

                            <div class="form-group">
                                <label for="user_email"><?php _trans('email_address'); ?></label>
                                <input type="text" name="user_email" id="user_email" class="form-control"
                                       value="<?php echo $this->mdl_users->form_value('user_email', true); ?>" required>
                            </div>

<?php // New user
if ( ! $id) {
?>
                            <div class="form-group">
                                <label for="user_password">
                                    <?php _trans('password'); ?>
                                </label>
                                <input type="password" name="user_password" id="user_password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="user_password">
                                    <?php _trans('verify_password'); ?>
                                </label>
                                <input type="password" name="user_passwordv" id="user_passwordv" class="form-control" required>
                            </div>
<?php
} else { // Edit user
?>
                            <div class="form-group">
                                <a href="<?php echo site_url('users/change_password/' . $id); ?>"
                                   class="btn btn-default">
                                    <?php _trans('change_password'); ?>
                                </a>
                            </div>
<?php
}
?>

                            <div class="form-group">
                                <label for="user_language"><?php _trans('language'); ?></label>
                                <select name="user_language" id="user_language" class="form-control simple-select" required>
                                    <option value="system">
                                        <?php echo trans('use_system_language') ?>
                                    </option>
<?php
$usr_lang = $this->mdl_users->form_value('user_language');
foreach ($languages as $language) {
?>
                                    <option value="<?php echo $language; ?>" <?php check_select($usr_lang, $language); ?>>
                                        <?php echo ucfirst($language); ?>
                                    </option>
<?php
}
?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="user_type"><?php _trans('user_type'); ?></label>
                                <select name="user_type" id="user_type" class="form-control simple-select" required>
<?php
$user_type = $this->mdl_users->form_value('user_type');
foreach ($user_types as $key => $type) {
?>
                                    <option value="<?php echo $key; ?>" <?php check_select($user_type, $key); ?>>
                                        <?php echo $type; ?>
                                    </option>
<?php
}
?>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div id="administrator_fields">
                        <div class="panel panel-default">
                            <div class="panel-heading"><?php _trans('address'); ?></div>

                            <div class="panel-body">
                                <div class="form-group"<?php echo $einvoicingReq; ?>>
                                    <label for="user_address_1"><?php _trans('street_address'); ?></label>
                                    <input type="text" name="user_address_1" id="user_address_1" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_address_1', true); ?>">
                                </div>

                                <div class="form-group"<?php echo $einvoicingOpt; ?>>
                                    <label for="user_address_2"><?php _trans('street_address_2'); ?></label>
                                    <input type="text" name="user_address_2" id="user_address_2" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_address_2', true); ?>">
                                </div>

                                <div class="form-group"<?php echo $einvoicingReq; ?>>
                                    <label for="user_city"><?php _trans('city'); ?></label>
                                    <input type="text" name="user_city" id="user_city" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_city', true); ?>">
                                </div>

                                <div class="form-group"<?php echo $einvoicingOpt; ?>>
                                    <label for="user_state"><?php _trans('state'); ?></label>
                                    <input type="text" name="user_state" id="user_state" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_state', true); ?>">
                                </div>

                                <div class="form-group"<?php echo $einvoicingReq; ?>>
                                    <label for="user_zip"><?php _trans('zip_code'); ?></label>
                                    <input type="text" name="user_zip" id="user_zip" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_zip', true); ?>">
                                </div>

                                <div class="form-group"<?php echo $einvoicingReq; ?>>
                                    <label for="user_country"><?php _trans('country'); ?></label>
                                    <select name="user_country" id="user_country" class="form-control">
                                        <option value=""><?php _trans('none'); ?></option>
<?php
foreach ($countries as $cldr => $country) {
?>
                                        <option value="<?php echo $cldr; ?>"
                                            <?php check_select($selected_country, $cldr); ?>>
                                            <?php echo $country ?>
                                        </option>
<?php
}
?>
                                    </select>
                                </div>
<?php
foreach ($custom_fields['ip_user_custom'] as $custom_field) {
    if ($custom_field->custom_field_location == 2) {
        print_field($this->mdl_users, $custom_field, $custom_values);
    }
}
?>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading"><?php _trans('tax_information'); ?></div>

                            <div class="panel-body">
                                <div class="form-group"<?php echo  $itsCompany ? $einvoicingB2B : $einvoicingOpt; ?>>
                                    <label for="user_vat_id"><?php _trans('vat_id'); ?> (<?php _trans($itsCompany ? 'required_field' : 'optional'); ?>)</label>
                                    <input type="text" name="user_vat_id" id="user_vat_id" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_vat_id', true); ?>">
                                </div>

                                <div class="form-group"<?php echo $einvoicingReq; ?>>
                                    <label for="user_tax_code"><?php _trans('tax_code'); ?></label>
                                    <input type="text" name="user_tax_code" id="user_tax_code" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_tax_code', true); ?>">
                                </div>

<?php
foreach ($custom_fields['ip_user_custom'] as $custom_field) {
    if ($custom_field->custom_field_location == 3) {
        print_field($this->mdl_users, $custom_field, $custom_values);
    }
}
?>

                            </div>
                        </div>

                        <!-- eInvoicing -->
                        <div class="panel panel-default">
                            <div class="panel-heading"><?php _trans('bank_information'); ?></div>

                            <div class="panel-body">
                                <div class="form-group"<?php echo $einvoicingOpt; ?>>
                                    <label for="user_bank"><?php _trans('bank'); ?></label>
                                    <input type="text" name="user_bank" id="user_bank" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_bank', true); ?>">
                                </div>

                                <div class="form-group"<?php echo $einvoicingReq; ?>><?php echo $qr_code_info; ?>
                                    <label for="user_iban"><?php echo 'IBAN'; ?></label>
                                    <input type="text" name="user_iban" id="user_iban" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_iban', true); ?>">
                                </div>

                                <div class="form-group"<?php echo $einvoicingOpt; ?>><?php echo $qr_code_info; ?>
                                    <label for="user_bic"><?php echo 'BIC'; ?></label>
                                    <input type="text" name="user_bic" id="user_bic"
                                           class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_bic', true); ?>">
                                </div>

                                <div class="form-group"><?php echo $qr_code_info; ?>
                                    <label for="user_remittance_text"><?php _trans('user_remittance_text'); ?></label>
                                    <input type="text" name="user_remittance_text" id="user_remittance_text" class="form-control taggable"
                                           placeholder="{{{invoice_number}}} {{{invoice_date_due}}}"
                                           value="<?php echo $this->mdl_users->form_value('user_remittance_text', true); ?>">
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?php _trans('qr_code_settings_remittance_text_tags'); ?>
                                </div>
                                <div class="panel-body">
                                    <?php $this->layout->load_view('email_templates/template-tags-invoices'); ?>
                                </div>
                            </div>

                        </div>

<?php
if ($this->mdl_settings->setting('sumex') == '1') {
?>
                        <div class="panel panel-default">
                            <div class="panel-heading"><?php _trans('sumex_information'); ?></div>

                            <div class="panel-body">

                                <div class="form-group">
                                    <label for="user_subscribernumber"><?php _trans('user_subscriber_number'); ?></label>
                                    <input type="text" name="user_subscribernumber" id="user_subscribernumber" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_subscribernumber', true); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="user_gln"><?php _trans('gln'); ?></label>
                                    <input type="text" name="user_gln" id="user_gln" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_gln', true); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="user_rcc"><?php _trans('sumex_rcc'); ?></label>
                                    <input type="text" name="user_rcc" id="user_rcc" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_rcc', true); ?>">
                                </div>
                            </div>

                        </div>

<?php
} // Endif sumex
?>
                        <div class="panel panel-default">

                            <div class="panel-heading"><?php _trans('contact_information'); ?></div>

                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="user_invoicing_con>tact"><?php _trans('contact'); ?> (<?php _trans('invoicing'); ?>)</label>
                                    <input type="text" name="user_invoicing_contact" id="user_invoicing_contact" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_invoicing_contact', true); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="user_phone"><?php _trans('phone_number'); ?></label>
                                    <input type="text" name="user_phone" id="user_phone" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_phone', true); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="user_fax"><?php _trans('fax_number'); ?></label>
                                    <input type="text" name="user_fax" id="user_fax" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_fax', true); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="user_mobile"><?php _trans('mobile_number'); ?></label>
                                    <input type="text" name="user_mobile" id="user_mobile" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_mobile', true); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="user_web"><?php _trans('web_address'); ?></label>
                                    <input type="text" name="user_web" id="user_web" class="form-control"
                                           value="<?php echo $this->mdl_users->form_value('user_web', true); ?>">
                                </div>
<?php
$default_custom = false;
foreach ($custom_fields['ip_user_custom'] as $custom_field) {
    if ( ! $default_custom && ! $custom_field->custom_field_location) {
        $default_custom = true;
    }

    if ($custom_field->custom_field_location == 4) {
        print_field($this->mdl_users, $custom_field, $custom_values);
    }
}
?>

                            </div>

                        </div>

<?php
if ($default_custom) {
?>
                        <div class="row">
                            <div class="col-xs-12">

                                <hr>

                                <div class="panel panel-default">
                                    <div class="panel-heading"><?php _trans('custom_fields'); ?></div>
                                    <div class="panel-body">
                                        <div class="row">
<?php
    $classes = ['control-label', 'controls', '', 'form-group col-xs-12 col-sm-6'];
    foreach ($custom_fields['ip_user_custom'] as $custom_field) {
        if ( ! $custom_field->custom_field_location) { // == 0
            print_field($this->mdl_users, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
        }
    }
?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
<?php
} // End if custom_fields
?>
                   </div> <!-- end administrator_fields -->

                </div><!-- userinfo -->

            </div><!-- col -->
        </div><!-- row -->
    </div><!-- content -->
</form>
