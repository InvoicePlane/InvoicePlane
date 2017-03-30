<script>
    $(function () {
        toggle_smtp_settings();

        $('#email_send_method').change(function () {
            toggle_smtp_settings();
        });

        function toggle_smtp_settings() {
            email_send_method = $('#email_send_method').val();

            if (email_send_method == 'smtp') {
                $('#div-smtp-settings').show();
            } else {
                $('#div-smtp-settings').hide();
            }
        }
    });
</script>

<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="form-group">
        <label for="settings[email_pdf_attachment]">
            <?php echo trans('email_pdf_attachment'); ?>
        </label>
        <select name="settings[email_pdf_attachment]" class=" form-control simple-select">
            <option value="0">
                <?php echo trans('no'); ?>
            </option>
            <option value="1" <?php check_select(get_setting('email_pdf_attachment'), '1'); ?>>
                <?php echo trans('yes'); ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[email_send_method]" class="control-label">
            <?php echo trans('email_send_method'); ?>
        </label>
        <select name="settings[email_send_method]" id="email_send_method"
                class=" form-control simple-select">
            <option value=""><?php echo trans('none'); ?></option>
            <option value="phpmail" <?php check_select(get_setting('email_send_method'), 'phpmail'); ?>>
                <?php echo trans('email_send_method_phpmail'); ?>
            </option>
            <option value="sendmail" <?php check_select(get_setting('email_send_method'), 'sendmail'); ?>>
                <?php echo trans('email_send_method_sendmail'); ?>
            </option>
            <option value="smtp" <?php check_select(get_setting('email_send_method'), 'smtp'); ?>>
                <?php echo trans('email_send_method_smtp'); ?>
            </option>
        </select>
    </div>

    <div id="div-smtp-settings">
        <div class="form-group">
            <label for="settings[smtp_server_address]" class="control-label">
                <?php echo trans('smtp_server_address'); ?>
            </label>
            <input type="text" name="settings[smtp_server_address]" class=" form-control"
                   value="<?php echo get_setting('smtp_server_address'); ?>">
        </div>

        <div class="form-group">
            <label for="settings[smtp_authentication]">
                <?php echo trans('smtp_requires_authentication'); ?>
            </label>
            <select name="settings[smtp_authentication]" class=" form-control simple-select">
                <option value="0">
                    <?php echo trans('no'); ?>
                </option>
                <option value="1" <?php check_select(get_setting('smtp_authentication'), '1'); ?>>
                    <?php echo trans('yes'); ?>
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="settings[smtp_username]" class="control-label">
                <?php echo trans('smtp_username'); ?>
            </label>
            <input type="text" name="settings[smtp_username]" class=" form-control"
                   value="<?php echo get_setting('smtp_username'); ?>">
        </div>

        <div class="form-group">
            <label for="smtp_password" class="control-label">
                <?php echo trans('smtp_password'); ?>
            </label>
            <input type="password" id="smtp_password" class=" form-control" name="settings[smtp_password]"
                   value="<?php echo $this->encrypt->decode(get_setting('settings[smtp_password]')); ?>">
            <input type="hidden" name="settings[smtp_password_field_is_password]" value="1">
        </div>

        <div class="form-group">
            <div>
                <label for="settings[smtp_port]" class="control-label">
                    <?php echo trans('smtp_port'); ?>
                </label>
                <input type="number" name="settings[smtp_port]" class=" form-control"
                       value="<?php echo get_setting('smtp_port'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="settings[smtp_security]" class="control-label">
                <?php echo trans('smtp_security'); ?>
            </label>
            <select name="settings[smtp_security]" class=" form-control simple-select">
                <option value=""><?php echo trans('none'); ?></option>
                <option value="ssl" <?php check_select(get_setting('smtp_security'), 'ssl'); ?>>
                    <?php echo trans('smtp_ssl'); ?>
                </option>
                <option value="tls" <?php check_select(get_setting('smtp_security'), 'tls'); ?>>
                    <?php echo trans('smtp_tls'); ?>
                </option>
            </select>
        </div>
    </div>

</div>
