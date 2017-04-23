<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('projects'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[projects_enabled]">
                            <?php _trans('enable_projects'); ?>
                        </label>
                        <select name="settings[projects_enabled]" class="form-control simple-select"
                                id="settings[projects_enabled]">
                            <option value="0">
                                <?php _trans('no'); ?>
                            </option>
                            <option value="1" <?php check_select(get_setting('projects_enabled'), '1'); ?>>
                                <?php _trans('yes'); ?>
                            </option>
                        </select>
                    </div>

                </div>
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[default_hourly_rate]">
                            <?php _trans('default_hourly_rate'); ?>
                        </label>
                        <div class="input-group">
                            <input type="text" name="settings[default_hourly_rate]" id="settings[default_hourly_rate]"
                                   class="form-control amount"
                                   value="<?php echo get_setting('default_hourly_rate') ? format_amount(get_setting('default_hourly_rate')) : get_setting('default_hourly_rate'); ?>">
                            <span class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></span>
                            <input type="hidden" name="settings[default_hourly_rate_field_is_amount]" value="1">
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
