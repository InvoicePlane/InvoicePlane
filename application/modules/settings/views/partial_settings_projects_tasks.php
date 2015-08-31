<div class="tab-info">

    <div class="form-group">
        <label for="settings[default_hourly_rate]" class="control-label">
            <?php echo trans('default_hourly_rate'); ?>
        </label>
        <input type="text" name="settings[default_hourly_rate]" id="settings[default_hourly_rate]"
               class="input-sm form-control" value="<?php echo $this->mdl_settings->setting('default_hourly_rate'); ?>">
        <p class="help-block"><?php echo trans('project_help'); ?></p>
    </div>
</div>
