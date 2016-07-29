<div id="headerbar">

    <h1><?php echo trans('quotes'); ?></h1>

    <div class="pull-right">
        <?php echo pager(site_url('guest/quotes/status/' . $this->uri->segment(3)), 'mdl_quotes'); ?>
    </div>

    <div class="pull-right">
        <ul class="nav nav-pills index-options">
            <li <?php if ($status == 'open') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('guest/quotes/status/open'); ?>"><?php echo trans('open'); ?></a></li>
            <li <?php if ($status == 'approved') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('guest/quotes/status/approved'); ?>"><?php echo trans('approved'); ?></a>
            </li>
            <li <?php if ($status == 'rejected') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('guest/quotes/status/rejected'); ?>"><?php echo trans('rejected'); ?></a>
            </li>
        </ul>
    </div>

</div>

<div id="content" class="table-content">

    <div id="filter_results">
        <?php echo $this->layout->load_view('layout/alerts'); ?>

        <div class="table-responsive">
            <table class="table table-striped">

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
                            <a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                               class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-search"></i>
                                <?php echo trans('view'); ?>
                            </a>

                            <a href="<?php echo site_url('guest/quotes/generate_pdf/' . $quote->quote_id); ?>"
                               class="btn btn-default btn-sm">
                                <i class="icon ion-printer"></i>
                                <?php echo trans('pdf'); ?>
                            </a>
                            <?php if (in_array($quote->quote_status_id, array(2, 3))) { ?>
                                <a href="<?php echo site_url('guest/quotes/approve/' . $quote->quote_id); ?>"
                                   class="btn btn-success btn-sm">
                                    <i class="glyphicon glyphicon-check"></i>
                                    <?php echo trans('approve'); ?>
                                </a>
                                <a href="<?php echo site_url('guest/quotes/reject/' . $quote->quote_id); ?>"
                                   class="btn btn-danger btn-sm">
                                    <i class="glyphicon glyphicon-ban-circle"></i>
                                    <?php echo trans('reject'); ?>
                                </a>
                            <?php } elseif ($quote->quote_status_id == 4) { ?>
                                <a href="#" class="btn btn-success btn-sm disabled"><?php echo trans('approved'); ?></a>
                            <?php } elseif ($quote->quote_status_id == 5) { ?>
                                <a href="#" class="btn btn-danger btn-sm disabled"><?php echo trans('rejected'); ?></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</div>