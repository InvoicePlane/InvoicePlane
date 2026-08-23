<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('einvoice_providers'); ?></h1>
    <div class="headerbar-item pull-right">
        <a href="<?php echo site_url('integrations/operations'); ?>" class="btn btn-sm btn-default">
            <i class="fa fa-heartbeat"></i> Operations
        </a>

        <a href="<?php echo site_url('integrations/incoming'); ?>" class="btn btn-sm btn-default">
            <i class="fa fa-inbox"></i> <?php _trans('incoming_invoices'); ?>
        </a>

        <a href="<?php echo site_url('integrations/events'); ?>" class="btn btn-sm btn-default">
            <i class="fa fa-history"></i> <?php _trans('einvoice_events'); ?>
        </a>
    </div>
</div>

<div id="content" class="table-content">
    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?php _trans('provider'); ?></th>
                <th><?php _trans('label'); ?></th>
                <th><?php _trans('status'); ?></th>
                <th>Last synchronization</th>
                <th><?php _trans('actions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($providers as $provider) : ?>
                <tr>
                    <td><?php _htmlsc($provider['merchant_type']); ?></td>
                    <td><?php _htmlsc($provider['label']); ?></td>
                    <td>
                        <?php echo (int) $provider['enabled'] === 1 ? trans('enabled') : trans('disabled'); ?>
                    </td>
                    <td>
                        <?php $lastRun = $latest_sync_runs[(int) $provider['id']] ?? null; ?>
                        <?php if ($lastRun !== null) : ?>
                            <span class="label label-<?php echo $lastRun['status'] === 'success' ? 'success' : ($lastRun['status'] === 'running' ? 'info' : 'warning'); ?>">
                                <?php _htmlsc($lastRun['status']); ?>
                            </span>
                            <?php _htmlsc($lastRun['started_at']); ?>
                            <small class="text-muted" title="Correlation ID">
                                <?php _htmlsc(mb_substr($lastRun['correlation_id'], 0, 8)); ?>
                            </small>
                        <?php else : ?>
                            <span class="text-muted">Never</span>
                        <?php endif; ?>
                    </td>
                    <td>
                       <a href="<?php echo site_url('integrations/settings/edit/' . $provider['id']); ?>"
                          class="btn btn-sm btn-default">
                          <?php _trans('edit'); ?>
                       </a>
                       <form method="post"
                             action="<?php echo site_url('integrations/sync/run/' . (int) $provider['id']); ?>"
                             style="display: inline;">
                           <?php _csrf_field(); ?>
                           <button type="submit" class="btn btn-sm btn-primary">
                               <i class="fa fa-refresh"></i>
                               <?php _trans('sync'); ?>
                           </button>
                       </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
