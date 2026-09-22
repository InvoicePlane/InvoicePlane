<form method="post" action="<?php echo site_url('integrations/settings/save/' . $provider['id']); ?>">
    <?php _csrf_field(); ?>

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('einvoice_provider_settings'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('einvoice_provider'); ?> (<?php _htmlsc($provider['merchant_type']); ?>)
                        <div class="pull-right">
                            <label for="enabled" class="control-label">
                                <?php _trans('enabled'); ?>
                                <input type="checkbox" name="enabled" value="1"
                                    <?php echo (int) $provider['enabled'] === 1 ? 'checked' : ''; ?>>
                            </label>
                        </div>
                    </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <label for="label"><?php _trans('label'); ?></label>
                            <input type="text" name="label" id="label" class="form-control"
                                   value="<?php _htmlsc($provider['label']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="auth_type"><?php _trans('auth_type'); ?></label>
                            <p class="form-control-static"><?php _htmlsc($provider['auth_type']); ?></p>
                        </div>

                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('settings'); ?> (<?php _htmlsc($provider['merchant_type']); ?>)
                    </div>

                    <div class="panel-body">

<?php foreach ($settings_schema as $fieldName => $field) : ?>
    <?php
    $currentValue   = $settings[$fieldName] ?? $field['default'];
    $hasStoredValue = $field['sensitive'] && $currentValue !== null && $currentValue !== '';
    $isRequired     = $field['required'] && ! $hasStoredValue;
    ?>
    <?php if ($field['type'] === 'checkbox') : ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox"
                                       name="<?php _htmlsc($fieldName); ?>"
                                       value="1"<?php echo ! empty($currentValue) ? ' checked' : ''; ?>>
                                <?php _trans($field['label']); ?>
                            </label>
                        </div>
    <?php else : ?>
                        <div class="form-group">
                            <label for="<?php _htmlsc($fieldName); ?>">
                                <?php _trans($field['label']); ?>
                                <?php if ($field['required']) : ?> *<?php endif; ?>
                            </label>
        <?php if ($field['type'] === 'select') : ?>
                            <select name="<?php _htmlsc($fieldName); ?>"
                                    id="<?php _htmlsc($fieldName); ?>"
                                    class="form-control"<?php echo $isRequired ? ' required' : ''; ?>>
            <?php foreach ($field['options'] as $optionValue => $optionLabel) : ?>
                                <option value="<?php _htmlsc($optionValue); ?>"
                                    <?php echo (string) $currentValue === (string) $optionValue ? ' selected' : ''; ?>>
                                    <?php _trans($optionLabel); ?>
                                </option>
            <?php endforeach; ?>
                            </select>
        <?php else : ?>
            <?php
            $inputType   = $field['type'] === 'password' ? 'password' : ($field['type'] === 'url' ? 'url' : 'text');
            $inputValue  = $field['sensitive'] ? '' : (string) $currentValue;
            $placeholder = $hasStoredValue ? trans('leave_blank_to_keep') : $field['placeholder'];
            ?>
                            <input type="<?php _htmlsc($inputType); ?>"
                                   name="<?php _htmlsc($fieldName); ?>"
                                   id="<?php _htmlsc($fieldName); ?>"
                                   class="form-control"
                                   value="<?php _htmlsc($inputValue); ?>"
                                   placeholder="<?php _htmlsc($placeholder); ?>"
                                   <?php echo $field['sensitive'] ? 'autocomplete="new-password"' : ''; ?>
                                   <?php echo $isRequired ? 'required' : ''; ?>>
        <?php endif; ?>
                        </div>
    <?php endif; ?>
<?php endforeach; ?>

                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <button type="button" class="btn btn-default js-test-connection"
                                data-url="<?php echo site_url('integrations/settings/test_connection/' . $provider['id']); ?>"
                                data-msg-running="<?php _htmlsc(trans('einvoice_test_connection_running')); ?>"
                                data-msg-ok="<?php _htmlsc(trans('einvoice_test_connection_ok')); ?>"
                                data-msg-failed="<?php _htmlsc(trans('einvoice_test_connection_failed')); ?>">
                            <i class="fa fa-plug"></i> <?php _trans('einvoice_test_connection'); ?>
                        </button>
                        <span class="js-test-connection-result" role="status"></span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</form>

<script src="<?php _core_asset('js/integration-settings.js'); ?>" defer></script>
