<div id="headerbar">
    <h1 class="headerbar-title"><?php echo trans('sales_by_client'); ?></h1>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div id="report_options" class="panel panel-default">

                <div class="panel-heading">
                    <i class="fa fa-print"></i>
                    <?php echo trans('report_options'); ?>
                </div>

                <div class="panel-body">

                    <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

                        <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

                        <div class="form-group has-feedback">
                            <label for="from_date">
                                <?php echo trans('from_date'); ?>
                            </label>

                            <div class="input-group">
                                <input name="from_date" id="from_date" class="form-control datepicker">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="to_date">
                                <?php echo trans('to_date'); ?>
                            </label>

                            <div class="input-group">
                                <input name="to_date" id="to_date" class="form-control datepicker">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <input type="submit" class="btn btn-success" name="btn_submit"
                               value="<?php echo trans('run_report'); ?>">

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>
