<script type="text/javascript">
    $(function()
    {
        $('#btn_generate_cron_key').click(function()
        {
            $.post("<?php echo site_url('settings/ajax/get_cron_key'); ?>", function(data) {
                $('#cron_key').val(data);
            });
        });
    });
	
	// Update check
	window.onload = function() {
		// Get the current version
		var current_version = "<?php echo $current_version; ?>";
		current_version = current_version.replace(/\./g, ''); // Remove the dots from the version
		
		// Get the latest version from updates.invoiceplane.com
		$.getJSON("http://updates.invoiceplane.com", function(data) {
			
			var updatecheck = data.current_version.replace(/\./g, '');
			
			// Compare each versions and replace the placeholder with a download button
			// or info label after 2 seconds
			setTimeout(function() {
				if ( current_version < updatecheck ) {
					$('#updatecheck-loading').addClass('hidden');
					$('#updatecheck-updates-available').removeClass('hidden');
				} else {
					$('#updatecheck-loading').addClass('hidden');
					$('#updatecheck-no-updates').removeClass('hidden');
				}
			}, 2000);
		});
	};
</script>

<div class="tab-info form-horizontal">

	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label class="control-label">
                <?php echo lang('current_version'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
			<div class="input-group">
				<input type="text" class="input-sm form-control"
	                   value="<?php echo $current_version; ?>" readonly="readonly">
					   
				<span id="updatecheck-loading" class="input-group-addon">
					<i class="fa fa-circle-o-notch fa-spin" ></i>  <?php echo lang('checking_for_updates'); ?>
				</span>  
				
				<a href="https://invoiceplane.com/downloads" id="updatecheck-updates-available"
					class="input-group-addon btn btn-success hidden" target="_blank">
					<?php echo lang('updates_available'); ?>
				</a>
				<span id="updatecheck-no-updates" class="input-group-addon hidden" >
					<?php echo lang('no_updates_available'); ?>
				</span>
			</div>
		</div>
	</div>

	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label for="settings[default_language]" class="control-label">
                <?php echo lang('language'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
			<select name="settings[default_language]" class="input-sm form-control">
				<?php foreach ($languages as $language) { ?>
				<option value="<?php echo $language; ?>" <?php if ($this->mdl_settings->setting('default_language') == $language) { ?>selected="selected"<?php } ?>><?php echo ucfirst($language); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
    
	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label for="settings[date_format]" class="control-label">
                <?php echo lang('date_format'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
			<select name="settings[date_format]" class="input-sm form-control">
				<?php foreach ($date_formats as $date_format) { ?>
				<option value="<?php echo $date_format['setting']; ?>" <?php if ($this->mdl_settings->setting('date_format') == $date_format['setting']) { ?>selected="selected"<?php } ?>><?php echo $current_date->format($date_format['setting']); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>

	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label class="control-label">
                <?php echo lang('currency_symbol'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
			<input type="text" name="settings[currency_symbol]" class="input-sm form-control"
                   value="<?php echo $this->mdl_settings->setting('currency_symbol'); ?>">
		</div>
	</div>
	
	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label for="settings[currency_symbol_placement]" class="control-label">
                <?php echo lang('currency_symbol_placement'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
			<select name="settings[currency_symbol_placement]" class="input-sm form-control">
				<option value="before" <?php if ($this->mdl_settings->setting('currency_symbol_placement') == 'before') { ?>selected="selected"<?php } ?>><?php echo lang('before_amount'); ?></option>
				<option value="after" <?php if ($this->mdl_settings->setting('currency_symbol_placement') == 'after') { ?>selected="selected"<?php } ?>><?php echo lang('after_amount'); ?></option>
			</select>
		</div>
	</div>
    
	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label for="settings[thousands_separator]" class="control-label">
                <?php echo lang('thousands_separator'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
			<input type="text" name="settings[thousands_separator]" class="input-sm form-control"
                   value="<?php echo $this->mdl_settings->setting('thousands_separator'); ?>">
		</div>
	</div>
    
	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label for="settings[decimal_point]" class="control-label">
                <?php echo lang('decimal_point'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
			<input type="text" name="settings[decimal_point]" class="input-sm form-control"
                   value="<?php echo $this->mdl_settings->setting('decimal_point'); ?>">
		</div>
	</div>
    
	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label class="control-label">
                <?php echo lang('tax_rate_decimal_places'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
			<select name="settings[tax_rate_decimal_places]" class="input-sm form-control"
                    id="tax_rate_decimal_places">
				<option value="2" <?php if ($this->mdl_settings->setting('tax_rate_decimal_places') == '2') { ?>selected="selected"<?php } ?>>2</option>
				<option value="3" <?php if ($this->mdl_settings->setting('tax_rate_decimal_places') == '3') { ?>selected="selected"<?php } ?>>3</option>
			</select>
		</div>
	</div>
    
	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label for="settings[cron_key]" class="control-label">
                <?php echo lang('cron_key'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="row">
                <div class="col-xs-8 col-sm-9">
                    <input type="text" name="settings[cron_key]" id="cron_key"
                           class="input-sm form-control"
                           value="<?php echo $this->mdl_settings->setting('cron_key'); ?>">
                </div>
                <div class="col-xs-4 col-sm-3">
                    <input id="btn_generate_cron_key" value="<?php echo lang('generate'); ?>"
                           type="button" class="btn btn-primary btn-sm btn-block">
                </div>
            </div>
		</div>
	</div>
    
	<div class="form-group">
        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
		    <label class="control-label">
                <?php echo lang('login_logo'); ?>
            </label>
        </div>
        <div class="col-xs-12 col-sm-6">
            <?php if ($this->mdl_settings->setting('login_logo')) { ?>
            <img src="<?php echo base_url(); ?>uploads/<?php echo $this->mdl_settings->setting('login_logo'); ?>"><br>
            <?php echo anchor('settings/remove_logo/login', 'Remove Logo'); ?><br>
            <?php } ?>
			<input type="file" name="login_logo" size="40" class="form-control control-label"/>
		</div>
	</div>

</div>