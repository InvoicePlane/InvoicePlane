<script type="text/javascript">
    $().ready(function () {
        $("[name='user_country']").select2({allowClear: true});
    });
</script>

<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <input type="hidden" name="user_type" value="1">

            <legend><?php echo lang('setup_create_user'); ?></legend>

            <?php echo $this->layout->load_view('layout/alerts'); ?>

            <p><?php echo lang('setup_create_user_message'); ?></p>

            <div class="form-group">
                <label><?php echo lang('email_address'); ?></label>
                <input type="email" name="user_email" id="user_email" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_email'); ?>">
                <span class="help-block"><?php echo lang('setup_user_email_info'); ?></span>
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('name'); ?>
                </label>
                <input type="text" name="user_name" id="user_name" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_name'); ?>">
                <span class="help-block"><?php echo lang('setup_user_name_info'); ?></span>
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('password'); ?>
                </label>
                <input type="password" name="user_password" id="user_password" class="form-control">
                <span class="help-block"><?php echo lang('setup_user_password_info'); ?></span>
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('verify_password'); ?>
                </label>
                <input type="password" name="user_passwordv" id="user_passwordv" class="form-control">
                <span class="help-block"><?php echo lang('setup_user_password_verify_info'); ?></span>
            </div>

            <legend><?php echo lang('address'); ?></legend>
            <p><?php echo lang('setup_user_address_info'); ?></p>

            <div class="form-group">
                <label>
                    <?php echo lang('street_address'); ?>
                </label>
                <input type="text" name="user_address_1" id="user_address_1" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_address_1'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('street_address_2'); ?>
                </label>
                <input type="text" name="user_address_2" id="user_address_2" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_address_2'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('city'); ?>
                </label>
                <input type="text" name="user_city" id="user_city" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_city'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('state'); ?>
                </label>
                <input type="text" name="user_state" id="user_state" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_state'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('zip_code'); ?>
                </label>
                <input type="text" name="user_zip" id="user_zip" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_zip'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('country'); ?>
                </label>
                <select name="user_country" class="form-control">
                    <option></option>
                    <?php foreach ($countries as $cldr => $country) { ?>
                        <option value="<?php echo $cldr; ?>"
                                <?php if ($this->mdl_users->form_value('user_country') == $cldr) { ?>selected="selected"<?php } ?>><?php echo $country ?></option>
                    <?php } ?>
                </select>
            </div>

            <legend><?php echo lang('setup_other_contact'); ?></legend>

            <p><?php echo lang('setup_user_contact_info'); ?></p>

            <div class="form-group">
                <label>
                    <?php echo lang('phone'); ?>
                </label>
                <input type="text" name="user_phone" id="user_phone" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_phone'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('fax'); ?>
                </label>
                <input type="text" name="user_fax" id="user_fax" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_fax'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('mobile'); ?>
                </label>
                <input type="text" name="user_mobile" id="user_mobile" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_mobile'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label>
                    <?php echo lang('web'); ?>
                </label>
                <input type="text" name="user_web" id="user_web" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_web'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <input type="submit" class="btn btn-success" name="btn_continue"
                   value="<?php echo lang('continue'); ?>">

        </form>

    </div>
</div>