<div class="table-responsive">
    <table class="table table-striped no-margin">

        <thead>
        <tr>
            <th><?php echo trans('client'); ?></th>
            <th><?php echo trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($user_clients as $user_client) { ?>
            <tr>
                <td><?php echo $user_client->client_name; ?></td>
                <td>
                    <?php if ($id) { ?>
                        <a class=""
                           href="<?php echo site_url('users/delete_user_client/' . $id . '/' . $user_client->user_client_id); ?>">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>

    </table>
</div>