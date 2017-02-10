<div id="headerbar">
    <h1><?php echo trans('units'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('units/form'); ?>"><i
                    class="fa fa-plus"></i> <?php echo trans('new'); ?></a>
    </div>

    <div class="pull-right">
        <?php echo pager(site_url('units/index'), 'mdl_units'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo trans('unit_name'); ?></th>
                <th><?php echo trans('unit_name_plrl'); ?></th>
                <th><?php echo trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($units as $unit) { ?>
                <tr>
                    <td><?php echo $unit->unit_name; ?></td>
                    <td><?php echo $unit->unit_name_plrl; ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php echo trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('units/form/' . $unit->unit_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php echo trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('units/delete/' . $unit->unit_id); ?>"
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

</div>
