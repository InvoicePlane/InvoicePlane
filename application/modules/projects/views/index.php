<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('projects'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('projects/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('projects/index'), 'mdl_projects'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php _trans('project_name'); ?></th>
                <th><?php _trans('client_name'); ?></th>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($projects as $project) { ?>
                <tr>
                    <td><?php echo anchor('projects/view/' . $project->project_id, htmlsc($project->project_name)); ?></td>
                    <td><?php echo ($project->client_id) ? htmlsc($project->client_name) : trans('none'); ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('projects/form/' . $project->project_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('projects/delete/' . $project->project_id); ?>"
                                       onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
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
