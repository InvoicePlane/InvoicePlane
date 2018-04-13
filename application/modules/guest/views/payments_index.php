<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('payments'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('guest/payments/index'), 'mdl_payments'); ?>
    </div>

</div>

<div id="content" class="table-content">
    <div id="filter_results">
        <?php echo $this->layout->load_view('layout/alerts'); ?>
        <?php $this->layout->load_view('guest/payments_partial_table', array('payments' => $payments)); ?>
        <?php echo pager_detailed('mdl_payments'); ?>
    </div>
</div>
