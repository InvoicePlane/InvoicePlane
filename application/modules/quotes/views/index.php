<div id="headerbar">

    <h1 class="headerbar-title"><?php echo trans('quotes'); ?></h1>

    <div class="headerbar-item pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> <?php echo trans('submenu'); ?>
        </button>
        <a class="create-quote btn btn-sm btn-primary" href="#">
            <i class="fa fa-plus"></i> <?php echo trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <?php echo pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes'); ?>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <div class="btn-group btn-group-sm index-options">
            <a href="<?php echo site_url('quotes/status/all'); ?>"
               class="btn <?php echo $status == 'all' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('all'); ?>
            </a>
            <a href="<?php echo site_url('quotes/status/draft'); ?>"
               class="btn <?php echo $status == 'draft' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('draft'); ?>
            </a>
            <a href="<?php echo site_url('quotes/status/sent'); ?>"
               class="btn <?php echo $status == 'sent' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('sent'); ?>
            </a>
            <a href="<?php echo site_url('quotes/status/viewed'); ?>"
               class="btn <?php echo $status == 'viewed' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('viewed'); ?>
            </a>
            <a href="<?php echo site_url('quotes/status/approved'); ?>"
               class="btn <?php echo $status == 'approved' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('approved'); ?>
            </a>
            <a href="<?php echo site_url('quotes/status/rejected'); ?>"
               class="btn <?php echo $status == 'rejected' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('rejected'); ?>
            </a>
            <a href="<?php echo site_url('quotes/status/canceled'); ?>"
               class="btn <?php echo $status == 'canceled' ? 'btn-primary' : 'btn-default' ?>">
                <?php echo trans('canceled'); ?>
            </a>
        </div>
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            <?php echo pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes'); ?>
        </div>

        <div class="submenu-row">
            <div class="btn-group btn-group-sm index-options">
                <a href="<?php echo site_url('quotes/status/all'); ?>"
                   class="btn <?php echo $status == 'all' ? 'btn-primary' : 'btn-default' ?>">
                    <?php echo trans('all'); ?>
                </a>
                <a href="<?php echo site_url('quotes/status/draft'); ?>"
                   class="btn <?php echo $status == 'draft' ? 'btn-primary' : 'btn-default' ?>">
                    <?php echo trans('draft'); ?>
                </a>
                <a href="<?php echo site_url('quotes/status/sent'); ?>"
                   class="btn <?php echo $status == 'sent' ? 'btn-primary' : 'btn-default' ?>">
                    <?php echo trans('sent'); ?>
                </a>
                <a href="<?php echo site_url('quotes/status/viewed'); ?>"
                   class="btn <?php echo $status == 'viewed' ? 'btn-primary' : 'btn-default' ?>">
                    <?php echo trans('viewed'); ?>
                </a>
                <a href="<?php echo site_url('quotes/status/approved'); ?>"
                   class="btn <?php echo $status == 'approved' ? 'btn-primary' : 'btn-default' ?>">
                    <?php echo trans('approved'); ?>
                </a>
                <a href="<?php echo site_url('quotes/status/rejected'); ?>"
                   class="btn <?php echo $status == 'rejected' ? 'btn-primary' : 'btn-default' ?>">
                    <?php echo trans('rejected'); ?>
                </a>
                <a href="<?php echo site_url('quotes/status/canceled'); ?>"
                   class="btn <?php echo $status == 'canceled' ? 'btn-primary' : 'btn-default' ?>">
                    <?php echo trans('canceled'); ?>
                </a>
            </div>
        </div>

    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
        <?php $this->layout->load_view('quotes/partial_quote_table', array('quotes' => $quotes)); ?>
    </div>

</div>
