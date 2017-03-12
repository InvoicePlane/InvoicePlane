<div id="headerbar">
    <h1 class="headerbar-title"><?php echo trans('custom_fields'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('custom_fields/form'); ?>">
            <i class="fa fa-plus"></i> <?php echo trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('custom_fields/index'), 'mdl_custom_fields'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php echo trans('table'); ?></th>
            <th><?php echo trans('label'); ?></th>
            <th><?php echo trans('type'); ?></th>
            <th><?php echo trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($custom_fields as $custom_field) { ?>
            <?php $alpha = str_replace("-", "_", strtolower($custom_field->custom_field_type)); ?>
            <tr>
                <td><?php echo lang($custom_tables[$custom_field->custom_field_table]); ?></td>
                <td><?php echo $custom_field->custom_field_label; ?></td>
                <td><?php echo trans($alpha); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php echo trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (in_array($custom_field->custom_field_type, $custom_value_fields)) { ?>
                                <li>
                                    <a href="<?php echo site_url('custom_values/field/' . $custom_field->custom_field_id); ?>">
                                        <i class="fa fa-list fa-margin"></i> <?php echo trans('values'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="<?php echo site_url('custom_fields/form/' . $custom_field->custom_field_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php echo trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('custom_fields/delete/' . $custom_field->custom_field_id); ?>"
                                   onclick="return confirm('<?php echo trans('delete_record_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php echo trans('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>

    </table>

</div>
