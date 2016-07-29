<div id="headerbar">

    <h1><?php echo trans('invoices'); ?></h1>

    <div class="pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> <?php echo trans('submenu'); ?>
        </button>
        <a class="create-invoice btn btn-sm btn-primary" href="#">
            <i class="fa fa-plus"></i> <?php echo trans('new'); ?>
        </a>
    </div>

    <div class="pull-right visible-lg">
        <?php echo pager(site_url('invoices/status/' . $this->uri->segment(3)), 'mdl_invoices'); ?>
    </div>

    <div class="pull-right visible-lg">
        <ul class="nav nav-pills index-options">
            <li <?php if ($status == 'all') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/status/all'); ?>"><?php echo trans('all'); ?></a></li>
            <li <?php if ($status == 'draft') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/status/draft'); ?>"><?php echo trans('draft'); ?></a></li>
            <li <?php if ($status == 'sent') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/status/sent'); ?>"><?php echo trans('sent'); ?></a></li>
            <li <?php if ($status == 'viewed') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/status/viewed'); ?>"><?php echo trans('viewed'); ?></a></li>
            <li <?php if ($status == 'paid') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/status/paid'); ?>"><?php echo trans('paid'); ?></a></li>
            <li <?php if ($status == 'overdue') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/status/overdue'); ?>"><?php echo trans('overdue'); ?></a></li>
        </ul>
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            <?php echo pager(site_url('invoices/status/' . $this->uri->segment(3)), 'mdl_invoices'); ?>
        </div>

        <div class="submenu-row">
            <ul class="nav nav-pills index-options">
                <li <?php if ($status == 'all') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('invoices/status/all'); ?>"><?php echo trans('all'); ?></a></li>
                <li <?php if ($status == 'draft') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('invoices/status/draft'); ?>"><?php echo trans('draft'); ?></a></li>
                <li <?php if ($status == 'sent') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('invoices/status/sent'); ?>"><?php echo trans('sent'); ?></a></li>
                <li <?php if ($status == 'viewed') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('invoices/status/viewed'); ?>"><?php echo trans('viewed'); ?></a></li>
                <li <?php if ($status == 'paid') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('invoices/status/paid'); ?>"><?php echo trans('paid'); ?></a></li>
                <li <?php if ($status == 'overdue') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('invoices/status/overdue'); ?>"><?php echo trans('overdue'); ?></a>
                </li>
            </ul>
        </div>

    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
        <?php $this->layout->load_view('invoices/partial_invoice_table', array('invoices' => $invoices)); ?>
    </div>

</div>