<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('incoming_invoices'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php foreach ($clients as $client) : ?>
            <a href="<?php echo site_url('integrations/incoming/sync/' . $client['id']); ?>" class="btn btn-primary">
                <i class="fa fa-refresh"></i>
                <?php _trans('sync'); ?> <?php _htmlsc($client['label']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div id="content" class="table-content">
    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?php _trans('date'); ?></th>
                <th><?php _trans('provider'); ?></th>
                <th><?php _trans('peppol_participant_id'); ?></th>
                <th><?php _trans('status'); ?></th>
                <th><?php _trans('message'); ?></th>
                <th><?php _trans('external_id'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($incoming)) : ?>
                <tr>
                    <td colspan="6" class="text-center text-muted"><?php _trans('no_incoming_invoices'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($incoming as $row) : ?>
                    <tr>
                        <td><?php _htmlsc($row['created_at'] ?? $row['merchant_response_date']); ?></td>
                        <td><?php _htmlsc($row['merchant_response_driver']); ?></td>
                        <td>
                            <?php if ( ! empty($row['peppol_participant_id'])) : ?>
                                <?php _htmlsc($row['peppol_participant_id']); ?>
                                <?php if ( ! empty($client_map[$row['peppol_participant_id']])) : ?>
                                    — <a href="<?php echo site_url('clients/view/' . $client_map[$row['peppol_participant_id']]['client_id']); ?>">
                                        <?php _htmlsc($client_map[$row['peppol_participant_id']]['client_name']); ?>
                                    </a>
                                <?php endif; ?>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?php _htmlsc($row['status']); ?></td>
                        <td><?php _htmlsc($row['merchant_response']); ?></td>
                        <td><?php _htmlsc($row['merchant_response_reference']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
