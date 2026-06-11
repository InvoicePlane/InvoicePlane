<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-paper-plane"></i> Transmission PA/PDP - Facture #<?php echo (int) $invoice_id; ?></h3>
    </div>
    <div class="panel-body">
        <p>
            Cette page transmet le <strong>PDF Factur-X deja genere par InvoicePlane</strong> a la plateforme configuree.
        </p>

        <table class="table table-bordered">
            <tr>
                <th style="width:220px;">Backend actif</th>
                <td><?php echo html_escape($settings['provider'] ?? 'demo'); ?></td>
            </tr>
            <tr>
                <th>Connecteur actif</th>
                <td><?php echo !empty($settings['enabled']) ? '<span class="label label-success">Oui</span>' : '<span class="label label-danger">Non</span>'; ?></td>
            </tr>
            <tr>
                <th>PDF Factur-X trouve</th>
                <td>
                    <?php if (!empty($pdf)): ?>
                        <span class="label label-success">Oui</span>
                        <code><?php echo html_escape($pdf); ?></code>
                    <?php else: ?>
                        <span class="label label-warning">Non</span>
                        Genere d'abord le PDF depuis InvoicePlane.
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="btn-group">
            <a class="btn btn-primary" href="<?php echo site_url('pdp/send/' . (int) $invoice_id); ?>">
                <i class="fa fa-paper-plane"></i> Transmettre PA/PDP
            </a>
            <?php if (!empty($latest['external_id'])): ?>
                <a class="btn btn-default" href="<?php echo site_url('pdp/status/' . (int) $invoice_id); ?>">
                    <i class="fa fa-refresh"></i> Verifier le statut
                </a>
            <?php endif; ?>
            <a class="btn btn-default" href="<?php echo site_url('invoices/view/' . (int) $invoice_id); ?>">
                <i class="fa fa-arrow-left"></i> Retour facture
            </a>
            <a class="btn btn-default" href="<?php echo site_url('pdp/settings'); ?>">
                <i class="fa fa-cog"></i> Backend
            </a>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Dernier statut</h3>
    </div>
    <table class="table table-bordered no-margin">
        <?php if (empty($latest)): ?>
            <tr><td class="text-muted">Aucune transmission pour cette facture.</td></tr>
        <?php else: ?>
            <tr><th style="width:220px;">Transmission</th><td>#<?php echo (int) $latest['id']; ?></td></tr>
            <tr><th>Provider</th><td><?php echo html_escape($latest['provider']); ?></td></tr>
            <tr><th>ID distant</th><td><?php echo html_escape($latest['external_id'] ?? ''); ?></td></tr>
            <?php if (!empty($latest['invoiceplane_external_id'])): ?><tr><th>External ID InvoicePlane</th><td><?php echo html_escape($latest['invoiceplane_external_id']); ?></td></tr><?php endif; ?>
            <?php if (!empty($latest['status_code'])): ?><tr><th>Code statut PA</th><td><?php echo html_escape($latest['status_code']); ?></td></tr><?php endif; ?>
            <?php if (!empty($latest['status_text'])): ?><tr><th>Texte statut PA</th><td><?php echo html_escape($latest['status_text']); ?></td></tr><?php endif; ?>
            <tr><th>Statut</th><td><span class="label label-info"><?php echo html_escape($latest['status']); ?></span></td></tr>
            <tr><th>Message</th><td><?php echo html_escape($latest['message'] ?? ''); ?></td></tr>
            <tr><th>HTTP</th><td><?php echo html_escape($latest['http_code'] ?? ''); ?></td></tr>
            <tr><th>Mis a jour</th><td><?php echo html_escape($latest['updated_at'] ?? ''); ?></td></tr>
        <?php endif; ?>
    </table>
</div>
