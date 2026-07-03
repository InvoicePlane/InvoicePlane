<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('einvoice_provider'); ?></h1>
</div>

<div class="content">
    <form method="post" action="<?php echo site_url('integrations/settings/save/' . $provider['id']); ?>">

        <input type="hidden"
               name="<?php echo $this->security->get_csrf_token_name(); ?>"
               value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('einvoice_provider_settings'); ?>
                    </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <label for="label"><?php _trans('label'); ?></label>
                            <input type="text" name="label" id="label" class="form-control"
                                   value="<?php echo htmlsc($provider['label']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="auth_type"><?php _trans('auth_type'); ?></label>
                            <input type="text" name="auth_type" id="auth_type" class="form-control"
                                   value="<?php echo htmlsc($provider['auth_type']); ?>">
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="enabled" value="1"
                                    <?php echo (int) $provider['enabled'] === 1 ? 'checked' : ''; ?>>
                                <?php _trans('enabled'); ?>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        SuperPDP API
                    </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <label for="client_id"><?php _trans('client_id'); ?></label>
                            <input type="text" name="client_id" id="client_id" class="form-control"
                                   value="<?php echo htmlsc($settings['client_id'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="client_secret"><?php _trans('client_secret'); ?></label>
                            <input type="password" name="client_secret" id="client_secret" class="form-control"
                                   value="<?php echo htmlsc($settings['client_secret'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="token_url"><?php _trans('token_url'); ?></label>
                            <input type="text" name="token_url" id="token_url" class="form-control"
                                   placeholder="https://api.superpdp.tech/oauth2/token"
                                   value="<?php echo htmlsc($settings['token_url'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="api_base_url"><?php _trans('api_base_url'); ?></label>
                            <input type="text" name="api_base_url" id="api_base_url" class="form-control"
                                   placeholder="https://api.superpdp.tech"
                                   value="<?php echo htmlsc($settings['api_base_url'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="invoice_endpoint"><?php _trans('invoice_endpoint'); ?></label>
                            <input type="text" name="invoice_endpoint" id="invoice_endpoint" class="form-control"
                                   placeholder="/v1.beta/invoices"
                                   value="<?php echo htmlsc($settings['invoice_endpoint'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="invoice_status_endpoint"><?php _trans('invoice_status_endpoint'); ?></label>
                            <input type="text" name="invoice_status_endpoint" id="invoice_status_endpoint" class="form-control"
                                   placeholder="/v1.beta/invoices/{id}"
                                   value="<?php echo htmlsc($settings['invoice_status_endpoint'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="incoming_invoices_endpoint"><?php _trans('incoming_invoices_endpoint'); ?></label>
                            <input type="text" name="incoming_invoices_endpoint" id="incoming_invoices_endpoint" class="form-control"
                                   placeholder="/v1.beta/invoices"
                                   value="<?php echo htmlsc($settings['incoming_invoices_endpoint'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="invoice_events_endpoint"><?php _trans('invoice_events_endpoint'); ?></label>
                            <input type="text" name="invoice_events_endpoint" id="invoice_events_endpoint" class="form-control"
                                   placeholder="/v1.beta/invoice_events"
                                   value="<?php echo htmlsc($settings['invoice_events_endpoint'] ?? ''); ?>">
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="disable_pre_check" value="1"
                                    <?php echo ! empty($settings['disable_pre_check']) ? 'checked' : ''; ?>>
                                <?php _trans('disable_pre_check'); ?>
                            </label>
                        </div>

                    </div>

                    <div class="panel-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check"></i> <?php _trans('save'); ?>
                        </button>

                        <a href="<?php echo site_url('integrations/settings'); ?>" class="btn btn-default">
                            <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </form>
</div>
