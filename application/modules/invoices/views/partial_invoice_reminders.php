<?php if ( ! empty($invoice_reminders)) { ?>
    <div class="row">
        <div class="col-xs-12">

            <hr>

            <div class="panel panel-default">
                <div class="panel-heading"><?php _trans('reminder_history'); ?></div>
                <div class="table-responsive">
                    <table class="table table-condensed table-striped no-margin">
                        <thead>
                        <tr>
                            <th><?php _trans('date'); ?></th>
                            <th><?php _trans('reminder_type'); ?></th>
                            <th><?php _trans('recipient'); ?></th>
                            <th><?php _trans('status'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
<?php foreach ($invoice_reminders as $reminder) { ?>
                        <tr>
                            <td><?php echo date_from_mysql(mb_substr((string) $reminder->reminder_date_sent, 0, 10), true); ?></td>
                            <td>
                                <?php _htmlsc(trans('reminder_type_' . $reminder->reminder_type)); ?>
                                <span class="text-muted">
                                    (<?php echo (int) $reminder->reminder_offset; ?> <?php _trans('days'); ?>)
                                </span>
                            </td>
                            <td><?php _htmlsc((string) $reminder->reminder_email_to); ?></td>
                            <td>
<?php if ($reminder->reminder_status === 'sent') { ?>
                                <span class="label label-success"><?php _trans('reminder_sent'); ?></span>
<?php } elseif ($reminder->reminder_status === 'failed') { ?>
                                <span class="label label-danger"><?php _trans('reminder_failed'); ?></span>
<?php } elseif ($reminder->reminder_status === 'skipped') { ?>
                                <span class="label label-default"><?php _trans('reminder_skipped'); ?></span>
<?php } else { ?>
                                <span class="label label-warning"><?php _trans('reminder_pending'); ?></span>
<?php } ?>
                            </td>
                        </tr>
<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
<?php } ?>
