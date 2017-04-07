<script>
    $(function () {
        $("[name='user_country']").select2({
            placeholder: "<?php _trans('country'); ?>",
            allowClear: true
        });
    });
</script>

<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

            <input type="hidden" name="user_type" value="1">

            <legend><?php _trans('setup_create_user'); ?></legend>

            <?php echo $this->layout->load_view('layout/alerts'); ?>

            <p><?php _trans('setup_create_user_message'); ?></p>

            <div class="form-group">
                <label for="user_email">
                    <?php _trans('email_address'); ?>
                </label>
                <input type="email" name="user_email" id="user_email" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_email', true); ?>">
                <span class="help-block"><?php _trans('setup_user_email_info'); ?></span>
            </div>

            <div class="form-group">
                <label for="user_name">
                    <?php _trans('name'); ?>
                </label>
                <input type="text" name="user_name" id="user_name" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_name', true); ?>">
                <span class="help-block"><?php _trans('setup_user_name_info'); ?></span>
            </div>

            <div class="form-group">
                <label for="user_password">
                    <?php _trans('password'); ?>
                </label>
                <input type="password" name="user_password" id="user_password" class="form-control">
                <span class="help-block"><?php _trans('setup_user_password_info'); ?></span>
            </div>

            <div class="form-group">
                <label for="user_passwordv">
                    <?php _trans('verify_password'); ?>
                </label>
                <input type="password" name="user_passwordv" id="user_passwordv" class="form-control">
                <span class="help-block"><?php _trans('setup_user_password_verify_info'); ?></span>
            </div>

            <div class="form-group">
                <label for="user_language">
                    <?php _trans('language'); ?>
                </label>
                <select name="user_language" id="user_language" class="form-control simple-select">
                    <option value="system">
                        <?php echo trans('use_system_language') ?>
                    </option>
                    <?php foreach ($languages as $language) { ?>
                        <option value="<?php echo $language; ?>">
                            <?php echo ucfirst($language); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <legend><?php _trans('address'); ?></legend>
            <p><?php _trans('setup_user_address_info'); ?></p>

            <div class="form-group">
                <label>
                    <?php _trans('street_address'); ?>
                </label>
                <input type="text" name="user_address_1" id="user_address_1" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_address_1', true); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php _trans('street_address_2'); ?>
                </label>
                <input type="text" name="user_address_2" id="user_address_2" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_address_2', true); ?>"
                       placeholder="<?php _trans('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php _trans('city'); ?>
                </label>
                <input type="text" name="user_city" id="user_city" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_city', true); ?>"
                       placeholder="<?php _trans('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php _trans('state'); ?>
                </label>
                <input type="text" name="user_state" id="user_state" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_state', true); ?>"
                       placeholder="<?php _trans('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php _trans('zip_code'); ?>
                </label>
                <input type="text" name="user_zip" id="user_zip" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_zip', true); ?>"
                       placeholder="<?php _trans('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php _trans('country'); ?>
                </label>
                <select name="user_country" class="form-control simple-select">
                    <option value=""><?php _trans('none'); ?></option>
                    <?php foreach ($countries as $cldr => $country) { ?>
                        <option value="<?php echo $cldr; ?>"
                            <?php check_select($this->mdl_users->form_value('user_country'), $cldr); ?>>
                            <?php echo $country ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <legend><?php _trans('setup_other_contact'); ?></legend>

            <p><?php _trans('setup_user_contact_info'); ?></p>

            <div class="form-group">
                <label>
                    <?php _trans('phone'); ?>
                </label>
                <input type="text" name="user_phone" id="user_phone" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_phone', true); ?>"
                       placeholder="<?php _trans('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php _trans('fax'); ?>
                </label>
                <input type="text" name="user_fax" id="user_fax" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_fax', true); ?>"
                       placeholder="<?php _trans('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php _trans('mobile'); ?>
                </label>
                <input type="text" name="user_mobile" id="user_mobile" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_mobile', true); ?>"
                       placeholder="<?php _trans('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php _trans('web'); ?>
                </label>
                <input type="text" name="user_web" id="user_web" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_web', true); ?>"
                       placeholder="<?php _trans('optional'); ?>">
            </div>

            <input type="submit" class="btn btn-success" name="btn_continue"
                   value="<?php _trans('continue'); ?>">

        </form>

    </div>
</div>
