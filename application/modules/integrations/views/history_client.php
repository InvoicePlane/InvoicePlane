<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('integration_history'); ?></h1>

    <div class="headerbar-item pull-right">
        <a href="<?php echo site_url('clients/view/' . $client_id); ?>" class="btn btn-default">
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
                <th><?php _trans('invoice'); ?></th>
                <th><?php _trans('provider'); ?></th>
                <th><?php _trans('status'); ?></th>
                <th><?php _trans('message'); ?></th>
                <th><?php _trans('external_id'); ?></th>
                <th><?php _trans('http_code'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($history)) : ?>
                <tr>
                    <td colspan="7" class="text-center text-muted"><?php _trans('no_send_history'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($history as $row) : ?>
                    <tr>
                        <td><?php _htmlsc($row['created_at'] ?? $row['merchant_response_date']); ?></td>
                        <td>
                            <?php if ( ! empty($row['invoice_id'])) : ?>
                                <a href="<?php echo site_url('invoices/view/' . $row['invoice_id']); ?>">
                                    <?php _htmlsc($row['invoice_number'] ?? '#' . $row['invoice_id']); ?>
                                </a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?php _htmlsc($row['merchant_response_driver']); ?></td>
                        <td>
                            <?php
                            $status = $row['status'] ?? '';
                    $badge          = match(true) {
                        in_array($status, ['sent', 'accepted', 'delivered'], true) => 'success',
                        in_array($status, ['error', 'rejected', 'failed'], true)   => 'danger',
                        default                                                    => 'warning',
                    };
                    ?>
                            <span class="label label-<?php echo $badge; ?>"><?php _htmlsc($status); ?></span>
                        </td>
                        <td><?php _htmlsc($row['merchant_response']); ?></td>
                        <td><?php _htmlsc($row['merchant_response_reference']); ?></td>
                        <td><?php _htmlsc($row['http_code']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
