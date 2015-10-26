<div class="tab-info">

    <?php
    $this->load->library('encrypt');
    foreach ($gateway_drivers as $driver) :
        $d = strtolower($driver);
        ?>

        <div class="col-xs-12 col-md-6">
            <div class="gateway-settings well well-sm">

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="settings[gateway_<?php echo $d; ?>]"
                               class="gateway-toggle" value="1"
                            <?php if ($this->mdl_settings->setting('gateway_' . $d) == 1) echo 'checked'; ?>>
                        <?php echo ucwords(str_replace('_', ' ', $driver)); ?>
                    </label>
                </div>


                
                <div
                    class="gateway-details small <?php if ($this->mdl_settings->setting('gateway_' . $d) != 1) echo 'hidden'; ?>">

                    <div class="form-group">
                        <label for="settings[gateway_username_<?php echo $d; ?>]" class="control-label">
                            <?php echo lang('username'); ?>
                        </label>
                        <input type="text" name="settings[gateway_username_<?php echo $d; ?>]"
                               class="input-sm form-control"
                               value="<?php echo $this->mdl_settings->setting('gateway_username_' . $d); ?>">
                    </div>

                    <div class="form-group">
                        <label for="settings[gateway_password_<?php echo $d; ?>]" class="control-label">
                            <?php echo lang('password'); ?>
                        </label>
                        <input type="password" name="settings[gateway_password_<?php echo $d; ?>]"
                               class="input-sm form-control"
                               value="<?php ;
                               echo $this->encrypt->decode($this->mdl_settings->setting('gateway_password_' . $d)); ?>">
                    </div>

                    <div class="form-group">
                        <label for="settings[gateway_currency_<?php echo $d; ?>]" class="control-label">
                            <?php echo lang('currency'); ?>
                        </label>
                        <select name="settings[gateway_currency_<?php echo $d; ?>]" class="input-sm form-control">
                            <option value=""></option>
                            <?php foreach ($gateway_currency_codes as $val => $key) { ?>
                                <option value="<?php echo $val; ?>"
                                        <?php if ($this->mdl_settings->setting('gateway_currency_' . $d) == $val) { ?>selected="selected"<?php } ?>><?php echo $val; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[gateway_payment_method_<?php echo $d; ?>]" class="control-label">
                            <?php echo lang('online_payment_method'); ?>
                        </label>
                        <select name="settings[gateway_payment_method_<?php echo $d; ?>]" class="input-sm form-control">
                            <option value=""></option>
                            <?php foreach ($payment_methods as $payment_method) { ?>
                                <option value="<?php echo $payment_method->payment_method_id; ?>"
                                        <?php if ($this->mdl_settings->setting('gateway_payment_method_' . $d) == $payment_method->payment_method_id) { ?>selected="selected"<?php } ?>><?php echo $payment_method->payment_method_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

            </div>
        </div>

    <?php endforeach; ?>

    <script>
        $('.gateway-settings').each(function () {
            var toggle = $(this).find('.gateway-toggle'),
                details = $(this).find('.gateway-details');
            toggle.click(function () {
                details.toggleClass('hidden');
            });
        });
    </script>
</div>