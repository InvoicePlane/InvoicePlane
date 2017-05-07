<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('sales_by_date'); ?></h1>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div id="report_options" class="panel panel-default">

                <div class="panel-heading">
                    <i class="fa fa-print fa-margin"></i>
                    <?php _trans('report_options'); ?>
                </div>

                <div class="panel-body">

                    <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>"
                        <?php echo get_setting('reports_in_new_tab', false) ? 'target="_blank"' : ''; ?>>

                        <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

                        <div class="form-group has-feedback">
                            <label for="from_date">
                                <?php _trans('from_date'); ?>
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
                                <?php _trans('to_date'); ?>
                            </label>

                            <div class="input-group">
                                <input name="to_date" id="to_date" class="form-control datepicker">
                                <span class="input-group-addon">
                            <i class="fa fa-calendar fa-fw"></i>
                        </span>
                            </div>
                        </div>


                        <div class="clearfix">
                            <div class="col-xs-12 col-md-2" style="margin-right:10px; padding-left:0px;">
                                <label for="minQuantity">
                                    <?php _trans('min_quantity'); ?>
                                </label>

                                <div>
                                    <input type="number" id="minQuantity" name="minQuantity" min="0"
                                           class="form-control">
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-2" style=padding-left:0px;>
                                <label for="maxQuantity">
                                    <?php _trans('max_quantity'); ?>
                                </label>

                                <div>
                                    <input type="number" id="maxQuantity" name="maxQuantity" min="0"
                                           class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <label for="checkboxTax">
                                    <input type="checkbox" id="checkboxTax" name="checkboxTax">
                                    <?php _trans('values_with_taxes'); ?>
                                </label>
                            </div>
                        </div>

                        <input type="submit" class="btn btn-success" name="btn_submit"
                               value="<?php _trans('run_report'); ?>">

                    </form>
                </div>
            </div>

        </div>
