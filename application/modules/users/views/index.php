<div id="headerbar">
    <h1 class="headerbar-title"><?php echo trans('users'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('users/form'); ?>">
            <i class="fa fa-plus"></i> <?php echo trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('users/index'), 'mdl_users'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo trans('name'); ?></th>
                <th><?php echo trans('user_type'); ?></th>
                <th><?php echo trans('email_address'); ?></th>
                <th><?php echo trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($users as $user) { ?>
                <tr>
                    <td><?php _htmlsc($user->user_name); ?></td>
                    <td><?php echo $user_types[$user->user_type]; ?></td>
                    <td><?php echo $user->user_email; ?></td>
                    <td>
                        <div class="options btn-group btn-group-sm ">
                            <a class="btn btn-default dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php echo trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('users/form/' . $user->user_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php echo trans('edit'); ?>
                                    </a>
                                </li>
                                <?php if ($user->user_id <> 1) { ?>
                                    <li>
                                        <a href="<?php echo site_url('users/delete/' . $user->user_id); ?>"
                                           onclick="return confirm('<?php echo trans('delete_record_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php echo trans('delete'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>

</div>
