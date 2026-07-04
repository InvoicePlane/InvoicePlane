<div id="headerbar">
    <h1 class="headerbar-title">
        <?php _trans('einvoice_history'); ?> #<?php _htmlsc($invoice_id); ?>
    </h1>

    <div class="headerbar-item pull-right">
        <a href="<?php echo site_url('invoices/view/' . $invoice_id); ?>" class="btn btn-sm btn-default">
            <i class="fa fa-arrow-left"></i> <?php _trans('back'); ?>
        </a>
    </div>
</div>

<div id="content" class="table-content">
    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?php _trans('date'); ?></th>
                <th><?php _trans('direction'); ?></th>
                <th><?php _trans('status'); ?></th>
                <th><?php _trans('message'); ?></th>
                <th><?php _trans('external_id'); ?></th>
                <th><?php _trans('http_code'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($history as $row) : ?>
                <tr>
                    <td><?php _htmlsc($row['created_at']); ?></td>
                    <td><?php _htmlsc($row['direction']); ?></td>
                    <td><?php _htmlsc($row['status']); ?></td>
                    <td><?php _htmlsc($row['merchant_response']); ?></td>
                    <td><?php _htmlsc($row['merchant_response_reference']); ?></td>
                    <td><?php _htmlsc($row['http_code']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
