<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('einvoice'); ?> <?php _trans('providers'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-default btn-sm" href="<?php echo site_url('einvoice/incoming'); ?>">
            <i class="fa fa-inbox"></i> <?php _trans('incoming_invoices'); ?>
        </a>

        <a class="btn btn-default btn-sm" href="<?php echo site_url('einvoice/events'); ?>">
            <i class="fa fa-history"></i> <?php _trans('einvoice_events'); ?>
        </a>
    </div>
</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th><?php _trans('provider'); ?></th>
                    <th><?php _trans('label'); ?></th>
                    <th><?php _trans('status'); ?></th>
                    <th><?php _trans('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($providers as $provider) : ?>
                <tr>
                    <td><?php echo htmlsc($provider['merchant_type']); ?></td>
                    <td><?php echo htmlsc($provider['label']); ?></td>
                    <td>
                        <?php echo (int) $provider['enabled'] === 1 ? trans('enabled') : trans('disabled'); ?>
                    </td>
                    <td>
                       <a href="<?php echo site_url('einvoice/settings/edit/' . $provider['id']); ?>"
                          class="btn btn-sm btn-default">
                          <?php _trans('edit'); ?>
                       </a>
                       <a href="<?php echo site_url('einvoice/sync/' . $provider['id']); ?>"
                          class="btn btn-sm btn-primary">
                          <i class="fa fa-refresh"></i>
                          <?php _trans('sync'); ?>
                       </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
