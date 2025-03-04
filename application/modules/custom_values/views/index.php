<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('custom_values'); ?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default" href="<?php echo site_url('custom_fields'); ?>">
                <i class="fa fa-arrow-left"></i> <?php _trans('back'); ?>
            </a>
            <a class="btn btn-primary" href="<?php echo site_url('custom_fields/form'); ?>">
                <i class="fa fa-plus"></i> <?php _trans('new'); ?>
            </a>
        </div>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('custom_values/index'), 'mdl_custom_values'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
<?php
$this->layout->load_view('custom_values/partial_custom_values_table', ['custom_values' => $custom_values, 'custom_tables' => $custom_tables, 'positions' => $positions]);
?>
    </div>

</div>
