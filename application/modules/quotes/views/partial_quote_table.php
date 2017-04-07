<div class="table-responsive">
    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php _trans('status'); ?></th>
            <th><?php _trans('quote'); ?></th>
            <th><?php _trans('created'); ?></th>
            <th><?php _trans('due_date'); ?></th>
            <th><?php _trans('client_name'); ?></th>
            <th style="text-align: right; padding-right: 25px;"><?php _trans('amount'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php
        $quote_idx = 1;
        $quote_count = count($quotes);
        $quote_list_split = $quote_count > 3 ? $quote_count / 2 : 9999;

        foreach ($quotes as $quote) {
            // Convert the dropdown menu to a dropup if quote is after the invoice split
            $dropup = $quote_idx > $quote_list_split ? true : false;
            ?>
            <tr>
                <td>
                    <span class="label <?php echo $quote_statuses[$quote->quote_status_id]['class']; ?>">
                        <?php echo $quote_statuses[$quote->quote_status_id]['label']; ?>
                    </span>
                </td>
                <td>
                    <a href="<?php echo site_url('quotes/view/' . $quote->quote_id); ?>"
                       title="<?php _trans('edit'); ?>">
                        <?php echo($quote->quote_number ? $quote->quote_number : $quote->quote_id); ?>
                    </a>
                </td>
                <td>
                    <?php echo date_from_mysql($quote->quote_date_created); ?>
                </td>
                <td>
                    <?php echo date_from_mysql($quote->quote_date_expires); ?>
                </td>
                <td>
                    <a href="<?php echo site_url('clients/view/' . $quote->client_id); ?>"
                       title="<?php _trans('view_client'); ?>">
                        <?php _htmlsc(format_client($quote)); ?>
                    </a>
                </td>
                <td style="text-align: right; padding-right: 25px;">
                    <?php echo format_currency($quote->quote_total); ?>
                </td>
                <td>
                    <div class="options btn-group<?php echo $dropup ? ' dropup' : ''; ?>">
                        <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"
                           href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('quotes/view/' . $quote->quote_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('quotes/generate_pdf/' . $quote->quote_id); ?>"
                                   target="_blank">
                                    <i class="fa fa-print fa-margin"></i> <?php _trans('download_pdf'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                                    <i class="fa fa-send fa-margin"></i> <?php _trans('send_email'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('quotes/delete/' . $quote->quote_id); ?>"
                                   onclick="return confirm('<?php _trans('delete_quote_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php
            $quote_idx++;
        } ?>
        </tbody>

    </table>
</div>
