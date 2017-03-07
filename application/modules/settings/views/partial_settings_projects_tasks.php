<div class="tab-info">

    <div class="row">
        <div class="col-xs-12 col-md-6">

            <div class="form-group">
                <label for="settings[projects_enabled]" class="control-label">
                    <?php echo trans('enable_projects'); ?>
                </label>
                <select name="settings[projects_enabled]" class="input-sm form-control"
                        id="settings[projects_enabled]">
                    <option value="1"
                            <?php if (get_setting('projects_enabled')) { ?>selected="selected"<?php } ?>>
                        <?php echo trans('yes'); ?>
                    </option>
                    <option value="0"
                            <?php if (!get_setting('projects_enabled')) { ?>selected="selected"<?php } ?>>
                        <?php echo trans('no'); ?>
                    </option>
                </select>
            </div>

        </div>
        <div class="col-xs-12 col-md-6">

            <div class="form-group">
                <label for="settings[default_hourly_rate]" class="control-label">
                    <?php echo trans('default_hourly_rate'); ?>
                </label>
                <div class="input-group">
                    <input type="text" name="settings[default_hourly_rate]" id="settings[default_hourly_rate]"
                           class="input-sm form-control amount"
                           value="<?php echo format_amount(get_setting('default_hourly_rate')); ?>">
                    <span class="input-group-addon">
                <?php echo get_setting('currency_symbol'); ?>
            </span>
                </div>
            </div>

        </div>
    </div>

</div>
