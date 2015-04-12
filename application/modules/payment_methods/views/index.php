<div id="headerbar">
    <h1><?php echo lang('payment_methods'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('payment_methods/form'); ?>">
            <i class="fa fa-plus"></i> <?php echo lang('new'); ?>
        </a>
    </div>

    <div class="pull-right">
        <?php echo pager(site_url('payment_methods/index'), 'mdl_payment_methods'); ?>
    </div>

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo lang('payment_method'); ?></th>
                <th><?php echo lang('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($payment_methods as $payment_method) { ?>
                <tr>
                    <td><?php echo $payment_method->payment_method_name; ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i>
                                <?php echo lang('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('payment_methods/form/' . $payment_method->payment_method_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i>
                                        <?php echo lang('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('payment_methods/delete/' . $payment_method->payment_method_id); ?>"
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
</div>