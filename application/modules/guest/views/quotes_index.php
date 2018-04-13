<div id="headerbar">

    <h1 class="headerbar-title"><?php _trans('quotes'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('guest/quotes/status/' . $this->uri->segment(3)), 'mdl_quotes'); ?>
    </div>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm index-options">
            <a href="<?php echo site_url('guest/quotes/status/open'); ?>"
               class="btn <?php echo $status == 'open' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('open'); ?>
            </a>
            <a href="<?php echo site_url('guest/quotes/status/approved'); ?>"
               class="btn  <?php echo $status == 'approved' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('approved'); ?>
            </a>
            <a href="<?php echo site_url('guest/quotes/status/rejected'); ?>"
               class="btn  <?php echo $status == 'rejected' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('rejected'); ?>
            </a>
        </div>
    </div>

</div>

<div id="content" class="table-content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>
    
    <div id="filter_results">
        <?php $this->layout->load_view('guest/quotes_partial_table', array('quotes' => $quotes)); ?>
        <?php echo pager_detailed('mdl_quotes'); ?>
    </div>
</div>
