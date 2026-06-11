<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-exchange"></i> Facturation electronique - PA/PDP</h3>
    </div>
    <div class="panel-body">
        <div class="btn-group">
            <a class="btn btn-primary" href="<?php echo site_url('pdp/settings'); ?>">
                <i class="fa fa-cog"></i> Backend / Configuration
            </a>
            <a class="btn btn-default" href="<?php echo site_url('pdp/receive'); ?>">
                <i class="fa fa-download"></i> Recuperer les factures fournisseurs
            </a>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Configuration active</h3>
    </div>
    <table class="table table-bordered table-striped no-margin">
        <tr><th style="width:220px;">Backend</th><td><?php echo html_escape($settings['provider'] ?? 'demo'); ?></td></tr>
        <tr><th>API</th><td><?php echo html_escape($settings['api_url'] ?? ''); ?></td></tr>
        <tr><th>Authentification</th><td><?php echo html_escape($settings['auth_type'] ?? 'none'); ?></td></tr>
        <tr><th>Active</th><td><?php echo !empty($settings['enabled']) ? '<span class="label label-success">Oui</span>' : '<span class="label label-default">Non</span>'; ?></td></tr>
    </table>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Dernieres transmissions</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover no-margin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Facture</th>
                    <th>Provider</th>
                    <th>Identifiant externe</th>
                    <th>Statut</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($transmissions)): ?>
                <tr><td colspan="8" class="text-muted">Aucune transmission pour le moment.</td></tr>
            <?php else: ?>
                <?php foreach ($transmissions as $row): ?>
                    <tr>
                        <td><?php echo (int) $row['id']; ?></td>
                        <td><?php echo (int) $row['invoice_id']; ?></td>
                        <td><?php echo html_escape($row['provider']); ?></td>
                        <td><?php echo html_escape($row['external_id'] ?? ''); ?></td>
                        <td><span class="label label-info"><?php echo html_escape($row['status']); ?></span></td>
                        <td><?php echo html_escape($row['message'] ?? ''); ?></td>
                        <td><?php echo html_escape($row['updated_at'] ?? $row['created_at']); ?></td>
                        <td>
                            <a class="btn btn-xs btn-default" href="<?php echo site_url('pdp/invoice/' . (int) $row['invoice_id']); ?>">Voir</a>
                            <?php if (!empty($row['external_id'])): ?>
                                <a class="btn btn-xs btn-default" href="<?php echo site_url('pdp/status/' . (int) $row['invoice_id']); ?>">Statut</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
