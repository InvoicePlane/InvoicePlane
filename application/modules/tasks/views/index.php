<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('tasks'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('tasks/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('tasks/index'), 'mdl_tasks'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <?php $this->layout->load_view('tasks/partial_tasks_table'); ?>
    </div>

</div>
