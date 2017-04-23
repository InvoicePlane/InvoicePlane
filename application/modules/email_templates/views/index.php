<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('email_templates'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('email_templates/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('email_templates/index'), 'mdl_email_templates'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <table class="table table-striped">

        <thead>
        <tr>
            <th><?php _trans('title'); ?></th>
            <th><?php _trans('type'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($email_templates as $email_template) { ?>
            <tr>
                <td><?php _htmlsc($email_template->email_template_title); ?></td>
                <td><?php echo lang($email_template->email_template_type); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><i
                                    class="fa fa-cog"></i> <?php _trans('options'); ?></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('email_templates/form/' . $email_template->email_template_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('email_templates/delete/' . $email_template->email_template_id); ?>"
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
