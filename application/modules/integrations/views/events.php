<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('einvoice_events'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php foreach ($clients as $client) : ?>
            <form method="post"
                  action="<?php echo site_url('integrations/events/sync/' . (int) $client['id']); ?>"
                  style="display: inline;">
                <?php _csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-refresh"></i>
                    <?php _trans('sync'); ?> <?php _htmlsc($client['label']); ?>
                </button>
            </form>
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
                <th><?php _trans('status'); ?></th>
                <th><?php _trans('message'); ?></th>
                <th><?php _trans('http_code'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($events as $row) : ?>
                <tr>
                    <td><?php _htmlsc($row['created_at']); ?></td>
                    <td><?php _htmlsc($row['merchant_client_label'] ?? $row['merchant_response_driver'] ?? $row['merchant_client_id'] ?? ''); ?></td>
                    <td><?php _htmlsc($row['status'] ?? ''); ?></td>
                    <td><?php _htmlsc($row['merchant_response'] ?? ''); ?></td>
                    <td><?php _htmlsc($row['http_code'] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
