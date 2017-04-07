<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('invoice_aging'); ?></h1>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div id="report_options" class="panel panel-default">

                <div class="panel-heading">
                    <i class="fa fa-print"></i>
                    <?php _trans('report_options'); ?>
                </div>

                <div class="panel-body">
                    <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

                        <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

                        <input type="submit" class="btn btn-success"
                               name="btn_submit" value="<?php _trans('run_report'); ?>">

                    </form>
                </div>

            </div>

        </div>
    </div>

</div>
