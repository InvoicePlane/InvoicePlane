<div class="tab-info">

    <div class="form-group">
        <label for="settings[merchant_enabled]" class="control-label">
            <?php echo lang('merchant_enable'); ?>
        </label>
        <select name="settings[merchant_enabled]" class="input-sm form-control">
            <option value="0"><?php echo lang('no'); ?></option>
            <option value="1"
                    <?php if ($this->mdl_settings->setting('merchant_enabled') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[merchant_driver]" class="control-label">
            <?php echo lang('merchant_driver'); ?>
        </label>
        <select name="settings[merchant_driver]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($merchant_drivers as $merchant_driver) { ?>
                <option value="<?php echo $merchant_driver; ?>"
                        <?php if ($this->mdl_settings->setting('merchant_driver') == $merchant_driver) { ?>selected="selected"<?php } ?>><?php echo ucwords(str_replace('_', ' ', $merchant_driver)); ?></option>
            <?php } ?>
        </select>
    </div>


    <div id="gateway_settings">

    </div>

</div>