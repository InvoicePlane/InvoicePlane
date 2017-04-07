<form method="post">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('assigned_clients'); ?></h1>

        <div class="headerbar-item pull-right">
            <div class="btn-group btn-group-sm">
                <a class="btn btn-default" href="<?php echo site_url('users'); ?>">
                    <i class="fa fa-arrow-left"></i> <?php _trans('back'); ?>
                </a>
                <a class="btn btn-primary" href="<?php echo site_url('user_clients/create/' . $id); ?>">
                    <i class="fa fa-plus"></i> <?php _trans('new'); ?>
                </a>
            </div>
        </div>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('user') . ': ' . htmlsc($user->user_name); ?>
                    </div>

                    <div class="panel-body table-content">
                        <div class="table-responsive no-margin">
                            <table class="table table-striped no-margin">

                                <thead>
                                <tr>
                                    <th><?php _trans('client'); ?></th>
                                    <th><?php _trans('options'); ?></th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($user_clients as $user_client) { ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo site_url('clients/view/' . $user_client->client_id); ?>">
                                                <?php _htmlsc(format_client($user_client)); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="options btn-group btn-group-sm">
                                                <a href="<?php echo site_url('user_clients/delete/' . $user_client->user_client_id); ?>"
                                                   onclick="return confirm('<?php _trans('delete_record_warning'); ?>');"
                                                   class="btn btn-default">
                                                    <i class="fa fa-trash-o fa-margin"></i> <?php _trans('remove'); ?>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</form>
