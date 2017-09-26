<div class="col-xs-12 col-md-8 col-md-offset-2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('warehouses'); ?>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="form-group">
                        <label for="settings[warehouses_enabled]">
                            <?php _trans('enable_warehouses'); ?>
                        </label>
                        <select name="settings[warehouses_enabled]" class="form-control simple-select"
                                id="settings[warehouses_enabled]">
                            <option value="0">
                                <?php _trans('no'); ?>
                            </option>
                            <option value="1" <?php check_select(get_setting('warehouses_enabled'), '1'); ?>>
                                <?php _trans('yes'); ?>
                            </option>
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
