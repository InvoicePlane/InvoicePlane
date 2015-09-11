<script type="text/javascript">
    $(function () {
        toggle_smtp_settings();

        $('#email_send_method').change(function () {
            toggle_smtp_settings();
        });

        function toggle_smtp_settings() {

            email_send_method = $('#email_send_method').val();

            if (email_send_method == 'smtp') {
                $('#div-smtp-settings').show();
            }
            else {
                $('#div-smtp-settings').hide();
            }
        }

    });
</script>

<div class="tab-info">

    <div class="form-group">
        <label for="settings[email_send_method]" class="control-label">
            <?php echo lang('email_send_method'); ?>
        </label>
        <select name="settings[email_send_method]" id="email_send_method"
                class="input-sm form-control">
            <option value=""></option>
            <option value="phpmail"
                    <?php if ($this->mdl_settings->setting('email_send_method') == 'phpmail') { ?>selected="selected"<?php } ?>>
                <?php echo lang('email_send_method_phpmail'); ?>
            </option>
            <option value="sendmail"
                    <?php if ($this->mdl_settings->setting('email_send_method') == 'sendmail') { ?>selected="selected"<?php } ?>>
                <?php echo lang('email_send_method_sendmail'); ?>
            </option>
            <option value="smtp"
                    <?php if ($this->mdl_settings->setting('email_send_method') == 'smtp') { ?>selected="selected"<?php } ?>>
                <?php echo lang('email_send_method_smtp'); ?>
            </option>
        </select>
    </div>
</div>

<div id="div-smtp-settings" class="tab-info">
    <div class="form-group">
        <label for="settings[smtp_server_address]" class="control-label">
            <?php echo lang('smtp_server_address'); ?>
        </label>
        <input type="text" name="settings[smtp_server_address]" class="input-sm form-control"
               value="<?php echo $this->mdl_settings->setting('smtp_server_address'); ?>">
    </div>

    <div class="form-group">
        <label for="settings[smtp_authentication]">
            <?php echo lang('smtp_requires_authentication'); ?>
        </label>
        <select name="settings[smtp_authentication]" class="input-sm form-control">
            <option value="0"
                    <?php if (!$this->mdl_settings->setting('smtp_authentication')) { ?>selected="selected"<?php } ?>>
                <?php echo lang('no'); ?>
            </option>
            <option value="1"
                    <?php if ($this->mdl_settings->setting('smtp_authentication')) { ?>selected="selected"<?php } ?>>
                <?php echo lang('yes'); ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[smtp_username]" class="control-label">
            <?php echo lang('smtp_username'); ?>
        </label>
        <input type="text" name="settings[smtp_username]" class="input-sm form-control"
               value="<?php echo $this->mdl_settings->setting('smtp_username'); ?>">
    </div>

    <div class="form-group">
        <label for="smtp_password" class="control-label">
            <?php echo lang('smtp_password'); ?>
        </label>
        <input type="password" id="smtp_password" class="input-sm form-control" name="settings[smtp_password]"
               value="<?php $this->load->library('encrypt');
               echo $this->encrypt->decode($this->mdl_settings->setting('smtp_password')); ?>">
    </div>

    <div class="form-group">
        <div>
            <label for="settings[smtp_port]" class="control-label">
                <?php echo lang('smtp_port'); ?>
            </label>
            <input type="text" name="settings[smtp_port]" class="input-sm form-control"
                   value="<?php echo $this->mdl_settings->setting('smtp_port'); ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="settings[smtp_security]" class="control-label">
            <?php echo lang('smtp_security'); ?>
        </label>
        <select name="settings[smtp_security]" class="input-sm form-control">
            <option value=""
                    <?php if (!$this->mdl_settings->setting('smtp_security')) { ?>selected="selected"<?php } ?>><?php echo lang('none'); ?></option>
            <option value="ssl"
                    <?php if ($this->mdl_settings->setting('smtp_security') == 'ssl') { ?>selected="selected"<?php } ?>><?php echo lang('smtp_ssl'); ?></option>
            <option value="tls"
                    <?php if ($this->mdl_settings->setting('smtp_security') == 'tls') { ?>selected="selected"<?php } ?>><?php echo lang('smtp_tls'); ?></option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="settings[email_pdf_attachment]">
        <?php echo lang('email_pdf_attachment'); ?>
    </label>
    <select name="settings[email_pdf_attachment]" class="input-sm form-control">
        <option value="0"
                <?php if (!$this->mdl_settings->setting('email_pdf_attachment')) { ?>selected="selected"<?php } ?>>
            <?php echo lang('no'); ?>
        </option>
        <option value="1"
                <?php if ($this->mdl_settings->setting('email_pdf_attachment')) { ?>selected="selected"<?php } ?>>
            <?php echo lang('yes'); ?>
        </option>
    </select>
</div>