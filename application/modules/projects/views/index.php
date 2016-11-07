<div id="headerbar">
    <h1><?php echo trans('projects'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('projects/form'); ?>"><i
                class="fa fa-plus"></i> <?php echo trans('new'); ?></a>
    </div>

    <div class="pull-right">
        <?php echo pager(site_url('projects/index'), 'mdl_projects'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo trans('project_name'); ?></th>
                <th><?php echo trans('client_name'); ?></th>
                <th><?php echo trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($projects as $project) { ?>
                <tr>
                    <td><?php echo $project->project_name; ?></td>
                    <td><?php echo ($project->client_id) ? $project->client_name : trans('none'); ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php echo trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('projects/form/' . $project->project_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php echo trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('projects/delete/' . $project->project_id); ?>"
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