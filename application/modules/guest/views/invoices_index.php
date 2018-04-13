<div id="headerbar">

    <h1 class="headerbar-title"><?php _trans('invoices'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('guest/invoices/status/' . $this->uri->segment(4)), 'mdl_invoices'); ?>
    </div>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm index-options">
            <a href="<?php echo site_url('guest/invoices/status/open'); ?>"
               class="btn <?php echo $status == 'open' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('open'); ?>
            </a>
            <a href="<?php echo site_url('guest/invoices/status/paid'); ?>"
               class="btn  <?php echo $status == 'paid' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('paid'); ?>
            </a>
        </div>
    </div>

</div>


<div id="content" class="table-content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>
    
    <div id="filter_results">
        <?php $this->layout->load_view('guest/invoices_partial_table', array('invoices' => $invoices)); ?>
        <?php echo pager_detailed('mdl_invoices'); ?>
    </div>
</div>
