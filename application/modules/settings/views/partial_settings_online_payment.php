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
            <?php foreach ($gateway_drivers as $driver => $fields) {
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
    foreach ($gateway_drivers as $driver => $fields) :
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

                <?php foreach ($fields as $key => $setting) { ?>
                    <?php if ($setting['type'] == 'checkbox') : ?>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="settings[gateway_<?php echo $d; ?>_<?php echo $key ?>]"
                                    <?php check_select(get_setting('gateway_' . $d . '_' . $key), 'on', '==', true) ?>>
                                <?php echo trans('online_payment_' . $key, '', $setting['label']); ?>
                            </label>
                        </div>

                    <?php else : ?>

                        <div class="form-group">
                            <label for="settings[gateway_<?php echo $d; ?>_<?php echo $key ?>]" class="control-label">
                                <?php echo trans('online_payment_' . $key, '', $setting['label']); ?>
                            </label>
                            <input type="<?php echo $setting['type']; ?>" class="input-sm form-control"
                                   name="settings[gateway_<?php echo $d; ?>_<?php echo $key ?>]"
                                <?php if ($setting['type'] == 'password') : ?>
                                    value="<?php echo $this->encrypt->decode(get_setting('gateway_' . $d . '_' . $key)); ?>"
                                <?php else : ?>
                                    value="<?php echo get_setting('gateway_' . $d . '_' . $key); ?>"
                                <?php endif; ?>
                            >
                            <?php if ($setting['type'] == 'password') : ?>
                                <input type="hidden" value="1"
                                       name="settings[gateway_<?php echo $d . '_' . $key ?>_field_is_password]">
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>
                <?php } ?>

                <hr>

                <div class="form-group">
                    <label for="settings[gateway_<?php echo $d; ?>_currency]" class="control-label">
                        <?php echo trans('currency'); ?>
                    </label>
                    <select name="settings[gateway_<?php echo $d; ?>_currency]"
                            class="input-sm form-control simple-select">
                        <option value=""><?php echo trans('none'); ?></option>
                        <?php foreach ($gateway_currency_codes as $val => $key) { ?>
                            <option value="<?php echo $val; ?>"
                                <?php check_select(get_setting('gateway_' . $d . '_currency'), $val); ?>>
                                <?php echo $val; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="settings[gateway_<?php echo $d; ?>_payment_method]"
                           class="control-label">
                        <?php echo trans('online_payment_method'); ?>
                    </label>
                    <select name="settings[gateway_<?php echo $d; ?>_payment_method]"
                            class="input-sm form-control simple-select">
                        <option value=""><?php echo trans('none'); ?></option>
                        <?php foreach ($payment_methods as $payment_method) { ?>
                            <option value="<?php echo $payment_method->payment_method_id; ?>"
                                <?php check_select(get_setting('gateway_' . $d . '_payment_method'), $payment_method->payment_method_id) ?>>
                                <?php echo $payment_method->payment_method_name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

            </div>

        </div>
    <?php endforeach; ?>

</div>
