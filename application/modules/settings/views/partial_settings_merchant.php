<div class="tab-info">

    <div class="form-group">
        <label for="settings[merchant_enabled]" class="control-label">
            <?php echo trans('merchant_enable'); ?>
        </label>
        <select name="settings[merchant_enabled]" class=" form-control simple-select">
            <option value="0"><?php echo trans('no'); ?></option>
            <option value="1" <?php check_select(get_setting('merchant_enabled'), '1'); ?>>
                <?php echo trans('yes'); ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[merchant_driver]" class="control-label">
            <?php echo trans('merchant_driver'); ?>
        </label>
        <select name="settings[merchant_driver]" class=" form-control simple-select">
            <option value=""><?php echo trans('none'); ?></option>
            <?php foreach ($merchant_drivers as $merchant_driver) { ?>
                <option value="<?php echo $merchant_driver; ?>"
                    <?php check_select(get_setting('merchant_driver'), $merchant_driver); ?>>
                    <?php echo ucwords(str_replace('_', ' ', $merchant_driver)); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[merchant_test_mode]" class="control-label">
            <?php echo trans('merchant_test_mode'); ?>
        </label>
        <select name="settings[merchant_test_mode]" class=" form-control simple-select">
            <option value="0"><?php echo trans('no'); ?></option>
            <option value="1" <?php check_select(get_setting('merchant_test_mode'), '1'); ?>>
                <?php echo trans('yes'); ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[merchant_username]" class="control-label">
            <?php echo trans('username'); ?>
        </label>
        <input type="text" name="settings[merchant_username]" class=" form-control"
               value="<?php echo get_setting('merchant_username'); ?>">
    </div>

    <div class="form-group">
        <label for="settings[merchant_password]" class="control-label">
            <?php echo trans('password'); ?>
        </label>
        <input type="password" name="settings[merchant_password]" class=" form-control"
               value="">
    </div>

    <div class="form-group">
        <label for="settings[merchant_signature]" class="control-label">
            <?php echo trans('merchant_signature'); ?>
        </label>
        <input type="text" name="settings[merchant_signature]" class=" form-control"
               value="<?php echo get_setting('merchant_signature'); ?>">
    </div>

    <div class="form-group">
        <label for="settings[merchant_currency_code]" class="control-label">
            <?php echo trans('merchant_currency_code'); ?>
        </label>
        <select name="settings[merchant_currency_code]" class=" form-control simple-select">
            <option value=""><?php echo trans('none'); ?></option>
            <?php foreach ($merchant_currency_codes as $val => $key) { ?>
                <option value="<?php echo $val; ?>"
                    <?php check_select(get_setting('merchant_currency_code'), $val); ?>>
                    <?php echo $val; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[online_payment_method]" class="control-label">
            <?php echo trans('online_payment_method'); ?>
        </label>
        <select name="settings[online_payment_method]" class=" form-control simple-select">
            <option value=""><?php echo trans('none'); ?></option>
            <?php foreach ($payment_methods as $payment_method) { ?>
                <option value="<?php echo $payment_method->payment_method_id; ?>"
                    <?php check_select(get_setting('online_payment_method'), $payment_method->payment_method_id); ?>>
                    <?php echo $payment_method->payment_method_name; ?>
                </option>
            <?php } ?>
        </select>
    </div>

</div>
