<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?php _trans('active'); ?></th>
            <th><?php _trans('client_name'); ?></th>
            <th><?php _trans('email_address'); ?></th>
            <th><?php _trans('phone_number'); ?></th>
            <th class="amount"><?php _trans('balance'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $client) : ?>
            <tr>
                <td><?php echo ($client->client_active) ? trans('yes') : trans('no'); ?></td>
                <td><?php echo anchor('clients/view/' . $client->client_id, htmlsc(format_client($client))); ?></td>
                <td><?php _htmlsc($client->client_email); ?></td>
                <td><?php _htmlsc($client->client_phone ? $client->client_phone : ($client->client_mobile ? $client->client_mobile : '')); ?></td>
                <td class="amount"><?php echo format_currency($client->client_invoice_balance); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('clients/view/' . $client->client_id); ?>">
                                    <i class="fa fa-eye fa-margin"></i> <?php _trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/form/' . $client->client_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-quote"
                                   data-client-id="<?php echo $client->client_id; ?>">
                                    <i class="fa fa-file fa-margin"></i> <?php _trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-invoice"
                                   data-client-id="<?php echo $client->client_id; ?>">
                                    <i class="fa fa-file-text fa-margin"></i> <?php _trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('clients/delete/' . $client->client_id); ?>"
                                   onclick="return confirm('<?php _trans('delete_client_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
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
