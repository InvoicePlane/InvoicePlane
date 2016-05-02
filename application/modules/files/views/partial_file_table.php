<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo lang('file_name'); ?></th>
                <th><?php echo lang('client_name'); ?></th>
                <th><?php echo lang('date_uploaded'); ?></th>
                <th><?php echo lang('options'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $file) : ?>
            <tr>
                <td><?php echo anchor('files/view/' . $file->file_id, $file->file_name); ?></td>
                <td><?php echo $file->client_name; ?></td>
                <td><?php echo $file->file_date_created; ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php echo lang('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('files/download/' . $file->file_id); ?>">
                                    <i class="fa fa-download fa-margin"></i> <?php echo lang('download'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('files/delete/' . $file->file_id); ?>"
                                   onclick="return confirm('<?php echo lang('delete_file_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
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
