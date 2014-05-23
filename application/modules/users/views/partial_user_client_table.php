<table class="table table-striped no-margin">

    <thead>
        <tr>
            <th style="width: 90%;"><?php echo lang('client'); ?></th>
            <th style="width: 10%;"><?php echo lang('options'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($user_clients as $user_client) { ?>
        <tr>
            <td><?php echo $user_client->client_name; ?></td>
            <td>
                <?php if ($id) { ?>
                <a class="" href="<?php echo site_url('users/delete_user_client/' . $id . '/' . $user_client->user_client_id); ?>">
                    <i class="icon-remove"></i>
                </a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>

</table>