<div class="headerbar">

	<h1><?php echo lang('quotes'); ?></h1>

	<div class="pull-right">
		<?php echo pager(site_url('guest/quotes/status/' . $this->uri->segment(3)), 'mdl_quotes'); ?>
	</div>
    
	<div class="pull-right">
		<ul class="nav nav-pills index-options">
			<li <?php if ($status == 'open') { ?>class="active"<?php } ?>><a href="<?php echo site_url('guest/quotes/status/open'); ?>"><?php echo lang('open'); ?></a></li>
			<li <?php if ($status == 'approved') { ?>class="active"<?php } ?>><a href="<?php echo site_url('guest/quotes/status/approved'); ?>"><?php echo lang('approved'); ?></a></li>
			<li <?php if ($status == 'rejected') { ?>class="active"<?php } ?>><a href="<?php echo site_url('guest/quotes/status/rejected'); ?>"><?php echo lang('rejected'); ?></a></li>
		</ul>
	</div>

</div>

<div class="table-content">

    <div id="filter_results">
        <?php echo $this->layout->load_view('layout/alerts'); ?>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th><?php echo lang('quote'); ?></th>
                    <th><?php echo lang('created'); ?></th>
                    <th><?php echo lang('due_date'); ?></th>
                    <th><?php echo lang('client_name'); ?></th>
                    <th><?php echo lang('amount'); ?></th>
                    <th><?php echo lang('options'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($quotes as $quote) { ?>
                <tr>
                    <td><a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>" title="<?php echo lang('edit'); ?>"><?php echo $quote->quote_number; ?></a></td>
                    <td><?php echo date_from_mysql($quote->quote_date_created); ?></td>
                    <td><?php echo date_from_mysql($quote->quote_date_expires); ?></td>
                    <td><?php echo $quote->client_name; ?></td>
                    <td><?php echo format_currency($quote->quote_total); ?></td>
                    <td>
                        <a href="<?php echo site_url('guest/quotes/view/' . $quote->quote_id); ?>" class="btn btn-small">
                            <i class="icon-search"></i> <?php echo lang('view'); ?>
                        </a>

                        <a href="<?php echo site_url('guest/quotes/generate_pdf/' . $quote->quote_id); ?>" class="btn btn-small">
                            <i class="icon-print"></i> <?php echo lang('pdf'); ?>
                        </a>
                        <?php if (in_array($quote->quote_status_id, array(2,3))) { ?>
                        <a href="<?php echo site_url('guest/quotes/approve/' . $quote->quote_id); ?>" class="btn btn-success"><i class="icon-white icon-check"></i> <?php echo lang('approve'); ?></a>
                        <a href="<?php echo site_url('guest/quotes/reject/' . $quote->quote_id); ?>" class="btn btn-danger"><i class="icon-white icon-ban-circle"></i> <?php echo lang('reject'); ?></a>
                        <?php } elseif ($quote->quote_status_id == 4) { ?>
                        <a href="#" class="btn btn-success"><?php echo lang('approved'); ?></a>
                        <?php } elseif ($quote->quote_status_id == 5) { ?>
                        <a href="#" class="btn btn-danger"><?php echo lang('rejected'); ?></a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>

        </table>
    </div>

</div>