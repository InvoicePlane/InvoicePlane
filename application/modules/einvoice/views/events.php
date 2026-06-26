<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('einvoice_events'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php foreach ($clients as $client) : ?>
            <a class="btn btn-primary btn-sm" href="<?php echo site_url('einvoice/events/sync/' . $client['id']); ?>">
                <i class="fa fa-refresh"></i>
                <?php _trans('sync'); ?> <?php echo htmlsc($client['label']); ?>
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
                <th><?php _trans('status'); ?></th>
                <th><?php _trans('message'); ?></th>
                <th><?php _trans('http_code'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($events as $row) : ?>
                <tr>
                    <td><?php echo htmlsc($row['created_at']); ?></td>
                    <td><?php echo htmlsc($row['merchant_client_id']); ?></td>
                    <td><?php echo htmlsc($row['status']); ?></td>
                    <td><?php echo htmlsc($row['message']); ?></td>
                    <td><?php echo htmlsc($row['http_code']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
