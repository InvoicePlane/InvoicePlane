<div class="table-responsive">
    <table class="table table-striped">

        <thead>
        <tr>
        	<th><?php echo lang('car_id'); ?></th>
            <th><?php echo lang('car_date_modified'); ?></th>
            <th><?php echo lang('client'); ?></th>
            <th><?php echo lang('car_vehicle'); ?></th>
            <th><?php echo lang('car_builddate'); ?></th>
            <th><?php echo lang('car_licenseplate'); ?></th>
            <th><?php echo lang('car_kmstand'); ?></th>
            <th><?php echo lang('car_auhu'); ?></th>
            <th><?php echo lang('note'); ?></th>
            <th><?php echo lang('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($cars as $car) { ?>
            <tr>
            	<td><?php echo $car->car_id; ?>
                <td><?php echo date_from_mysql($car->car_date_modified); ?></td>
                <td>
                    <a href="<?php echo site_url('clients/view/' . $car->client_id); ?>"
                       title="<?php echo lang('view_client'); ?>">
                        <?php echo $car->client_name; echo '&nbsp'; echo $car->client_surname; ?>
                    </a>
                </td>
                <td><?php echo $car->car_vehicle; ?></td>
                <td><?php echo date_from_mysql($car->car_builddate); ?></td>
                <td><?php echo $car->car_licenseplate; ?></td>
                <td><?php echo $car->car_kmstand; ?></td>
                <td><?php echo date_from_mysql($car->car_auhu); ?></td>
                <td><?php echo $car->car_note; ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php echo lang('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('cars/form/' . $car->car_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i>
                                    <?php echo lang('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('cars/remove/' . $car->car_id); ?>"
                                   onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i>
                                    <?php echo lang('delete'); ?>
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
