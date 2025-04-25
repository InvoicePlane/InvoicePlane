    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th><?php _trans('family_name'); ?></th>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
<?php
foreach ($families as $family)
{
?>
                <tr>
                    <td><?php _htmlsc($family->family_name); ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('families/form/' . $family->family_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <form action="<?php echo site_url('families/delete/' . $family->family_id); ?>"
                                          method="POST">
                                        <?php _csrf_field(); ?>
                                        <button type="submit" class="dropdown-button"
                                                onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
<?php
}
?>
            </tbody>

        </table>
    </div>
