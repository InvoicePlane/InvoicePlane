<div class="table-responsive">
    <table class="table table-striped no-margin">

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
        <?php foreach ($quotes as $quote) { ?>
            <tr>
                <td>
                    <a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                       title="<?php _trans('edit'); ?>">
                        <?php echo $quote->quote_number; ?>
                    </a>
                    <?php if ($quote->quote_status_id == 4) : ?>
                        <span class="text-success"><?php _trans('approved'); ?></span>
                    <?php endif; ?>
                    <?php if ($quote->quote_status_id == 5) : ?>
                        <span class="text-danger"><?php _trans('rejected'); ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo date_from_mysql($quote->quote_date_created); ?>
                </td>
                <td>
                    <?php echo date_from_mysql($quote->quote_date_expires); ?>
                </td>
                <td>
                    <?php _htmlsc($quote->client_name); ?>
                </td>
                <td>
                    <?php echo format_currency($quote->quote_total); ?>
                </td>
                <td>
                    <div class="options btn-group btn-group-sm">
                        <?php if (in_array($quote->quote_status_id, array(2, 3))) { ?>
                            <a href="<?php echo site_url('guest/quotes/approve/' . $quote->quote_id); ?>"
                               class="btn btn-success btn-sm">
                                <i class="fa fa-check"></i>
                                <?php _trans('approve'); ?>
                            </a>
                            <a href="<?php echo site_url('guest/quotes/reject/' . $quote->quote_id); ?>"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-ban"></i>
                                <?php _trans('reject'); ?>
                            </a>
                        <?php } ?>
                        <a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                           class="btn btn-default btn-sm">
                            <i class="fa fa-eye"></i>
                            <?php _trans('view'); ?>
                        </a>
                        <a href="<?php echo site_url('guest/quotes/generate_pdf/' . $quote->quote_id); ?>"
                           class="btn btn-default btn-sm">
                            <i class="fa fa-print"></i>
                            <?php _trans('pdf'); ?>
                        </a>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>

    </table>
</div>