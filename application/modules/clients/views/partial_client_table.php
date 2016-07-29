<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?php echo trans('client_name'); ?></th>
            <th><?php echo trans('email_address'); ?></th>
            <th><?php echo trans('phone_number'); ?></th>
            <th style="text-align: right;"><?php echo trans('balance'); ?></th>
            <th><?php echo trans('active'); ?></th>
            <th><?php echo trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $client) : ?>
            <tr>
                <td><?php echo anchor('clients/view/' . $client->client_id, $client->client_name); ?></td>
                <td><?php echo $client->client_email; ?></td>
                <td><?php echo(($client->client_phone ? $client->client_phone : ($client->client_mobile ? $client->client_mobile : ''))); ?></td>
                <td style="text-align: right;"><?php echo format_currency($client->client_invoice_balance); ?></td>
                <td><?php echo ($client->client_active) ? trans('yes') : lang('no'); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php echo trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('clients/view/' . $client->client_id); ?>">
                                    <i class="fa fa-eye fa-margin"></i> <?php echo trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/form/' . $client->client_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php echo trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-quote"
                                   data-client-name="<?php echo $client->client_name; ?>">
                                    <i class="fa fa-file fa-margin"></i> <?php echo trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-invoice"
                                   data-client-name="<?php echo $client->client_name; ?>">
                                    <i class="fa fa-file-text fa-margin"></i> <?php echo trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/delete/' . $client->client_id); ?>"
                                   onclick="return confirm('<?php echo trans('delete_client_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php echo trans('delete'); ?>
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