<?php
$providers = $providers ?? array();
$provider = $settings['provider'] ?? 'superpdp';
$auth = $settings['auth_type'] ?? 'oauth2_client_credentials';

function pdp_setting($settings, $key, $default = '') {
    return isset($settings[$key]) && $settings[$key] !== '' ? $settings[$key] : $default;
}

$apiFields = array(
    array('api_url', 'URL API de base', 'text', 'https://api.superpdp.tech', 'api-field api-common superpdp-field'),
    array('send_endpoint', 'Endpoint envoi facture', 'text', '/v1.beta/invoices', 'api-field api-common superpdp-field'),
    array('status_endpoint', 'Endpoint statut facture', 'text', '/v1.beta/invoices/{id}', 'api-field api-common superpdp-field'),
    array('receive_endpoint', 'Endpoint reception fournisseurs', 'text', '/v1.beta/invoices', 'api-field api-common superpdp-field'),
    array('events_endpoint', 'Endpoint evenements/statuts', 'text', '/v1.beta/invoice_events', 'api-field api-common superpdp-field'),
    array('disable_pre_check', 'Desactiver le pre-check annuaire Peppol', 'checkbox', '0', 'api-field superpdp-field'),
    array('file_field', 'Champ fichier multipart', 'text', 'file', 'api-field generic-field'),
);

$authFields = array(
    array('access_token', 'Access token', 'password', '', 'auth-field bearer-field'),
    array('api_key', 'API key', 'password', '', 'auth-field api-key-field'),
    array('api_key_header', 'Header API key', 'text', 'X-API-Key', 'auth-field api-key-field'),
    array('token_url', 'OAuth token URL', 'text', 'https://api.superpdp.tech/oauth2/token', 'auth-field oauth2-field superpdp-field'),
    array('client_id', 'Client ID', 'text', '', 'auth-field oauth2-field superpdp-field'),
    array('client_secret', 'Client Secret', 'password', '', 'auth-field oauth2-field superpdp-field'),
    array('scope', 'Scope OAuth', 'text', '', 'auth-field oauth2-field'),
);
?>

<?php if ($this->input->get('saved')): ?>
    <div class="alert alert-success">Configuration enregistree.</div>
