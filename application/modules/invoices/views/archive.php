<div id="headerbar">

    <h1><?php echo lang('invoice_archive'); ?></h1>

    <div class="col-lg-2">
        <div class="pull-right">
            <form action="<?php echo site_url('invoices/archive/'); ?>" method="post">
                <div class="input-group">
                    <input name="invoice_number" id="invoice_number" type="text" class="form-control">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit"><?php echo lang('filter_invoices'); ?></button>
                </span>
                </div>
            </form>
        </div>
    </div>

</div
<div id="content" class="table-content">

    <div id="filter_results">
        <?php $this->layout->load_view('invoices/partial_invoice_archive',
            array('invoices_archive' => $invoices_archive)); ?>
    </div>

</div>