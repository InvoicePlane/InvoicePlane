<div id="headerbar">

    <h1><?php echo trans('invoices'); ?></h1>

    <div class="pull-right">
        <a class="create-invoice btn btn-sm btn-primary" href="#">
            <i class="fa fa-plus"></i> <?php echo trans('new'); ?>
        </a>
    </div>

    <div class="pull-right">
        <?php echo pager(site_url('invoices/client/' . $client_id . '/' . $status), 'mdl_invoices'); ?>
    </div>

    <div class="pull-right">
        <ul class="nav nav-pills index-options">
            <li <?php if ($status == 'open') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/client/' . $client_id . '/open'); ?>"><?php echo trans('open'); ?></a>
            </li>
            <li <?php if ($status == 'closed') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/client/' . $client_id . '/closed'); ?>"><?php echo trans('closed'); ?></a>
            </li>
            <li <?php if ($status == 'overdue') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('invoices/client/' . $client_id . '/overdue'); ?>"><?php echo trans('overdue'); ?></a>
            </li>
        </ul>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('invoices/partial_invoice_table', array('invoices' => $invoices)); ?>

</div>