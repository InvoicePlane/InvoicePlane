<div id="headerbar">

    <h1><?php echo lang('quotes'); ?></h1>

    <div class="pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> <?php echo lang('submenu'); ?>
        </button>
        <a class="create-quote btn btn-sm btn-primary" href="#">
            <i class="fa fa-plus"></i> <?php echo lang('new'); ?>
        </a>
    </div>

    <div class="pull-right visible-lg">
        <?php echo pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes'); ?>
    </div>

    <div class="pull-right visible-lg">
        <ul class="nav nav-pills index-options">
            <li <?php if ($status == 'all') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/all'); ?>"><?php echo lang('all'); ?></a></li>
            <li <?php if ($status == 'draft') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/draft'); ?>"><?php echo lang('draft'); ?></a></li>
            <li <?php if ($status == 'sent') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/sent'); ?>"><?php echo lang('sent'); ?></a></li>
            <li <?php if ($status == 'viewed') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/viewed'); ?>"><?php echo lang('viewed'); ?></a></li>
            <li <?php if ($status == 'approved') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/approved'); ?>"><?php echo lang('approved'); ?></a></li>
            <li <?php if ($status == 'rejected') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/rejected'); ?>"><?php echo lang('rejected'); ?></a></li>
            <li <?php if ($status == 'canceled') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/canceled'); ?>"><?php echo lang('canceled'); ?></a></li>
        </ul>
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            <?php echo pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes'); ?>
        </div>

        <div class="submenu-row">
            <ul class="nav nav-pills index-options">
                <li <?php if ($status == 'all') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/all'); ?>"><?php echo lang('all'); ?></a></li>
                <li <?php if ($status == 'draft') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/draft'); ?>"><?php echo lang('draft'); ?></a></li>
                <li <?php if ($status == 'sent') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/sent'); ?>"><?php echo lang('sent'); ?></a></li>
                <li <?php if ($status == 'viewed') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/viewed'); ?>"><?php echo lang('viewed'); ?></a></li>
                <li <?php if ($status == 'approved') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/approved'); ?>"><?php echo lang('approved'); ?></a>
                </li>
                <li <?php if ($status == 'rejected') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/rejected'); ?>"><?php echo lang('rejected'); ?></a>
                </li>
                <li <?php if ($status == 'canceled') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/canceled'); ?>"><?php echo lang('canceled'); ?></a>
                </li>
            </ul>
        </div>

    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
        <?php $this->layout->load_view('quotes/partial_quote_table', array('quotes' => $quotes)); ?>
    </div>

</div>