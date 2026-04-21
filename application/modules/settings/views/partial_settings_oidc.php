<script>
    $(function () {
        toggle_oidc_settings();

        $('#oidc_enabled').change(function () {
            toggle_oidc_settings();
        });

        function toggle_oidc_settings() {
            if ($('#oidc_enabled').val() === '1') {
                $('#div-oidc-settings').show();
            } else {
                $('#div-oidc-settings').hide();
            }
        }
    });
</script>

<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <?php _trans('oidc_sso'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="oidc_enabled">
                                <?php _trans('oidc_enabled'); ?>
                            </label>
                            <select name="settings[oidc_enabled]" id="oidc_enabled"
                                class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0"><?php _trans('no'); ?></option>
                                <option value="1" <?php check_select(get_setting('oidc_enabled'), '1'); ?>>
                                    <?php _trans('yes'); ?>
                                </option>
                            </select>
                        </div>

                        <div id="div-oidc-settings">
                            <hr>

                            <div class="form-group">
                                <label for="settings[oidc_provider_url]">
                                    <?php _trans('oidc_provider_url'); ?>
                                </label>
                                <input type="url" name="settings[oidc_provider_url]" id="settings[oidc_provider_url]"
                                    class="form-control"
                                    placeholder="https://your-provider.com/realms/your-realm"
                                    value="<?php echo get_setting('oidc_provider_url', '', true); ?>">
                                <p class="help-block"><?php _trans('oidc_provider_url_hint'); ?></p>
                            </div>

                            <div class="form-group">
                                <label for="settings[oidc_client_id]">
                                    <?php _trans('oidc_client_id'); ?>
                                </label>
                                <input type="text" name="settings[oidc_client_id]" id="settings[oidc_client_id]"
                                    class="form-control"
                                    value="<?php echo get_setting('oidc_client_id', '', true); ?>">
                            </div>

                            <div class="form-group">
                                <label for="oidc_client_secret">
                                    <?php _trans('oidc_client_secret'); ?>
                                </label>
                                <input type="password" id="oidc_client_secret" class="form-control"
                                    name="settings[oidc_client_secret]" autocomplete="new-password"
                                    value="">
                                <input type="hidden" name="settings[oidc_client_secret_field_is_password]" value="1">
                                <p class="help-block"><?php _trans('oidc_client_secret_hint'); ?></p>
                            </div>

                            <div class="form-group">
                                <label for="settings[oidc_scopes]">
                                    <?php _trans('oidc_scopes'); ?>
                                </label>
                                <input type="text" name="settings[oidc_scopes]" id="settings[oidc_scopes]"
                                    class="form-control"
                                    placeholder="openid email profile"
                                    value="<?php echo get_setting('oidc_scopes', 'openid email profile', true); ?>">
                                <p class="help-block"><?php _trans('oidc_scopes_hint'); ?></p>
                            </div>

                            <div class="alert alert-info">
                                <strong><?php _trans('oidc_redirect_uri'); ?>:</strong><br>
                                <code><?php echo site_url('sessions/oidc_callback'); ?></code>
                                <p class="help-block" style="margin-top: 10px; margin-bottom: 0;">
                                    <?php _trans('oidc_redirect_uri_hint'); ?>
                                </p>
                            </div>

                            <hr>
                            <h4><?php _trans('oidc_user_settings'); ?></h4>

                            <div class="form-group">
                                <label for="settings[oidc_auto_create_users]">
                                    <?php _trans('oidc_auto_create_users'); ?>
                                </label>
                                <select name="settings[oidc_auto_create_users]" id="settings[oidc_auto_create_users]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="0"><?php _trans('no'); ?></option>
                                    <option value="1" <?php check_select(get_setting('oidc_auto_create_users'), '1'); ?>>
                                        <?php _trans('yes'); ?>
                                    </option>
                                </select>
                                <p class="help-block"><?php _trans('oidc_auto_create_users_hint'); ?></p>
                            </div>

                            <div class="form-group">
                                <label for="settings[oidc_default_user_type]">
                                    <?php _trans('oidc_default_user_type'); ?>
                                </label>
                                <select name="settings[oidc_default_user_type]" id="settings[oidc_default_user_type]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="2" <?php check_select(get_setting('oidc_default_user_type', '2'), '2'); ?>>
                                        <?php _trans('guest_read_only'); ?>
                                    </option>
                                    <option value="1" <?php check_select(get_setting('oidc_default_user_type'), '1'); ?>>
                                        <?php _trans('administrator'); ?>
                                    </option>
                                </select>
                                <p class="help-block"><?php _trans('oidc_default_user_type_hint'); ?></p>
                            </div>

                            <div class="form-group">
                                <label for="settings[oidc_allow_local_login]">
                                    <?php _trans('oidc_allow_local_login'); ?>
                                </label>
                                <select name="settings[oidc_allow_local_login]" id="settings[oidc_allow_local_login]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="1" <?php check_select(get_setting('oidc_allow_local_login', '1'), '1'); ?>>
                                        <?php _trans('yes'); ?>
                                    </option>
                                    <option value="0" <?php check_select(get_setting('oidc_allow_local_login'), '0'); ?>>
                                        <?php _trans('no'); ?>
                                    </option>
                                </select>
                                <p class="help-block"><?php _trans('oidc_allow_local_login_hint'); ?></p>
                            </div>

                            <div class="form-group">
                                <label for="settings[oidc_auto_redirect_login]">
                                    <?php _trans('oidc_auto_redirect_login'); ?>
                                </label>
                                <select name="settings[oidc_auto_redirect_login]" id="settings[oidc_auto_redirect_login]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="0" <?php check_select(get_setting('oidc_auto_redirect_login', '0'), '0'); ?>>
                                        <?php _trans('no'); ?>
                                    </option>
                                    <option value="1" <?php check_select(get_setting('oidc_auto_redirect_login'), '1'); ?>>
                                        <?php _trans('yes'); ?>
                                    </option>
                                </select>
                                <p class="help-block"><?php _trans('oidc_auto_redirect_login_hint'); ?></p>
                            </div>

                            <div class="form-group">
                                <label for="settings[oidc_require_email_verified]">
                                    <?php _trans('oidc_require_email_verified'); ?>
                                </label>
                                <select name="settings[oidc_require_email_verified]" id="settings[oidc_require_email_verified]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="1" <?php check_select(get_setting('oidc_require_email_verified', '1'), '1'); ?>>
                                        <?php _trans('yes'); ?>
                                    </option>
                                    <option value="0" <?php check_select(get_setting('oidc_require_email_verified'), '0'); ?>>
                                        <?php _trans('no'); ?>
                                    </option>
                                </select>
                                <p class="help-block"><?php _trans('oidc_require_email_verified_hint'); ?></p>
                            </div>

                            <hr>
                            <h4><?php _trans('oidc_advanced_settings'); ?></h4>

                            <div class="form-group">
                                <label for="settings[oidc_verify_ssl]">
                                    <?php _trans('oidc_verify_ssl'); ?>
                                </label>
                                <select name="settings[oidc_verify_ssl]" id="settings[oidc_verify_ssl]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="1" <?php check_select(get_setting('oidc_verify_ssl', '1'), '1'); ?>>
                                        <?php _trans('yes'); ?>
                                    </option>
                                    <option value="0" <?php check_select(get_setting('oidc_verify_ssl'), '0'); ?>>
                                        <?php _trans('no'); ?>
                                    </option>
                                </select>
                                <p class="help-block"><?php _trans('oidc_verify_ssl_hint'); ?></p>
                            </div>

                            <div class="form-group">
                                <label for="settings[oidc_button_text]">
                                    <?php _trans('oidc_button_text'); ?>
                                </label>
                                <input type="text" name="settings[oidc_button_text]" id="settings[oidc_button_text]"
                                    class="form-control"
                                    placeholder="<?php _trans('oidc_login'); ?>"
                                    value="<?php echo get_setting('oidc_button_text', '', true); ?>">
                                <p class="help-block"><?php _trans('oidc_button_text_hint'); ?></p>
                            </div>

                            <div class="form-group">
                                <label for="settings[oidc_token_auth_methods]">
                                    <?php _trans('oidc_token_auth_methods'); ?>
                                </label>
                                <input type="text" name="settings[oidc_token_auth_methods]" id="settings[oidc_token_auth_methods]"
                                    class="form-control"
                                    placeholder="client_secret_post,client_secret_basic"
                                    value="<?php echo get_setting('oidc_token_auth_methods', 'client_secret_post,client_secret_basic', true); ?>">
                                <p class="help-block"><?php _trans('oidc_token_auth_methods_hint'); ?></p>
                            </div>

                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <?php _trans('oidc_idp_examples'); ?>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="oidc_idp_provider">
                                        <?php _trans('oidc_idp_provider'); ?>
                                    </label>
                                    <select id="oidc_idp_provider" class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                        <option value="authelia"><?php _trans('oidc_idp_authelia'); ?></option>
                                        <option value="authentik"><?php _trans('oidc_idp_authentik'); ?></option>
                                        <option value="keycloak"><?php _trans('oidc_idp_keycloak'); ?></option>
                                        <option value="azure"><?php _trans('oidc_idp_azure'); ?></option>
                                        <option value="okta"><?php _trans('oidc_idp_okta'); ?></option>
                                        <option value="google"><?php _trans('oidc_idp_google'); ?></option>
                                        <option value="auth0"><?php _trans('oidc_idp_auth0'); ?></option>
                                        <option value="onelogin"><?php _trans('oidc_idp_onelogin'); ?></option>
                                        <option value="zitadel"><?php _trans('oidc_idp_zitadel'); ?></option>
                                    </select>
                                    <p class="help-block"><?php _trans('oidc_idp_provider_hint'); ?></p>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="authelia">
                                    <strong><?php _trans('oidc_idp_authelia'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum client configuration example (Authelia YAML):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="authelia" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="authelia" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>- client_id: invoiceplanetest
  client_name: InvoicePlane
  client_secret: (generated)
  public: false
  consent_mode: implicit
  authorization_policy: one_factor
  redirect_uris:
    - https://invoiceplane.example.com/sessions/oidc_callback
  scopes:
    - openid
    - email
    - profile
  userinfo_signed_response_alg: none
  token_endpoint_auth_method: client_secret_post</code></pre>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="authentik" style="display: none;">
                                    <strong><?php _trans('oidc_idp_authentik'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum configuration (UI fields vary by version):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="authentik" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="authentik" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>Application / Provider
  Client ID: invoiceplanetest
  Client Secret: (generated)
  Redirect URIs:
    - https://invoiceplane.example.com/sessions/oidc_callback
  Scopes:
    - openid
    - email
    - profile
  Token endpoint auth method:
    - client_secret_post (recommended)

InvoicePlane Settings
  Provider URL: https://auth.example.com/application/o/invoiceplane/
  Token auth methods: client_secret_post
  Scopes: openid email profile</code></pre>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="keycloak" style="display: none;">
                                    <strong><?php _trans('oidc_idp_keycloak'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum configuration (UI fields vary by version):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="keycloak" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="keycloak" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>Client Settings
  Client ID: invoiceplanetest
  Client Authentication: On (confidential)
  Standard Flow: On
  Valid Redirect URIs:
    - https://invoiceplane.example.com/sessions/oidc_callback
  Web Origins:
    - https://invoiceplane.example.com (or +)

InvoicePlane Settings
  Provider URL: https://keycloak.example.com/realms/your-realm
  Token auth methods: client_secret_post, client_secret_basic
  Scopes: openid email profile</code></pre>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="azure" style="display: none;">
                                    <strong><?php _trans('oidc_idp_azure'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum configuration (Azure AD App Registration):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="azure" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="azure" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>App Registration
  Client ID: invoiceplanetest (Application ID)
  Client Secret: (generated)
  Redirect URI (Web):
    - https://invoiceplane.example.com/sessions/oidc_callback

InvoicePlane Settings
  Provider URL: https://login.microsoftonline.com/{tenant_id}/v2.0
  Token auth methods: client_secret_post, client_secret_basic
  Scopes: openid email profile</code></pre>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="okta" style="display: none;">
                                    <strong><?php _trans('oidc_idp_okta'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum configuration (Okta OIDC app):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="okta" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="okta" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>Application
  Client ID: invoiceplanetest
  Client Secret: (generated)
  Sign-in redirect URIs:
    - https://invoiceplane.example.com/sessions/oidc_callback

InvoicePlane Settings
  Provider URL: https://dev-123456.okta.com/oauth2/default
  Token auth methods: client_secret_post, client_secret_basic
  Scopes: openid email profile</code></pre>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="google" style="display: none;">
                                    <strong><?php _trans('oidc_idp_google'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum configuration (Google Cloud OAuth client):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="google" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="google" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>OAuth Client
  Client ID: invoiceplanetest
  Client Secret: (generated)
  Authorized redirect URIs:
    - https://invoiceplane.example.com/sessions/oidc_callback

InvoicePlane Settings
  Provider URL: https://accounts.google.com
  Token auth methods: client_secret_post, client_secret_basic
  Scopes: openid email profile</code></pre>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="auth0" style="display: none;">
                                    <strong><?php _trans('oidc_idp_auth0'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum configuration (Auth0 application):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="auth0" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="auth0" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>Application
  Client ID: invoiceplanetest
  Client Secret: (generated)
  Allowed Callback URLs:
    - https://invoiceplane.example.com/sessions/oidc_callback

InvoicePlane Settings
  Provider URL: https://your-tenant.us.auth0.com/
  Token auth methods: client_secret_post, client_secret_basic
  Scopes: openid email profile</code></pre>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="onelogin" style="display: none;">
                                    <strong><?php _trans('oidc_idp_onelogin'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum configuration (OneLogin OIDC app):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="onelogin" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="onelogin" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>Application
  Client ID: invoiceplanetest
  Client Secret: (generated)
  Redirect URIs:
    - https://invoiceplane.example.com/sessions/oidc_callback

InvoicePlane Settings
  Provider URL: https://{your-subdomain}.onelogin.com/oidc/2
  Token auth methods: client_secret_post, client_secret_basic
  Scopes: openid email profile</code></pre>
                                </div>

                                <div class="alert alert-info oidc-idp-example" data-provider="zitadel" style="display: none;">
                                    <strong><?php _trans('oidc_idp_zitadel'); ?></strong>
                                    <p class="help-block" style="margin: 8px 0 10px;">
                                        Minimum configuration (Zitadel application):
                                    </p>
                                    <button type="button" class="btn btn-default btn-xs oidc-copy-example" data-provider="zitadel" style="margin-bottom: 8px;">
                                        <?php _trans('oidc_copy_example'); ?>
                                    </button>
                                    <pre class="pre-scrollable oidc-example-code" data-provider="zitadel" style="margin: 0; font-size: 11px; line-height: 1.35;"><code>Application
  Client ID: invoiceplanetest
  Client Secret: (generated)
  Redirect URIs:
    - https://invoiceplane.example.com/sessions/oidc_callback

InvoicePlane Settings
  Provider URL: https://your-zitadel.example.com
  Token auth methods: client_secret_post, client_secret_basic
  Scopes: openid email profile</code></pre>
                                </div>

                                <div class="alert alert-warning" style="margin-bottom: 0;">
                                    <?php _trans('oidc_idp_examples_note'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
    $(function () {
        $('#oidc_idp_provider').change(function () {
            var provider = $(this).val();
            $('.oidc-idp-example').hide();
            $('.oidc-idp-example[data-provider="' + provider + '"]').show();
        });

        $('.oidc-copy-example').click(function () {
            var provider = $(this).data('provider');
            var code = $('.oidc-example-code[data-provider="' + provider + '"] code').text();
            var $button = $(this);

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(function () {
                    $button.text('<?php _trans('oidc_copied'); ?>');
                    setTimeout(function () {
                        $button.text('<?php _trans('oidc_copy_example'); ?>');
                    }, 1500);
                }).catch(function () {
                    // Fallback if clipboard API fails (e.g., permissions denied)
                    fallbackCopy(code, $button);
                });
            } else {
                fallbackCopy(code, $button);
            }

            function fallbackCopy(text, $btn) {
                var $temp = $('<textarea>');
                $('body').append($temp);
                $temp.val(text).select();
                document.execCommand('copy');
                $temp.remove();
                $btn.text('<?php _trans('oidc_copied'); ?>');
                setTimeout(function () {
                    $btn.text('<?php _trans('oidc_copy_example'); ?>');
                }, 1500);
            }
        });
    });
</script>
