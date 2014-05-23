<script type="text/javascript">
$(function() {
	$('#smtp_password').val('');
    
    toggle_smtp_settings();
    
    $('#email_send_method').change(function() {
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

	<div class="control-group">
		<label class="control-label"><?php echo lang('email_send_method'); ?>: </label>
		<div class="controls">
			<select name="settings[email_send_method]" id="email_send_method">
                <option value=""></option>
				<option value="phpmail" <?php if ($this->mdl_settings->setting('email_send_method')=='phpmail') { ?>selected="selected"<?php } ?>><?php echo lang('email_send_method_phpmail'); ?></option>
				<option value="sendmail" <?php if ($this->mdl_settings->setting('email_send_method')=='sendmail') { ?>selected="selected"<?php } ?>><?php echo lang('email_send_method_sendmail'); ?></option>
				<option value="smtp" <?php if ($this->mdl_settings->setting('email_send_method')=='smtp') { ?>selected="selected"<?php } ?>><?php echo lang('email_send_method_smtp'); ?></option>
			</select>
		</div>
	</div>

    <div id="div-smtp-settings">
        <div class="control-group">
            <label class="control-label"><?php echo lang('smtp_server_address'); ?>: </label>
            <div class="controls">
                <input type="text" name="settings[smtp_server_address]" value="<?php echo $this->mdl_settings->setting('smtp_server_address'); ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo lang('smtp_requires_authentication'); ?>: </label>
            <div class="controls">
                <select name="settings[smtp_authentication]" >
                    <option value="0" <?php if (!$this->mdl_settings->setting('smtp_authentication')) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
                    <option value="1" <?php if ($this->mdl_settings->setting('smtp_authentication')) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo lang('smtp_username'); ?>: </label>
            <div class="controls">
                <input type="text" name="settings[smtp_username]" value="<?php echo $this->mdl_settings->setting('smtp_username'); ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo lang('smtp_password'); ?>: </label>
            <div class="controls">
                <input type="password" id="smtp_password" name="settings[smtp_password]">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo lang('smtp_port'); ?>: </label>
            <div class="controls">
                <input type="text" name="settings[smtp_port]" value="<?php echo $this->mdl_settings->setting('smtp_port'); ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo lang('smtp_security'); ?>: </label>
            <div class="controls">
                <select name="settings[smtp_security]">
                    <option value="" <?php if (!$this->mdl_settings->setting('smtp_security')) { ?>selected="selected"<?php } ?>><?php echo lang('none'); ?></option>
                    <option value="ssl" <?php if ($this->mdl_settings->setting('smtp_security') == 'ssl') { ?>selected="selected"<?php } ?>><?php echo lang('smtp_ssl'); ?></option>
                    <option value="tls" <?php if ($this->mdl_settings->setting('smtp_security') == 'tls') { ?>selected="selected"<?php } ?>><?php echo lang('smtp_tls'); ?></option>
                </select>
            </div>
        </div>
    </div>

</div>