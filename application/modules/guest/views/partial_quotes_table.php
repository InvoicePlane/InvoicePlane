<div class="table-responsive">
    <table class="table table-hover table-striped no-margin">

        <thead>
        <tr>
            <th><?php _trans('quote'); ?></th>
            <th><?php _trans('created'); ?></th>
            <th><?php _trans('due_date'); ?></th>
            <th><?php _trans('client_name'); ?></th>
            <th><?php _trans('amount'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
<?php
foreach ($quotes as $quote) {
?>
            <tr>
                <td>
                    <a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                       title="<?php _trans('edit'); ?>">
                        <?php echo $quote->quote_number; ?>
                    </a>
<?php
    if ($quote->quote_status_id == 4) {
?>
                    <span class="text-success"><?php _trans('approved'); ?></span>
<?php
    } elseif ($quote->quote_status_id == 5) {
?>
                    <span class="text-danger"><?php _trans('rejected'); ?></span>
<?php
    }
?>
                </td>
                <td><?php echo date_from_mysql($quote->quote_date_created); ?></td>
                <td><?php echo date_from_mysql($quote->quote_date_expires); ?></td>
                <td><?php _htmlsc($quote->client_name); ?></td>
                <td><?php echo format_currency($quote->quote_total); ?></td>
                <td>
                    <div class="options btn-group btn-group-sm">
                        <a class="btn btn-default" href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>">
                            <i class="fa fa-eye"></i> <?php _trans('view'); ?>
                        </a>
                        <a class="btn btn-default" target="_blank" href="<?php echo site_url('guest/quotes/generate_pdf/' . $quote->quote_id); ?>">
                            <i class="fa fa-print"></i> <?php _trans('pdf'); ?>
                        </a>
<?php
    if (in_array($quote->quote_status_id, [2, 3])) {
?>
                        <a class="btn btn-success" href="<?php echo site_url('guest/quotes/approve/' . $quote->quote_id); ?>">
                            <i class="fa fa-check"></i> <?php _trans('approve'); ?>
                        </a>
                        <a class="btn btn-danger" href="<?php echo site_url('guest/quotes/reject/' . $quote->quote_id); ?>">
                            <i class="fa fa-ban"></i> <?php _trans('reject'); ?>
                        </a>
<?php
    }
?>
                    </div>
                </td>
            </tr>
<?php
} // End foreach
?>
        </tbody>

    </table>
</div>