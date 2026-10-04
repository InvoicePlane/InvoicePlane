<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('operations'); ?></h1>
    <div class="headerbar-item pull-right">
        <a href="<?php echo site_url('integrations/settings'); ?>" class="btn btn-sm btn-default">
            <?php _trans('settings'); ?>
        </a>
    </div>
</div>

<div id="content" class="table-content">
    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th><?php _trans('started'); ?></th>
                <th><?php _trans('provider'); ?></th>
                <th><?php _trans('trigger_scope'); ?></th>
                <th><?php _trans('status'); ?></th>
                <th><?php _trans('incoming'); ?></th>
                <th><?php _trans('events'); ?></th>
                <th><?php _trans('attempts'); ?></th>
                <th><?php _trans('duration'); ?></th>
                <th><?php _trans('correlation_id'); ?></th>
                <th><?php _trans('error'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($runs === []) : ?>
                <tr>
                    <td colspan="10" class="text-center text-muted"><?php _trans('no_synchronization_runs'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($runs as $run) : ?>
                    <tr>
                        <td><?php _htmlsc($run['started_at']); ?></td>
                        <td><?php _htmlsc($run['label'] ?: $run['merchant_type'] ?: $run['merchant_client_id']); ?></td>
                        <td><?php _htmlsc($run['trigger_type'] . ' / ' . $run['sync_scope']); ?></td>
                        <td>
                            <span class="label label-<?php echo $run['status'] === 'success' ? 'success' : ($run['status'] === 'running' ? 'info' : 'warning'); ?>">
                                <?php _htmlsc($run['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php _htmlsc(sprintf(
                                trans('received_archived_failed'),
                                $run['incoming_received'],
                                $run['incoming_archived'],
                                $run['incoming_failed']
                            )); ?>
                        </td>
                        <td>
                            <?php _htmlsc(sprintf(
                                trans('received_created'),
                                $run['events_received'],
                                $run['events_created']
                            )); ?>
                        </td>
                        <td><?php _htmlsc($run['attempt_count']); ?></td>
                        <td><?php _htmlsc($run['duration_ms'] === null ? '—' : $run['duration_ms'] . ' ms'); ?></td>
                        <td><code><?php _htmlsc($run['correlation_id']); ?></code></td>
                        <td><?php _htmlsc($run['error_summary'] ?: '—'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
