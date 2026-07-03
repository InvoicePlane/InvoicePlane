<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('einvoice'); ?></h1>
</div>

<div class="content">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php _trans('providers'); ?>
        </div>

        <div class="panel-body">
            <a href="<?php echo site_url('integrations/incoming'); ?>" class="btn btn-default">
                <i class="fa fa-inbox"></i> <?php _trans('incoming_invoices'); ?>
            </a>

            <a href="<?php echo site_url('integrations/events'); ?>" class="btn btn-default">
                <i class="fa fa-history"></i> <?php _trans('einvoice_events'); ?>
            </a>
        </div>

        <table class="table table-striped">
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
                       <a href="<?php echo site_url('integrations/settings/edit/' . $provider['id']); ?>"
                          class="btn btn-sm btn-default">
                          <?php _trans('edit'); ?>
                       </a>
                       <a href="<?php echo site_url('integrations/sync/' . $provider['id']); ?>"
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
