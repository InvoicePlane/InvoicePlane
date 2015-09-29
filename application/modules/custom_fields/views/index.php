<div id="headerbar">
    <h1><?php echo lang('custom_fields'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('custom_fields/form'); ?>">
            <i class="fa fa-plus"></i> <?php echo lang('new'); ?>
        </a>
    </div>

    <div class="pull-right">
        <?php echo pager(site_url('custom_fields/index'), 'mdl_custom_fields'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php echo lang('table'); ?></th>
            <th><?php echo lang('label'); ?></th>
            <th><?php echo lang('column'); ?></th>
            <th><?php echo lang('type'); ?></th>
            <th><?php echo lang('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($custom_fields as $custom_field) : ?>
            <tr>
                <td><?php echo $custom_field->custom_field_table; ?></td>
                <td><?php echo $custom_field->custom_field_label; ?></td>
                <td><?php echo $custom_field->custom_field_column; ?></td>
                <td><?php echo $custom_field->custom_field_type; ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php echo lang('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('custom_fields/form/' . $custom_field->custom_field_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php echo lang('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('custom_fields/delete/' . $custom_field->custom_field_id); ?>"
                                   onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>

</div>
