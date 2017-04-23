<div id="headerbar">

    <h1 class="headerbar-title"><?php _trans('invoice_archive'); ?></h1>

    <div class="headerbar-item pull-right">
        <form action="<?php echo site_url('invoices/archive/'); ?>" method="post">
            <div class="input-group">
                <input name="invoice_number" id="invoice_number" type="text" class="form-control input-sm">
                <span class="input-group-btn">
                    <button class="btn btn-default btn-sm"
                            type="submit"><?php _trans('filter_invoices'); ?></button>
                </span>
            </div>
        </form>
    </div>

</div
<div id="content" class="table-content">

    <div id="filter_results">
        <?php $this->layout->load_view('invoices/partial_invoice_archive', array('invoices_archive' => $invoices_archive)); ?>
    </div>

</div>
