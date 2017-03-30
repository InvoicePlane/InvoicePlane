<div class="table-responsive">
    <table class="table table-striped no-margin">

        <thead>
        <tr>
            <th><?php echo trans('quote'); ?></th>
            <th><?php echo trans('created'); ?></th>
            <th><?php echo trans('due_date'); ?></th>
            <th><?php echo trans('client_name'); ?></th>
            <th><?php echo trans('amount'); ?></th>
            <th><?php echo trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($quotes as $quote) { ?>
            <tr>
                <td>
                    <a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                       title="<?php echo trans('edit'); ?>">
                        <?php echo $quote->quote_number; ?>
                    </a>
                    <?php if ($quote->quote_status_id == 4) : ?>
                        <span class="text-success"><?php echo trans('approved'); ?></span>
                    <?php endif; ?>
                    <?php if ($quote->quote_status_id == 5) : ?>
                        <span class="text-danger"><?php echo trans('rejected'); ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo date_from_mysql($quote->quote_date_created); ?>
                </td>
                <td>
                    <?php echo date_from_mysql($quote->quote_date_expires); ?>
                </td>
                <td>
                    <?php echo $quote->client_name; ?>
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
                                <?php echo trans('approve'); ?>
                            </a>
                            <a href="<?php echo site_url('guest/quotes/reject/' . $quote->quote_id); ?>"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-ban"></i>
                                <?php echo trans('reject'); ?>
                            </a>
                        <?php } ?>
                        <a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                           class="btn btn-default btn-sm">
                            <i class="fa fa-eye"></i>
                            <?php echo trans('view'); ?>
                        </a>
                        <a href="<?php echo site_url('guest/quotes/generate_pdf/' . $quote->quote_id); ?>"
                           class="btn btn-default btn-sm">
                            <i class="fa fa-print"></i>
                            <?php echo trans('pdf'); ?>
                        </a>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>

    </table>
</div>