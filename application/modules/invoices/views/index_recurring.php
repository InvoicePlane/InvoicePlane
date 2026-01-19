<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('recurring_invoices'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('invoices/recurring/index'), 'mdl_invoices_recurring'); ?>
    </div>
</div>

<div id="content" class="table-content">
    <div id="filter_results">
        <?php $this->layout->load_view('invoices/partial_invoices_recurring_table'); ?>
    </div>
</div>
