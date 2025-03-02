<?php
$active = $this->uri->segment(3);
?>
<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('custom_fields'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('custom_fields/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('custom_fields/table/' . $active), 'mdl_custom_fields'); ?>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <div class="btn-group btn-group-sm index-options">
            <a href="<?php echo site_url('custom_fields/table/all'); ?>"
               class="btn <?php echo $active == 'all' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('all'); ?>
            </a>
<?php
foreach ($custom_tables as $table)
{
?>
            <a href="<?php echo site_url('custom_fields/table/' . $table); ?>"
               class="btn <?php echo $active == $table ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans($table); ?>
            </a>
<?php
}
?>
        </div>
    </div>
</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <?php $this->layout->load_view('custom_fields/partial_custom_fields_table',
            [
                'custom_fields' => $custom_fields,
                'custom_tables' => $custom_tables,
                'custom_value_fields' => $custom_value_fields,
            ]
        ); ?>
    </div>

</div>
