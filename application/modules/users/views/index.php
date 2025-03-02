<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('users'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('users/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('users/index'), 'mdl_users'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <?php $this->layout->load_view('users/partial_users_table',
            [
                'users'      => $users,
                'user_types' => $user_types,
            ]
        ); ?>
    </div>

</div>