<?php endif; ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-cog"></i> Configuration PA/PDP</h3>
    </div>
    <div class="panel-body">
        <form method="post" action="<?php echo site_url('pdp/settings'); ?>" class="form-horizontal" id="pdp-settings-form">
            <?php
            if (function_exists('get_csrf_field')) {
                echo get_csrf_field();
            } else {
                $CI =& get_instance();
                if (!empty($CI->security)) {
                    echo '<input type="hidden" name="' . $CI->security->get_csrf_token_name() . '" value="' . $CI->security->get_csrf_hash() . '">';
                }
            }
            ?>

            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#tab-backend" role="tab" data-toggle="tab">Backend</a></li>
                <li><a href="#tab-api" role="tab" data-toggle="tab">API</a></li>
                <li><a href="#tab-auth" role="tab" data-toggle="tab">Authentification</a></li>
                <li><a href="#tab-payload" role="tab" data-toggle="tab">Payload</a></li>
                <li><a href="#tab-help" role="tab" data-toggle="tab">Aide</a></li>
            </ul>

            <div class="tab-content" style="padding-top:20px;">
                <div class="tab-pane active" id="tab-backend">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Activer</label>
                        <div class="col-sm-9">
                            <div class="checkbox">
                                <label><input type="checkbox" name="enabled" value="1" <?php echo !empty($settings['enabled']) ? 'checked' : ''; ?>> Activer le connecteur PA/PDP</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Backend</label>
                        <div class="col-sm-9">
                            <select name="provider" id="pdp_provider" class="form-control">
                                <?php foreach ($providers as $code => $info): ?>
                                    <option value="<?php echo html_escape($code); ?>" <?php echo $provider === $code ? 'selected' : ''; ?>><?php echo html_escape($info['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($providers)): ?>
                                <p class="help-block text-danger">Aucun backend PDP detecte dans <code>application/modules/pdp/libraries/providers</code>.</p>
                            <?php else: ?>
                                <p class="help-block">Les backends sont detectes automatiquement depuis <code>libraries/providers/*Provider.php</code>.</p>
                            <?php endif; ?>
                            <p class="help-block help-superpdp" style="display:none;">SuperPDP utilise OAuth2 client credentials. Tu n'as besoin que du Client ID, du Client Secret, et eventuellement de l'option disable_pre_check pour les tests.</p>
                            <p class="help-block help-demo" style="display:none;">En mode Demo, aucune API externe n'est appelee.</p>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab-api">
                    <div class="alert alert-info superpdp-only" style="display:none;">
                        Les valeurs SuperPDP sont pre-remplies automatiquement. En production, garde <strong>disable_pre_check</strong> decoche.
                    </div>
                    <?php foreach ($apiFields as $f): ?>
                        <div class="form-group <?php echo $f[4]; ?>" data-field="<?php echo $f[0]; ?>">
                            <label class="col-sm-3 control-label"><?php echo html_escape($f[1]); ?></label>
                            <div class="col-sm-9">
                                <?php if ($f[2] === 'checkbox'): ?>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="<?php echo $f[0]; ?>" value="1" <?php echo !empty($settings[$f[0]]) ? 'checked' : ''; ?>> Oui, uniquement pour les tests
                                        </label>
                                    </div>
                                <?php else: ?>
                                    <input class="form-control" type="<?php echo $f[2]; ?>" name="<?php echo $f[0]; ?>" value="<?php echo html_escape(pdp_setting($settings, $f[0], $f[3])); ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="tab-pane" id="tab-auth">
                    <div class="form-group auth-selector-row">
                        <label class="col-sm-3 control-label">Authentification</label>
                        <div class="col-sm-9">
                            <select name="auth_type" id="pdp_auth_type" class="form-control">
                                <option value="none" <?php echo $auth === 'none' ? 'selected' : ''; ?>>Aucune / Demo</option>
                                <option value="bearer" <?php echo $auth === 'bearer' ? 'selected' : ''; ?>>Bearer token</option>
                                <option value="api_key" <?php echo $auth === 'api_key' ? 'selected' : ''; ?>>API key</option>
                                <option value="oauth2_client_credentials" <?php echo $auth === 'oauth2_client_credentials' ? 'selected' : ''; ?>>OAuth2 client credentials</option>
                            </select>
                            <p class="help-block superpdp-only" style="display:none;">Pour SuperPDP, ce champ est force sur OAuth2 client credentials.</p>
                        </div>
                    </div>
                    <?php foreach ($authFields as $f): ?>
                        <div class="form-group <?php echo $f[4]; ?>" data-field="<?php echo $f[0]; ?>">
                            <label class="col-sm-3 control-label"><?php echo html_escape($f[1]); ?></label>
                            <div class="col-sm-9">
                                <input class="form-control" type="<?php echo $f[2]; ?>" name="<?php echo $f[0]; ?>" value="<?php echo html_escape(pdp_setting($settings, $f[0], $f[3])); ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="tab-pane" id="tab-payload">
                    <div class="form-group generic-field">
                        <label class="col-sm-3 control-label">Payload JSON additionnel</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="extra_payload_json" rows="10"><?php echo html_escape($settings['extra_payload_json'] ?? ''); ?></textarea>
                            <p class="help-block">Utilise ce champ uniquement si la plateforme demande des metadonnees supplementaires en plus du PDF Factur-X.</p>
                        </div>
                    </div>
                    <div class="alert alert-info superpdp-only" style="display:none;">
                        SuperPDP recoit directement le PDF Factur-X en <code>application/pdf</code>. Aucun payload JSON n'est necessaire.
                    </div>
                </div>

                <div class="tab-pane" id="tab-help">
                    <div class="alert alert-info">
                        <strong>Flux attendu :</strong> InvoicePlane genere le PDF Factur-X, puis ce module transmet ce PDF a la PA/PDP.
                    </div>
                    <ol>
                        <li>Active la facturation electronique sur le client.</li>
                        <li>Genere le PDF de la facture dans InvoicePlane.</li>
                        <li>Verifie le PDF avec <code>pdfdetach -list Facture.pdf</code> : tu dois voir <code>factur-x.xml</code>.</li>
                        <li>Depuis la facture, clique sur <strong>Transmettre PA/PDP</strong>.</li>
                        <li>SuperPDP attend un PDF Factur-X complet. Si l'API retourne une erreur comme <code>Seller.ElectronicAddress</code>, corrige les champs e-facture dans InvoicePlane puis regenere le PDF.</li>
                    </ol>
                </div>
            </div>

            <hr>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Enregistrer</button>
                    <a href="<?php echo site_url('pdp'); ?>" class="btn btn-default">Retour</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    function setIfEmpty(selector, value) {
        var el = document.querySelector(selector);
        if (el && !el.value) {
            el.value = value;
        }
    }

    function setValue(selector, value) {
        var el = document.querySelector(selector);
        if (el) {
            el.value = value;
        }
    }

    function showElements(selector, show) {
        var nodes = document.querySelectorAll(selector);
        for (var i = 0; i < nodes.length; i++) {
            nodes[i].style.display = show ? '' : 'none';
        }
    }

    function updatePdpFields() {
        var provider = document.getElementById('pdp_provider').value;
        var auth = document.getElementById('pdp_auth_type').value;
        var isSuperPdp = provider === 'superpdp';
        var isDemo = false;

        if (isSuperPdp) {
            setValue('select[name="auth_type"]', 'oauth2_client_credentials');
            auth = 'oauth2_client_credentials';
            setIfEmpty('input[name="api_url"]', 'https://api.superpdp.tech');
            setIfEmpty('input[name="token_url"]', 'https://api.superpdp.tech/oauth2/token');
            setIfEmpty('input[name="send_endpoint"]', '/v1.beta/invoices');
            setIfEmpty('input[name="status_endpoint"]', '/v1.beta/invoices/{id}');
            setIfEmpty('input[name="receive_endpoint"]', '/v1.beta/invoices');
            setIfEmpty('input[name="events_endpoint"]', '/v1.beta/invoice_events');
        }

        showElements('.auth-field', false);
        showElements('.api-field', !isDemo);
        showElements('.generic-field', false);
        showElements('.superpdp-only', isSuperPdp);
        showElements('.help-superpdp', isSuperPdp);
        showElements('.help-demo', isDemo);

        if (isSuperPdp) {
            showElements('.superpdp-field', true);
            showElements('.oauth2-field.superpdp-field', true);
            showElements('.bearer-field', false);
            showElements('.api-key-field', false);
            showElements('.generic-field', false);
            return;
        }

        if (!isDemo) {
            showElements('.api-common', true);
            showElements('.generic-field', true);
        }

        if (auth === 'bearer') {
            showElements('.bearer-field', true);
        } else if (auth === 'api_key') {
            showElements('.api-key-field', true);
        } else if (auth === 'oauth2_client_credentials') {
            showElements('.oauth2-field', true);
        }
    }

    document.getElementById('pdp_provider').addEventListener('change', updatePdpFields);
    document.getElementById('pdp_auth_type').addEventListener('change', updatePdpFields);
    updatePdpFields();
})();
</script>
