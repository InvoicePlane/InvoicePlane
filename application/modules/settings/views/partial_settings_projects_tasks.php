<script type="text/javascript">

</script>

<div class="tab-info">

    <div class="form-group">
        <label for="settings[default_hourly_rate]" class="control-label">
            <?php echo lang('default_hourly_rate'); ?>
        </label>

        <p class="help-block"><?php echo lang('project_help'); ?></p>
        <input type="text" name="settings[default_hourly_rate]" class="input-sm form-control"
               value="<?php echo $this->mdl_settings->setting('default_hourly_rate'); ?>">
    </div>
</div>