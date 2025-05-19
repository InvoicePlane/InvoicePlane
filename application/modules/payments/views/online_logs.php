<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('payment_logs'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('payments/online_logs'), 'mdl_payment_logs'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <?php $this->layout->load_view('payments/partial_online_logs_table'); ?>
    </div>

</div>
