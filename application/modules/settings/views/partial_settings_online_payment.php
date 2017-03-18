<script>
    $(function () {
        var online_payment_select = $('#online-payment-select');
        online_payment_select.select2().on('change', function () {
            var driver = online_payment_select.val();
            $('.gateway-settings:not(.active-gateway)').addClass('hidden');
            $('#gateway-settings-' + driver).removeClass('hidden');
        });
    });
</script>

<div class="tab-info">

    <div class="form-group">
        <label for="online-payment-select" class="control-label">
            <?php echo trans('online_payment'); ?>
        </label>
        <select id="online-payment-select" class=" form-control">
            <option value=""><?php echo trans('none'); ?></option>
            <?php foreach ($gateway_drivers as $driver) {
                $d = strtolower($driver);
                ?>
                <option value="<?php echo $d; ?>">
                    <?php echo ucwords(str_replace('_', ' ', $driver)); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <?php
    $this->load->library('encrypt');
    foreach ($gateway_drivers as $driver) :
        $d = strtolower($driver);
        ?>
        <div id="gateway-settings-<?php echo $d; ?>"
             class="gateway-settings panel panel-default <?php echo get_setting('gateway_' . $d) == 'on' ? 'active-gateway' : 'hidden'; ?>">

            <div class="panel-heading">
                <?php echo ucwords(str_replace('_', ' ', $driver)); ?>
                <div class="pull-right">
                    <div class="checkbox no-margin">
                        <label>
                            <input type="checkbox" name="settings[gateway_<?php echo $d; ?>]"
                                <?php check_select(get_setting('gateway_' . $d), 'on', '==', true) ?>>
                            <?php echo trans('enabled'); ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="panel-body small">

                <div class="form-group">
                    <label for="settings[gateway_username_<?php echo $d; ?>]" class="control-label">
                        <?php echo trans('username') . ' / ' . trans('online_payment_api_key'); ?>
                    </label>
                    <input type="text" name="settings[gateway_username_<?php echo $d; ?>]"
                           class="input-sm form-control"
                           value="<?php echo $this->mdl_settings->setting('gateway_username_' . $d); ?>">
                </div>

                <div class="form-group">
                    <label for="settings[gateway_password_<?php echo $d; ?>]" class="control-label">
                        <?php echo trans('password') . ' / ' . trans('online_payment_secret_key'); ?>
                    </label>
                    <input type="password" name="settings[gateway_password_<?php echo $d; ?>]"
                           class="input-sm form-control"
                           value="<?php echo $this->encrypt->decode($this->mdl_settings->setting('gateway_password_' . $d)); ?>">
                </div>

                <div class="form-group">
                    <label for="settings[gateway_currency_<?php echo $d; ?>]" class="control-label">
                        <?php echo trans('currency'); ?>
                    </label>
                    <select name="settings[gateway_currency_<?php echo $d; ?>]"
                            class="input-sm form-control simple-select">
                        <option value=""><?php echo trans('none'); ?></option>
                        <?php foreach ($gateway_currency_codes as $val => $key) { ?>
                            <option value="<?php echo $val; ?>"
                                <?php check_select($this->mdl_settings->setting('gateway_currency_' . $d), $val); ?>>
                                <?php echo $val; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="settings[gateway_payment_method_<?php echo $d; ?>]"
                           class="control-label">
                        <?php echo trans('online_payment_method'); ?>
                    </label>
                    <select name="settings[gateway_payment_method_<?php echo $d; ?>]"
                            class="input-sm form-control simple-select">
                        <option value=""><?php echo trans('none'); ?></option>
                        <?php foreach ($payment_methods as $payment_method) { ?>
                            <option value="<?php echo $payment_method->payment_method_id; ?>"
                                <?php check_select($this->mdl_settings->setting('gateway_payment_method_' . $d), $payment_method->payment_method_id) ?>>
                                <?php echo $payment_method->payment_method_name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

        </div>
    <?php endforeach; ?>

</div>